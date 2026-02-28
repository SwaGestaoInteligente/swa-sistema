<?php

namespace App\Http\Controllers;

use App\Models\Bloco;
use App\Models\Condominio;
use App\Models\Pavimento;
use App\Models\Unidade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\View\View;

class UnidadeController extends Controller
{
    public function index(Request $request, Condominio $condominio): View
    {
        $filters = $request->validate([
            'bloco_id' => ['nullable', 'uuid', Rule::exists('blocos', 'id')],
            'pavimento_id' => ['nullable', 'uuid', Rule::exists('pavimentos', 'id')],
            'status' => ['nullable', Rule::in(['ocupado', 'vago'])],
            'tipo' => ['nullable', Rule::in(['apto', 'sala'])],
            'numero' => ['nullable', 'string', 'max:30'],
        ]);

        $query = Unidade::query()
            ->with(['bloco:id,nome,codigo', 'pavimento:id,nome,codigo'])
            ->where('condominio_id', $condominio->id);

        if (! empty($filters['bloco_id'])) {
            $query->where('bloco_id', $filters['bloco_id']);
        }

        if (! empty($filters['pavimento_id'])) {
            $query->where('pavimento_id', $filters['pavimento_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (! empty($filters['numero'])) {
            $query->where('numero', 'like', '%'.$filters['numero'].'%');
        }

        return view('unidades.index', [
            'condominio' => $condominio,
            'unidades' => $query
                ->orderBy('numero')
                ->paginate(20)
                ->withQueryString(),
            'filters' => $filters,
            'blocos' => Bloco::query()
                ->where('condominio_id', $condominio->id)
                ->orderBy('nome')
                ->get(['id', 'nome', 'codigo']),
            'pavimentos' => Pavimento::query()
                ->where('condominio_id', $condominio->id)
                ->orderBy('ordem')
                ->orderBy('nome')
                ->get(['id', 'bloco_id', 'nome', 'codigo']),
        ]);
    }

    public function create(Condominio $condominio): View
    {
        return view('unidades.create', [
            'condominio' => $condominio,
            'unidade' => new Unidade([
                'condominio_id' => $condominio->id,
                'tipo' => 'apto',
                'status' => 'ocupado',
            ]),
            'blocos' => Bloco::query()
                ->where('condominio_id', $condominio->id)
                ->orderBy('nome')
                ->get(['id', 'nome', 'codigo']),
            'pavimentos' => Pavimento::query()
                ->where('condominio_id', $condominio->id)
                ->orderBy('ordem')
                ->orderBy('nome')
                ->get(['id', 'bloco_id', 'nome', 'codigo']),
        ]);
    }

    public function store(Request $request, Condominio $condominio): RedirectResponse
    {
        $data = $this->validatedData($request, $condominio);
        $data['condominio_id'] = $condominio->id;

        Unidade::query()->create($data);

        return redirect()
            ->route('condominios.context.unidades.index', $condominio)
            ->with('success', 'Unidade cadastrada com sucesso.');
    }

    public function edit(Condominio $condominio, Unidade $unidade): View
    {
        $this->assertSameCondominio($condominio, $unidade->condominio_id);

        return view('unidades.edit', [
            'condominio' => $condominio,
            'unidade' => $unidade,
            'blocos' => Bloco::query()
                ->where('condominio_id', $condominio->id)
                ->orderBy('nome')
                ->get(['id', 'nome', 'codigo']),
            'pavimentos' => Pavimento::query()
                ->where('condominio_id', $condominio->id)
                ->orderBy('ordem')
                ->orderBy('nome')
                ->get(['id', 'bloco_id', 'nome', 'codigo']),
        ]);
    }

    public function update(Request $request, Condominio $condominio, Unidade $unidade): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $unidade->condominio_id);

        $data = $this->validatedData($request, $condominio, $unidade);
        $unidade->update($data);

        return redirect()
            ->route('condominios.context.unidades.index', $condominio)
            ->with('success', 'Unidade atualizada com sucesso.');
    }

    public function destroy(Condominio $condominio, Unidade $unidade): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $unidade->condominio_id);

        $unidade->delete();

        return redirect()
            ->route('condominios.context.unidades.index', $condominio)
            ->with('success', 'Unidade removida com sucesso.');
    }

    private function validatedData(Request $request, Condominio $condominio, ?Unidade $unidade = null): array
    {
        $validator = validator($request->all(), [
            'bloco_id' => ['required', 'uuid', Rule::exists('blocos', 'id')],
            'pavimento_id' => ['required', 'uuid', Rule::exists('pavimentos', 'id')],
            'numero' => [
                'required',
                'string',
                'max:30',
                Rule::unique('unidades', 'numero')
                    ->where(fn ($query) => $query->where('condominio_id', $condominio->id))
                    ->ignore($unidade?->id),
            ],
            'tipo' => ['required', Rule::in(['apto', 'sala'])],
            'status' => ['required', Rule::in(['ocupado', 'vago'])],
        ]);

        $validator->after(function (Validator $validator) use ($request, $condominio): void {
            $bloco = Bloco::query()->find($request->input('bloco_id'));
            $pavimento = Pavimento::query()->find($request->input('pavimento_id'));

            if ($bloco && (string) $bloco->condominio_id !== (string) $condominio->id) {
                $validator->errors()->add('bloco_id', 'Bloco não pertence ao condomínio selecionado.');
            }

            if ($pavimento && (string) $pavimento->condominio_id !== (string) $condominio->id) {
                $validator->errors()->add('pavimento_id', 'Pavimento não pertence ao condomínio selecionado.');
            }

            if ($bloco && $pavimento && (string) $pavimento->bloco_id !== (string) $bloco->id) {
                $validator->errors()->add('pavimento_id', 'Pavimento não pertence ao bloco informado.');
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
