<?php

namespace App\Http\Controllers;

use App\Models\ChecklistTemplate;
use App\Models\Condominio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ChecklistTemplateController extends Controller
{
    public function index(Condominio $condominio): View
    {
        return view('templates.index', [
            'condominio' => $condominio,
            'templates' => ChecklistTemplate::query()
                ->withCount('itens')
                ->where('condominio_id', $condominio->id)
                ->orderByDesc('updated_at')
                ->paginate(20),
        ]);
    }

    public function create(Condominio $condominio): View
    {
        return view('templates.create', [
            'condominio' => $condominio,
            'template' => new ChecklistTemplate([
                'condominio_id' => $condominio->id,
                'ativo' => true,
            ]),
            'categorias' => $this->categorias(),
            'itemCategorias' => $this->itemCategorias(),
        ]);
    }

    public function store(Request $request, Condominio $condominio): RedirectResponse
    {
        $data = $this->validatedData($request, $condominio);

        $template = ChecklistTemplate::query()->create([
            'condominio_id' => $condominio->id,
            'nome' => $data['nome'],
            'categoria' => $data['categoria'] ?: null,
            'ativo' => (bool) $data['ativo'],
        ]);

        foreach ($data['itens'] as $index => $item) {
            $template->itens()->create([
                'titulo_item' => $item['titulo_item'],
                'categoria' => $item['categoria'],
                'obrigatorio_foto_se_nao_ok' => (bool) ($item['obrigatorio_foto_se_nao_ok'] ?? true),
                'ordem' => (int) ($item['ordem'] ?? ($index + 1)),
            ]);
        }

        return redirect()
            ->route('condominios.context.templates.index', $condominio)
            ->with('success', 'Template criado com sucesso.');
    }

    public function edit(Condominio $condominio, ChecklistTemplate $template): View
    {
        $this->assertSameCondominio($condominio, $template->condominio_id);
        $template->load('itens');

        return view('templates.edit', [
            'condominio' => $condominio,
            'template' => $template,
            'categorias' => $this->categorias(),
            'itemCategorias' => $this->itemCategorias(),
        ]);
    }

    public function update(Request $request, Condominio $condominio, ChecklistTemplate $template): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $template->condominio_id);
        $data = $this->validatedData($request, $condominio, $template);

        $template->update([
            'nome' => $data['nome'],
            'categoria' => $data['categoria'] ?: null,
            'ativo' => (bool) $data['ativo'],
        ]);

        $template->itens()->delete();
        foreach ($data['itens'] as $index => $item) {
            $template->itens()->create([
                'titulo_item' => $item['titulo_item'],
                'categoria' => $item['categoria'],
                'obrigatorio_foto_se_nao_ok' => (bool) ($item['obrigatorio_foto_se_nao_ok'] ?? true),
                'ordem' => (int) ($item['ordem'] ?? ($index + 1)),
            ]);
        }

        return redirect()
            ->route('condominios.context.templates.index', $condominio)
            ->with('success', 'Template atualizado com sucesso.');
    }

    public function destroy(Condominio $condominio, ChecklistTemplate $template): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $template->condominio_id);
        $template->delete();

        return redirect()
            ->route('condominios.context.templates.index', $condominio)
            ->with('success', 'Template removido com sucesso.');
    }

    private function validatedData(Request $request, Condominio $condominio, ?ChecklistTemplate $template = null): array
    {
        return $request->validate([
            'nome' => [
                'required',
                'string',
                'max:120',
                Rule::unique('checklist_templates', 'nome')
                    ->where(fn ($query) => $query->where('condominio_id', $condominio->id))
                    ->ignore($template?->id),
            ],
            'categoria' => ['nullable', Rule::in(array_keys($this->categorias()))],
            'ativo' => ['nullable', 'boolean'],
            'itens' => ['required', 'array', 'min:1'],
            'itens.*.titulo_item' => ['required', 'string', 'max:150'],
            'itens.*.categoria' => ['required', Rule::in(array_keys($this->itemCategorias()))],
            'itens.*.obrigatorio_foto_se_nao_ok' => ['nullable', 'boolean'],
            'itens.*.ordem' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function categorias(): array
    {
        return [
            'seguranca' => 'Segurança',
            'sinalizacao' => 'Sinalização',
            'iluminacao_emergencia' => 'Iluminação de emergência',
            'extintores' => 'Extintores',
            'estrutural' => 'Estrutural',
            'outros' => 'Outros',
        ];
    }

    private function itemCategorias(): array
    {
        return [
            'extintor' => 'Extintor',
            'placa_saida' => 'Placa de saída',
            'luz_emergencia' => 'Luz de emergência',
            'corrimao' => 'Corrimão',
            'sinalizacao' => 'Sinalização',
            'outro' => 'Outro',
        ];
    }

    private function assertSameCondominio(Condominio $condominio, string $resourceCondominioId): void
    {
        if ((string) $condominio->id !== (string) $resourceCondominioId) {
            abort(404);
        }
    }
}
