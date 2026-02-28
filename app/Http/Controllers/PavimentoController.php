<?php

namespace App\Http\Controllers;

use App\Models\Bloco;
use App\Models\Condominio;
use App\Models\Pavimento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\View\View;

class PavimentoController extends Controller
{
    public function index(Request $request, Condominio $condominio): View
    {
        $filters = $request->validate([
            'bloco_id' => ['nullable', 'uuid', Rule::exists('blocos', 'id')],
            'ativo' => ['nullable', Rule::in(['1', '0'])],
            'nome' => ['nullable', 'string', 'max:120'],
        ]);

        $query = Pavimento::query()
            ->with('bloco:id,nome,codigo')
            ->where('condominio_id', $condominio->id);

        if (! empty($filters['bloco_id'])) {
            $query->where('bloco_id', $filters['bloco_id']);
        }

        if (isset($filters['ativo']) && $filters['ativo'] !== '') {
            $query->where('ativo', $filters['ativo'] === '1');
        }

        if (! empty($filters['nome'])) {
            $query->where('nome', 'like', '%'.$filters['nome'].'%');
        }

        return view('pavimentos.index', [
            'condominio' => $condominio,
            'pavimentos' => $query
                ->orderBy('ordem')
                ->orderBy('nome')
                ->paginate(20)
                ->withQueryString(),
            'filters' => $filters,
            'blocos' => Bloco::query()
                ->where('condominio_id', $condominio->id)
                ->orderBy('nome')
                ->get(['id', 'nome', 'codigo']),
        ]);
    }

    public function create(Condominio $condominio): View
    {
        return view('pavimentos.create', [
            'condominio' => $condominio,
            'pavimento' => new Pavimento([
                'condominio_id' => $condominio->id,
                'ativo' => true,
                'ordem' => 0,
                'nivel' => 0,
            ]),
            'blocos' => Bloco::query()
                ->where('condominio_id', $condominio->id)
                ->orderBy('nome')
                ->get(['id', 'nome', 'codigo']),
        ]);
    }

    public function store(Request $request, Condominio $condominio): RedirectResponse
    {
        $data = $this->validatedData($request, $condominio);
        $data['ativo'] = $request->boolean('ativo');
        $data['condominio_id'] = $condominio->id;

        Pavimento::query()->create($data);

        return redirect()
            ->route('condominios.context.pavimentos.index', [
                'condominio' => $condominio,
                'bloco_id' => $data['bloco_id'],
            ])
            ->with('success', 'Pavimento criado com sucesso.');
    }

    public function edit(Condominio $condominio, Pavimento $pavimento): View
    {
        $this->assertSameCondominio($condominio, $pavimento->condominio_id);

        return view('pavimentos.edit', [
            'condominio' => $condominio,
            'pavimento' => $pavimento,
            'blocos' => Bloco::query()
                ->where('condominio_id', $condominio->id)
                ->orderBy('nome')
                ->get(['id', 'nome', 'codigo']),
        ]);
    }

    public function update(Request $request, Condominio $condominio, Pavimento $pavimento): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $pavimento->condominio_id);

        $data = $this->validatedData($request, $condominio, $pavimento);
        $data['ativo'] = $request->boolean('ativo');
        $pavimento->update($data);

        return redirect()
            ->route('condominios.context.pavimentos.index', [
                'condominio' => $condominio,
                'bloco_id' => $pavimento->bloco_id,
            ])
            ->with('success', 'Pavimento atualizado com sucesso.');
    }

    public function destroy(Condominio $condominio, Pavimento $pavimento): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $pavimento->condominio_id);

        $blocoId = $pavimento->bloco_id;
        $pavimento->delete();

        return redirect()
            ->route('condominios.context.pavimentos.index', [
                'condominio' => $condominio,
                'bloco_id' => $blocoId,
            ])
            ->with('success', 'Pavimento removido com sucesso.');
    }

    private function validatedData(Request $request, Condominio $condominio, ?Pavimento $pavimento = null): array
    {
        $validator = validator($request->all(), [
            'bloco_id' => ['required', 'uuid', Rule::exists('blocos', 'id')],
            'codigo' => [
                'required',
                'string',
                'max:30',
                Rule::unique('pavimentos', 'codigo')
                    ->where(fn ($query) => $query->where('bloco_id', $request->input('bloco_id')))
                    ->ignore($pavimento?->id),
            ],
            'nome' => ['required', 'string', 'max:120'],
            'nivel' => ['nullable', 'integer'],
            'ordem' => ['nullable', 'integer', 'min:0'],
            'ativo' => ['nullable', 'boolean'],
        ]);

        $validator->after(function (Validator $validator) use ($request, $condominio): void {
            $bloco = Bloco::query()->find($request->input('bloco_id'));

            if ($bloco && (string) $bloco->condominio_id !== (string) $condominio->id) {
                $validator->errors()->add('bloco_id', 'Bloco não pertence ao condomínio selecionado.');
            }
        });

        return $validator->validate();
    }

    private function assertSameCondominio(Condominio $condominio, string $resourceCondominioId): void
    {
        if ((string) $condominio->id !== (string) $resourceCondominioId) {
            abort(404);
        }
    }
}
