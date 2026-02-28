<?php

namespace App\Http\Controllers;

use App\Models\Condominio;
use App\Models\OcorrenciaFuncionario;
use App\Models\Usuario;
use App\Services\AnexoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\View\View;

class OcorrenciaFuncionarioController extends Controller
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

        $query = OcorrenciaFuncionario::query()
            ->with(['funcionario:id,nome', 'anexos'])
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

        return view('ocorrencias-funcionarios.index', [
            'condominio' => $condominio,
            'ocorrencias' => $query->orderByDesc('ocorrido_em')->paginate(15)->withQueryString(),
            'filters' => $filters,
            'tipos' => $this->tipos(),
            'statusList' => $this->statusList(),
        ]);
    }

    public function create(Condominio $condominio): View
    {
        return view('ocorrencias-funcionarios.create', [
            'condominio' => $condominio,
            'ocorrencia' => new OcorrenciaFuncionario([
                'condominio_id' => $condominio->id,
                'protocolo' => $this->generateProtocol('OF'),
                'ocorrido_em' => now()->format('Y-m-d\TH:i'),
                'status' => 'registrada',
                'reincidencia_nivel' => 0,
            ]),
            'funcionarios' => $this->funcionariosByCondominio((string) $condominio->id),
            'tipos' => $this->tipos(),
            'statusList' => $this->statusList(),
        ]);
    }

    public function store(Request $request, Condominio $condominio): RedirectResponse
    {
        $data = $this->validatedData($request, $condominio);
        $data['registrado_por'] = auth()->user()?->name ?? auth()->user()?->email;
        $data['condominio_id'] = $condominio->id;
        $data['historico_snapshot'] = [[
            'status' => $data['status'],
            'medida_aplicada' => $data['medida_aplicada'] ?? null,
            'at' => now()->toDateTimeString(),
            'by' => $data['registrado_por'],
        ]];

        $ocorrencia = OcorrenciaFuncionario::query()->create($data);

        if ($request->hasFile('anexo')) {
            $this->anexoService->store(
                condominioId: $ocorrencia->condominio_id,
                owner: $ocorrencia,
                file: $request->file('anexo'),
                directory: "condominios/{$ocorrencia->condominio_id}/ocorrencias/{$ocorrencia->id}",
                uploadedBy: auth()->id()
            );
        }

        return redirect()
            ->route('condominios.context.ocorrencias.index', ['condominio' => $condominio])
            ->with('success', 'Ocorrência de funcionário registrada com sucesso.');
    }

    public function edit(Condominio $condominio, OcorrenciaFuncionario $ocorrencia): View
    {
        $this->assertSameCondominio($condominio, $ocorrencia->condominio_id);

        $ocorrencia->load('anexos');

        return view('ocorrencias-funcionarios.edit', [
            'condominio' => $condominio,
            'ocorrencia' => $ocorrencia,
            'funcionarios' => $this->funcionariosByCondominio((string) $condominio->id),
            'tipos' => $this->tipos(),
            'statusList' => $this->statusList(),
        ]);
    }

    public function update(Request $request, Condominio $condominio, OcorrenciaFuncionario $ocorrencia): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $ocorrencia->condominio_id);

        $data = $this->validatedData($request, $condominio, $ocorrencia);

        $oldStatus = $ocorrencia->status;
        $ocorrencia->fill($data);

        if ($ocorrencia->status === 'encerrada' && ! $ocorrencia->encerrado_em) {
            $ocorrencia->encerrado_em = now();
        }

        if ($ocorrencia->status !== 'encerrada') {
            $ocorrencia->encerrado_em = null;
        }

        if ($oldStatus !== $ocorrencia->status) {
            $history = $ocorrencia->historico_snapshot ?? [];
            $history[] = [
                'status' => $ocorrencia->status,
                'medida_aplicada' => $ocorrencia->medida_aplicada,
                'at' => now()->toDateTimeString(),
                'by' => auth()->user()?->name ?? auth()->user()?->email,
            ];
            $ocorrencia->historico_snapshot = $history;
        }

        $ocorrencia->save();

        if ($request->hasFile('anexo')) {
            $this->anexoService->store(
                condominioId: $ocorrencia->condominio_id,
                owner: $ocorrencia,
                file: $request->file('anexo'),
                directory: "condominios/{$ocorrencia->condominio_id}/ocorrencias/{$ocorrencia->id}",
                uploadedBy: auth()->id()
            );
        }

        return redirect()
            ->route('condominios.context.ocorrencias.index', ['condominio' => $condominio])
            ->with('success', 'Ocorrência atualizada com sucesso.');
    }

    public function destroy(Condominio $condominio, OcorrenciaFuncionario $ocorrencia): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $ocorrencia->condominio_id);

        $ocorrencia->delete();

        return redirect()
            ->route('condominios.context.ocorrencias.index', ['condominio' => $condominio])
            ->with('success', 'Ocorrência removida com sucesso.');
    }

    public function alterarStatus(Request $request, Condominio $condominio, OcorrenciaFuncionario $ocorrencia): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $ocorrencia->condominio_id);

        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys($this->statusList()))],
            'medida_aplicada' => ['nullable', 'string', 'max:150'],
        ]);

        $oldStatus = $ocorrencia->status;
        $ocorrencia->status = $data['status'];
        if (isset($data['medida_aplicada']) && $data['medida_aplicada'] !== '') {
            $ocorrencia->medida_aplicada = $data['medida_aplicada'];
        }

        if ($data['status'] === 'encerrada') {
            $ocorrencia->encerrado_em = now();
        } else {
            $ocorrencia->encerrado_em = null;
        }

        if ($oldStatus !== $ocorrencia->status) {
            $history = $ocorrencia->historico_snapshot ?? [];
            $history[] = [
                'status' => $ocorrencia->status,
                'medida_aplicada' => $ocorrencia->medida_aplicada,
                'at' => now()->toDateTimeString(),
                'by' => auth()->user()?->name ?? auth()->user()?->email,
            ];
            $ocorrencia->historico_snapshot = $history;
        }

        $ocorrencia->save();

        return back()->with('success', 'Status da ocorrência atualizado.');
    }

    private function validatedData(Request $request, Condominio $condominio, ?OcorrenciaFuncionario $ocorrencia = null): array
    {
        $id = $ocorrencia?->id;

        $validator = validator($request->all(), [
            'protocolo' => [
                'required',
                'string',
                'max:40',
                Rule::unique('ocorrencias_funcionarios', 'protocolo')
                    ->where(fn ($query) => $query->where('condominio_id', $condominio->id))
                    ->ignore($id),
            ],
            'ocorrido_em' => ['required', 'date'],
            'funcionario_id' => ['nullable', 'uuid', Rule::exists('usuarios', 'id')],
            'funcionario_nome' => ['nullable', 'string', 'max:150'],
            'cargo' => ['nullable', 'string', 'max:100'],
            'tipo' => ['required', Rule::in(array_keys($this->tipos()))],
            'relato_detalhado' => ['required', 'string'],
            'testemunha_nome' => ['nullable', 'string', 'max:150'],
            'testemunha_contato' => ['nullable', 'string', 'max:100'],
            'medida_aplicada' => ['nullable', 'string', 'max:150'],
            'status' => ['required', Rule::in(array_keys($this->statusList()))],
            'reincidencia_nivel' => ['nullable', 'integer', 'min:0', 'max:10'],
            'anexo' => array_merge(['nullable'], $this->anexoService->allowedUploadRules()),
        ]);

        $validator->after(function (Validator $validator) use ($request, $condominio): void {
            $condominioId = $condominio->id;
            $funcionarioId = $request->input('funcionario_id');
            $funcionarioNome = trim((string) $request->input('funcionario_nome'));

            if (! $funcionarioId && $funcionarioNome === '') {
                $validator->errors()->add('funcionario_id', 'Informe o funcionário por cadastro ou nome.');
            }

            if ($funcionarioId) {
                $funcionario = Usuario::query()->find($funcionarioId);
                if ($funcionario && $funcionario->condominio_id !== $condominioId) {
                    $validator->errors()->add('funcionario_id', 'Funcionário não pertence ao condomínio selecionado.');
                }
            }
        });

        $data = $validator->validate();
        $data['condominio_id'] = $condominio->id;

        if (! empty($data['funcionario_id'])) {
            $funcionario = Usuario::query()->find($data['funcionario_id']);
            if ($funcionario) {
                $data['funcionario_nome'] = $funcionario->nome;
            }
        }

        if (($data['status'] ?? null) === 'encerrada') {
            $data['encerrado_em'] = $ocorrencia?->encerrado_em ?? now();
        } else {
            $data['encerrado_em'] = null;
        }

        $data['reincidencia_nivel'] = (int) ($data['reincidencia_nivel'] ?? 0);

        return $data;
    }

    private function funcionariosByCondominio(string $condominioId)
    {
        return Usuario::query()
            ->where('tipo', 'funcionario')
            ->where('condominio_id', $condominioId)
            ->orderBy('nome')
            ->get(['id', 'condominio_id', 'nome']);
    }

    private function generateProtocol(string $prefix): string
    {
        return $prefix.'-'.now()->format('Ymd-His').'-'.Str::upper(Str::random(4));
    }

    private function tipos(): array
    {
        return [
            'atraso' => 'Atraso',
            'desrespeito' => 'Desrespeito',
            'falha_operacional' => 'Falha operacional',
            'negligencia' => 'Negligência',
            'falta_grave' => 'Falta grave',
            'outro' => 'Outro',
        ];
    }

    private function statusList(): array
    {
        return [
            'registrada' => 'Registrada',
            'advertencia' => 'Advertência',
            'suspensao' => 'Suspensão',
            'encaminhado_juridico' => 'Encaminhado jurídico',
            'encerrada' => 'Encerrada',
        ];
    }

    private function assertSameCondominio(Condominio $condominio, string $resourceCondominioId): void
    {
        if ((string) $condominio->id !== (string) $resourceCondominioId) {
            abort(404);
        }
    }
}
