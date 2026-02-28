<?php

namespace App\Http\Controllers;

use App\Models\Bloco;
use App\Models\Condominio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BlocoController extends Controller
{
    public function index(Request $request, Condominio $condominio): View
    {
        $filters = $request->validate([
            'ativo' => ['nullable', Rule::in(['1', '0'])],
            'nome' => ['nullable', 'string', 'max:120'],
        ]);

        $query = Bloco::query()
            ->where('condominio_id', $condominio->id);

        if (isset($filters['ativo']) && $filters['ativo'] !== '') {
            $query->where('ativo', $filters['ativo'] === '1');
        }

        if (! empty($filters['nome'])) {
            $query->where('nome', 'like', '%'.$filters['nome'].'%');
        }

        return view('blocos.index', [
            'condominio' => $condominio,
            'blocos' => $query
                ->orderBy('ordem')
                ->orderBy('nome')
                ->paginate(20)
                ->withQueryString(),
            'filters' => $filters,
        ]);
    }

    public function create(Condominio $condominio): View
    {
        return view('blocos.create', [
            'condominio' => $condominio,
            'bloco' => new Bloco([
                'condominio_id' => $condominio->id,
                'ativo' => true,
                'ordem' => 0,
            ]),
        ]);
    }

    public function store(Request $request, Condominio $condominio): RedirectResponse
    {
        $data = $this->validatedData($request, $condominio);
        $data['ativo'] = $request->boolean('ativo');
        $data['condominio_id'] = $condominio->id;

        Bloco::query()->create($data);

        return redirect()
            ->route('condominios.context.blocos.index', $condominio)
            ->with('success', 'Bloco criado com sucesso.');
    }

    public function edit(Condominio $condominio, Bloco $bloco): View
    {
        $this->assertSameCondominio($condominio, $bloco->condominio_id);

        return view('blocos.edit', [
            'condominio' => $condominio,
            'bloco' => $bloco,
        ]);
    }

    public function update(Request $request, Condominio $condominio, Bloco $bloco): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $bloco->condominio_id);

        $data = $this->validatedData($request, $condominio, $bloco);
        $data['ativo'] = $request->boolean('ativo');
        $bloco->update($data);

        return redirect()
            ->route('condominios.context.blocos.index', $condominio)
            ->with('success', 'Bloco atualizado com sucesso.');
    }

    public function destroy(Condominio $condominio, Bloco $bloco): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $bloco->condominio_id);
        $bloco->delete();

        return redirect()
            ->route('condominios.context.blocos.index', $condominio)
            ->with('success', 'Bloco removido com sucesso.');
    }

    private function validatedData(Request $request, Condominio $condominio, ?Bloco $bloco = null): array
    {
        return $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:30',
                Rule::unique('blocos', 'codigo')
                    ->where(fn ($query) => $query->where('condominio_id', $condominio->id))
                    ->ignore($bloco?->id),
            ],
            'nome' => ['required', 'string', 'max:120'],
            'descricao' => ['nullable', 'string'],
            'ordem' => ['nullable', 'integer', 'min:0'],
            'ativo' => ['nullable', 'boolean'],
        ]);
    }

    private function assertSameCondominio(Condominio $condominio, string $resourceCondominioId): void
    {
        if ((string) $condominio->id !== (string) $resourceCondominioId) {
            abort(404);
        }
    }
}
