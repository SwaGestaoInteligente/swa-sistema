<?php

namespace App\Http\Controllers;

use App\Models\Condominio;
use App\Models\ConflitoMorador;
use App\Models\Unidade;
use App\Models\Usuario;
use App\Services\AnexoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\View\View;

class ConflitoMoradorController extends Controller
{
    public function __construct(private readonly AnexoService $anexoService)
    {
    }

    public function index(Request $request, Condominio $condominio): View
    {
        $filters = $request->validate([
            'protocolo' => ['nullable', 'string', 'max:40'],
            'status' => ['nullable', Rule::in(array_keys($this->statusList()))],
            'tipo' => ['nullable', Rule::in(array_keys($this->tipos()))],
            'de' => ['nullable', 'date'],
            'ate' => ['nullable', 'date', 'after_or_equal:de'],
        ]);

        $query = ConflitoMorador::query()
            ->with(['moradorA:id,nome', 'moradorB:id,nome', 'unidade:id,numero', 'anexos'])
            ->where('condominio_id', $condominio->id);

        if (! empty($filters['protocolo'])) {
            $query->where('protocolo', 'like', '%'.$filters['protocolo'].'%');
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (! empty($filters['de'])) {
            $query->whereDate('ocorrido_em', '>=', $filters['de']);
        }

        if (! empty($filters['ate'])) {
            $query->whereDate('ocorrido_em', '<=', $filters['ate']);
        }

        return view('conflitos-moradores.index', [
            'condominio' => $condominio,
            'conflitos' => $query->orderByDesc('ocorrido_em')->paginate(15)->withQueryString(),
            'filters' => $filters,
            'tipos' => $this->tipos(),
            'statusList' => $this->statusList(),
        ]);
    }

    public function create(Condominio $condominio): View
    {
        return view('conflitos-moradores.create', [
            'condominio' => $condominio,
            'conflito' => new ConflitoMorador([
                'condominio_id' => $condominio->id,
                'protocolo' => $this->generateProtocol('CM'),
                'ocorrido_em' => now()->format('Y-m-d\TH:i'),
                'status' => 'em_analise',
            ]),
            'moradores' => $this->moradoresByCondominio((string) $condominio->id),
            'unidades' => $this->unidadesByCondominio((string) $condominio->id),
            'tipos' => $this->tipos(),
            'statusList' => $this->statusList(),
            'testemunhasText' => '',
        ]);
    }

    public function store(Request $request, Condominio $condominio): RedirectResponse
    {
        $data = $this->validatedData($request, $condominio);
        $data['registrado_por'] = auth()->user()?->name ?? auth()->user()?->email;
        $data['condominio_id'] = $condominio->id;

        $conflito = ConflitoMorador::query()->create($data);

        if ($request->hasFile('anexo')) {
            $this->anexoService->store(
                condominioId: $conflito->condominio_id,
                owner: $conflito,
                file: $request->file('anexo'),
                directory: "condominios/{$conflito->condominio_id}/conflitos/{$conflito->id}",
                uploadedBy: auth()->id()
            );
        }

        return redirect()
            ->route('condominios.context.conflitos.index', ['condominio' => $condominio])
            ->with('success', 'Conflito de moradores registrado com sucesso.');
    }

    public function edit(Condominio $condominio, ConflitoMorador $conflito): View
    {
        $this->assertSameCondominio($condominio, $conflito->condominio_id);

        $conflito->load('anexos');

        return view('conflitos-moradores.edit', [
            'condominio' => $condominio,
            'conflito' => $conflito,
            'moradores' => $this->moradoresByCondominio((string) $condominio->id),
            'unidades' => $this->unidadesByCondominio((string) $condominio->id),
            'tipos' => $this->tipos(),
            'statusList' => $this->statusList(),
            'testemunhasText' => $this->arrayToLines($conflito->testemunhas),
        ]);
    }

    public function update(Request $request, Condominio $condominio, ConflitoMorador $conflito): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $conflito->condominio_id);

