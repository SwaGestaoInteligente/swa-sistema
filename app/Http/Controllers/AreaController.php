<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Bloco;
use App\Models\Condominio;
use App\Models\Pavimento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\View\View;

class AreaController extends Controller
{
    public function index(Request $request, Condominio $condominio): View
    {
        $filters = $request->validate([
            'bloco_id' => ['nullable', 'uuid', Rule::exists('blocos', 'id')],
            'pavimento_id' => ['nullable', 'uuid', Rule::exists('pavimentos', 'id')],
            'tipo' => ['nullable', Rule::in(['externa', 'comum', 'tecnica', 'seguranca'])],
            'nome' => ['nullable', 'string', 'max:120'],
        ]);

        $query = Area::query()
            ->with(['bloco:id,nome,codigo', 'pavimento:id,nome,codigo'])
            ->where('condominio_id', $condominio->id);

        if (! empty($filters['bloco_id'])) {
            $query->where('bloco_id', $filters['bloco_id']);
        }

        if (! empty($filters['pavimento_id'])) {
            $query->where('pavimento_id', $filters['pavimento_id']);
        }

        if (! empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (! empty($filters['nome'])) {
            $query->where('nome', 'like', '%'.$filters['nome'].'%');
        }

        return view('areas.index', [
            'condominio' => $condominio,
            'areas' => $query
                ->orderBy('nome')
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
        return view('areas.create', [
            'condominio' => $condominio,
            'area' => new Area([
                'condominio_id' => $condominio->id,
                'ativa' => true,
                'tipo' => 'comum',
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
        $data['ativa'] = $request->boolean('ativa');
        $data['condominio_id'] = $condominio->id;

        Area::query()->create($data);

        return redirect()
            ->route('condominios.context.areas.index', $condominio)
            ->with('success', 'Área criada com sucesso.');
    }

    public function edit(Condominio $condominio, Area $area): View
    {
        $this->assertSameCondominio($condominio, $area->condominio_id);

        return view('areas.edit', [
            'condominio' => $condominio,
            'area' => $area,
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

    public function update(Request $request, Condominio $condominio, Area $area): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $area->condominio_id);

        $data = $this->validatedData($request, $condominio, $area);
        $data['ativa'] = $request->boolean('ativa');
        $area->update($data);

        return redirect()
            ->route('condominios.context.areas.index', $condominio)
            ->with('success', 'Área atualizada com sucesso.');
    }

    public function destroy(Condominio $condominio, Area $area): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $area->condominio_id);
        $area->delete();

        return redirect()
            ->route('condominios.context.areas.index', $condominio)
            ->with('success', 'Área removida com sucesso.');
    }

    private function validatedData(Request $request, Condominio $condominio, ?Area $area = null): array
    {
        $validator = validator($request->all(), [
            'bloco_id' => ['nullable', 'uuid', Rule::exists('blocos', 'id')],
            'pavimento_id' => ['nullable', 'uuid', Rule::exists('pavimentos', 'id')],
            'tipo' => ['required', Rule::in(['externa', 'comum', 'tecnica', 'seguranca'])],
            'codigo' => [
                'required',
                'string',
                'max:30',
                Rule::unique('areas', 'codigo')
                    ->where(fn ($query) => $query->where('condominio_id', $condominio->id))
                    ->ignore($area?->id),
            ],
            'nome' => ['required', 'string', 'max:120'],
            'descricao' => ['nullable', 'string'],
            'ativa' => ['nullable', 'boolean'],
        ]);

        $validator->after(function (Validator $validator) use ($request, $condominio): void {
            $blocoId = $request->input('bloco_id');
            $pavimentoId = $request->input('pavimento_id');

            if ($blocoId) {
                $bloco = Bloco::query()->find($blocoId);
                if ($bloco && (string) $bloco->condominio_id !== (string) $condominio->id) {
                    $validator->errors()->add('bloco_id', 'Bloco não pertence ao condomínio selecionado.');
                }
            }

            if ($pavimentoId) {
                $pavimento = Pavimento::query()->find($pavimentoId);
                if ($pavimento && (string) $pavimento->condominio_id !== (string) $condominio->id) {
                    $validator->errors()->add('pavimento_id', 'Pavimento não pertence ao condomínio selecionado.');
                }

                if ($blocoId && $pavimento && (string) $pavimento->bloco_id !== (string) $blocoId) {
                    $validator->errors()->add('pavimento_id', 'Pavimento não pertence ao bloco selecionado.');
                }
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