        $data = $this->validatedData($request, $condominio, $conflito);
        $data['condominio_id'] = $condominio->id;
        $conflito->update($data);

        if ($request->hasFile('anexo')) {
            $this->anexoService->store(
                condominioId: $conflito->condominio_id,
                owner: $conflito,
                file: $request->file('anexo'),
                directory: "condominios/{$conflito->condominio_id}/conflitos/{$conflito->id}",
                uploadedBy: auth()->id()
            );
        }

        return redirect()
            ->route('condominios.context.conflitos.index', ['condominio' => $condominio])
            ->with('success', 'Conflito atualizado com sucesso.');
    }

    public function destroy(Condominio $condominio, ConflitoMorador $conflito): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $conflito->condominio_id);

        $conflito->delete();

        return redirect()
            ->route('condominios.context.conflitos.index', ['condominio' => $condominio])
            ->with('success', 'Conflito removido com sucesso.');
    }

    public function alterarStatus(Request $request, Condominio $condominio, ConflitoMorador $conflito): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $conflito->condominio_id);

        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys($this->statusList()))],
            'tratado_por' => ['nullable', 'string', 'max:120'],
        ]);

        $conflito->status = $data['status'];
        $conflito->tratado_por = $data['tratado_por'] ?: (auth()->user()?->name ?? auth()->user()?->email);
        $conflito->resolvido_em = in_array($data['status'], ['resolvido', 'judicial'], true) ? now() : null;
        $conflito->save();

        return back()->with('success', 'Status do conflito atualizado.');
    }

    private function validatedData(Request $request, Condominio $condominio, ?ConflitoMorador $conflito = null): array
    {
        $id = $conflito?->id;

        $validator = validator($request->all(), [
            'protocolo' => [
                'required',
                'string',
                'max:40',
                Rule::unique('conflitos_moradores', 'protocolo')
                    ->where(fn ($query) => $query->where('condominio_id', $condominio->id))
                    ->ignore($id),
            ],
            'ocorrido_em' => ['required', 'date'],
            'morador_a_id' => ['nullable', 'uuid', Rule::exists('usuarios', 'id')],
            'morador_a_nome' => ['nullable', 'string', 'max:150'],
            'morador_b_id' => ['nullable', 'uuid', Rule::exists('usuarios', 'id')],
            'morador_b_nome' => ['nullable', 'string', 'max:150'],
            'unidade_id' => ['nullable', 'uuid', Rule::exists('unidades', 'id')],
            'unidade' => ['nullable', 'string', 'max:40'],
            'tipo' => ['required', Rule::in(array_keys($this->tipos()))],
            'relato' => ['required', 'string'],
            'testemunhas_text' => ['nullable', 'string'],
            'tentativa_mediacao' => ['nullable', 'string'],
            'status' => ['required', Rule::in(array_keys($this->statusList()))],
            'tratado_por' => ['nullable', 'string', 'max:120'],
            'anexo' => array_merge(['nullable'], $this->anexoService->allowedUploadRules()),
        ]);

        $validator->after(function (Validator $validator) use ($request, $condominio): void {
            $condominioId = $condominio->id;
            $moradorAId = $request->input('morador_a_id');
            $moradorBId = $request->input('morador_b_id');
            $moradorANome = trim((string) $request->input('morador_a_nome'));
            $moradorBNome = trim((string) $request->input('morador_b_nome'));

            if (! $moradorAId && $moradorANome === '') {
                $validator->errors()->add('morador_a_id', 'Informe o morador A por cadastro ou nome.');
            }

            if (! $moradorBId && $moradorBNome === '') {
                $validator->errors()->add('morador_b_id', 'Informe o morador B por cadastro ou nome.');
            }

            if ($moradorAId && $moradorBId && $moradorAId === $moradorBId) {
                $validator->errors()->add('morador_b_id', 'Morador A e B não podem ser iguais.');
            }

            if (! $moradorAId && ! $moradorBId && $moradorANome !== '' && mb_strtolower($moradorANome) === mb_strtolower($moradorBNome)) {
                $validator->errors()->add('morador_b_nome', 'Morador A e B não podem ser iguais.');
            }

            if ($moradorAId) {
                $moradorA = Usuario::query()->find($moradorAId);
                if ($moradorA && $moradorA->condominio_id !== $condominioId) {
                    $validator->errors()->add('morador_a_id', 'Morador A não pertence ao condomínio selecionado.');
                }
            }

            if ($moradorBId) {
                $moradorB = Usuario::query()->find($moradorBId);
                if ($moradorB && $moradorB->condominio_id !== $condominioId) {
                    $validator->errors()->add('morador_b_id', 'Morador B não pertence ao condomínio selecionado.');
                }
            }

            if ($request->filled('unidade_id')) {
                $unidade = Unidade::query()->find($request->input('unidade_id'));
                if ($unidade && $unidade->condominio_id !== $condominioId) {
                    $validator->errors()->add('unidade_id', 'Unidade não pertence ao condomínio selecionado.');
                }
            }
        });

        $data = $validator->validate();
        $data['condominio_id'] = $condominio->id;

        $moradorA = ! empty($data['morador_a_id']) ? Usuario::query()->find($data['morador_a_id']) : null;
        $moradorB = ! empty($data['morador_b_id']) ? Usuario::query()->find($data['morador_b_id']) : null;

        if ($moradorA) {
            $data['morador_a_nome'] = $moradorA->nome;
        }

        if ($moradorB) {
            $data['morador_b_nome'] = $moradorB->nome;
        }

        $data['testemunhas'] = $this->linesToArray($data['testemunhas_text'] ?? null);
        unset($data['testemunhas_text']);

        if (in_array($data['status'], ['resolvido', 'judicial'], true)) {
            $data['resolvido_em'] = $conflito?->resolvido_em ?? now();
        } else {
            $data['resolvido_em'] = null;
        }

        return $data;
    }

    private function moradoresByCondominio(string $condominioId)
    {
        return Usuario::query()
            ->where('tipo', 'morador')
            ->where('condominio_id', $condominioId)
            ->orderBy('nome')
            ->get(['id', 'condominio_id', 'nome']);
    }

    private function unidadesByCondominio(string $condominioId)
    {
        return Unidade::query()
            ->where('condominio_id', $condominioId)
            ->orderBy('numero')
            ->get(['id', 'condominio_id', 'numero']);
    }

    private function linesToArray(?string $text): ?array
    {
        if (! $text) {
            return null;
        }

        $lines = collect(preg_split('/\r\n|\r|\n/', $text))
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->values()
            ->all();

        return count($lines) > 0 ? $lines : null;
    }

    private function arrayToLines($array): string
    {
        if (! is_array($array) || empty($array)) {
            return '';
        }

        return implode(PHP_EOL, $array);
    }

    private function generateProtocol(string $prefix): string
    {
        return $prefix.'-'.now()->format('Ymd-His').'-'.Str::upper(Str::random(4));
    }

    private function tipos(): array
    {
        return [
            'barulho' => 'Barulho',
            'ameaca' => 'Ameaça',
            'vaga' => 'Vaga',
            'ofensa' => 'Ofensa',
            'uso_indevido_area_comum' => 'Uso indevido de área comum',
            'outro' => 'Outro',
        ];
    }

    private function statusList(): array
    {
        return [
            'em_analise' => 'Em análise',
            'advertido' => 'Advertido',
            'resolvido' => 'Resolvido',
            'judicial' => 'Judicial',
        ];
    }

    private function assertSameCondominio(Condominio $condominio, string $resourceCondominioId): void
    {
        if ((string) $condominio->id !== (string) $resourceCondominioId) {
            abort(404);
        }
    }
}
