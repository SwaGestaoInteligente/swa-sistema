<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\ChecklistTemplate;
use App\Models\Condominio;
use App\Models\Vistoria;
use App\Services\RelatorioService;
use App\Services\VistoriaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VistoriaController extends Controller
{
    public function __construct(
        private readonly VistoriaService $vistoriaService,
        private readonly RelatorioService $relatorioService
    ) {
    }

    public function wizard(Condominio $condominio): View
    {
        return view('vistorias.wizard', [
            'condominio' => $condominio,
            'estrutura' => [
                'blocos' => $condominio->blocos()->count(),
                'pavimentos' => $condominio->pavimentos()->count(),
                'unidades' => $condominio->unidades()->count(),
                'areas' => $condominio->areas()->count(),
                'templates' => $condominio->templates()->count(),
            ],
            'abertas' => Vistoria::query()
                ->where('condominio_id', $condominio->id)
                ->whereIn('status', ['rascunho', 'em_andamento'])
                ->orderByDesc('updated_at')
                ->limit(8)
                ->get(['id', 'codigo', 'status', 'updated_at', 'risco_geral']),
        ]);
    }

    public function index(Request $request, Condominio $condominio): View
    {
        $filters = $request->validate([
            'codigo' => ['nullable', 'string', 'max:30'],
            'status' => ['nullable', Rule::in(array_keys($this->statusList()))],
            'tipo' => ['nullable', Rule::in(array_keys($this->tipos()))],
            'de' => ['nullable', 'date'],
            'ate' => ['nullable', 'date', 'after_or_equal:de'],
            'risco_min' => ['nullable', 'integer', 'min:0', 'max:100'],
            'risco_max' => ['nullable', 'integer', 'min:0', 'max:100', 'gte:risco_min'],
        ]);

        $query = Vistoria::query()
            ->with([
                'area:id,nome,codigo,tipo',
                'template:id,nome,categoria',
            ])
            ->withCount('itens')
            ->where('condominio_id', $condominio->id);

        if (! empty($filters['codigo'])) {
            $query->where('codigo', 'like', '%'.$filters['codigo'].'%');
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (! empty($filters['de'])) {
            $query->whereDate('competencia', '>=', $filters['de']);
        }

        if (! empty($filters['ate'])) {
            $query->whereDate('competencia', '<=', $filters['ate']);
        }

        if (isset($filters['risco_min']) && $filters['risco_min'] !== null && $filters['risco_min'] !== '') {
            $query->where('risco_geral', '>=', (int) $filters['risco_min']);
        }

        if (isset($filters['risco_max']) && $filters['risco_max'] !== null && $filters['risco_max'] !== '') {
            $query->where('risco_geral', '<=', (int) $filters['risco_max']);
        }

        return view('vistorias.index', [
            'condominio' => $condominio,
            'vistorias' => $query
                ->orderByDesc('created_at')
                ->paginate(15)
                ->withQueryString(),
            'filters' => $filters,
            'tipos' => $this->tipos(),
            'statusList' => $this->statusList(),
        ]);
    }

    public function create(Condominio $condominio): View
    {
        return view('vistorias.create', [
            'condominio' => $condominio,
            'vistoria' => new Vistoria([
                'condominio_id' => $condominio->id,
                'tipo' => 'rotina',
                'status' => 'rascunho',
                'competencia' => now()->toDateString(),
                'risco_geral' => 0,
            ]),
            'tipos' => $this->tipos(),
            'statusList' => $this->statusList(),
            'areas' => Area::query()
                ->where('condominio_id', $condominio->id)
                ->orderBy('nome')
                ->get(['id', 'nome', 'codigo', 'tipo']),
            'templates' => ChecklistTemplate::query()
                ->where('condominio_id', $condominio->id)
                ->where('ativo', true)
                ->withCount('itens')
                ->orderBy('nome')
                ->get(['id', 'nome', 'categoria']),
        ]);
    }

    public function store(Request $request, Condominio $condominio): RedirectResponse
    {
        $data = $this->validatedData($request, $condominio);

        $vistoria = Vistoria::query()->create($data);
        $createdFromTemplate = 0;

        if (! empty($data['checklist_template_id']) && ! empty($data['area_id'])) {
            $template = ChecklistTemplate::query()->find($data['checklist_template_id']);
            $area = Area::query()->find($data['area_id']);

            if ($template && $area) {
                $createdFromTemplate = $this->vistoriaService->aplicarTemplate($vistoria, $template, $area);
            }
        }

        $message = $createdFromTemplate > 0
            ? "Vistoria criada com {$createdFromTemplate} itens a partir do template."
            : 'Vistoria criada com sucesso. Agora registre os itens no modo campo.';

        return redirect()
            ->route('condominios.context.vistorias.show', ['condominio' => $condominio, 'vistoria' => $vistoria])
            ->with('success', $message);
    }

    public function show(Condominio $condominio, Vistoria $vistoria): View
    {
        $this->assertSameCondominio($condominio, $vistoria->condominio_id);

        $vistoria->load([
            'condominio:id,nome,codigo,cidade,uf',
            'area:id,nome,codigo,tipo',
            'template:id,nome,categoria',
            'itens' => fn ($query) => $query
                ->with(['area:id,nome,codigo,tipo', 'anexos'])
                ->orderBy('ordem')
                ->orderByDesc('inspecionado_em')
                ->orderByDesc('created_at'),
        ]);

        $totalItens = $vistoria->itens->count();
        $itensOk = $vistoria->itens->where('status', 'ok')->count();
        $pendencias = $this->vistoriaService->pendenciasEvidencia($vistoria);

        return view('vistorias.show', [
            'condominio' => $condominio,
            'vistoria' => $vistoria,
            'areas' => Area::query()
                ->where('condominio_id', $condominio->id)
                ->orderBy('nome')
                ->get(['id', 'nome', 'codigo', 'tipo']),
            'templates' => ChecklistTemplate::query()
                ->where('condominio_id', $condominio->id)
                ->where('ativo', true)
                ->withCount('itens')
                ->orderBy('nome')
                ->get(['id', 'nome', 'categoria']),
            'statusList' => $this->itemStatusList(),
            'criticidades' => $this->criticidades(),
            'quickObs' => $this->quickObservacoes(),
            'resumoCampo' => [
                'total' => $totalItens,
                'ok' => $itensOk,
                'nao_ok' => max($totalItens - $itensOk, 0),
                'pendencias' => $pendencias,
                'progresso' => $totalItens > 0 ? (int) round(($itensOk / $totalItens) * 100) : 0,
            ],
        ]);
    }

    public function edit(Condominio $condominio, Vistoria $vistoria): View
    {
        $this->assertSameCondominio($condominio, $vistoria->condominio_id);
        $this->ensureEditable($vistoria);

        return view('vistorias.edit', [
            'condominio' => $condominio,
            'vistoria' => $vistoria,
            'tipos' => $this->tipos(),
            'statusList' => $this->statusList(),
            'areas' => Area::query()
                ->where('condominio_id', $condominio->id)
                ->orderBy('nome')
                ->get(['id', 'nome', 'codigo', 'tipo']),
            'templates' => ChecklistTemplate::query()
                ->where('condominio_id', $condominio->id)
                ->where('ativo', true)
                ->withCount('itens')
                ->orderBy('nome')
                ->get(['id', 'nome', 'categoria']),
        ]);
    }

    public function update(Request $request, Condominio $condominio, Vistoria $vistoria): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $vistoria->condominio_id);
        $this->ensureEditable($vistoria);

        $data = $this->validatedData($request, $condominio, $vistoria);

        if (($data['status'] ?? null) === 'finalizada') {
            $data['finalizada_em'] = $data['finalizada_em'] ?? now();
            $data['iniciada_em'] = $data['iniciada_em'] ?? $vistoria->iniciada_em ?? now();
        }

        $vistoria->update($data);

        return redirect()
            ->route('condominios.context.vistorias.show', ['condominio' => $condominio, 'vistoria' => $vistoria])
            ->with('success', 'Vistoria atualizada com sucesso.');
    }

    public function aplicarTemplate(Request $request, Condominio $condominio, Vistoria $vistoria): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $vistoria->condominio_id);
        $this->ensureEditable($vistoria);

        $data = $request->validate([
            'checklist_template_id' => ['required', 'uuid', Rule::exists('checklist_templates', 'id')],
            'area_id' => ['required', 'uuid', Rule::exists('areas', 'id')],
        ]);

        $template = ChecklistTemplate::query()
            ->where('id', $data['checklist_template_id'])
            ->where('condominio_id', $condominio->id)
            ->first();
        $area = Area::query()
            ->where('id', $data['area_id'])
            ->where('condominio_id', $condominio->id)
            ->first();

        if (! $template || ! $area) {
            return back()->withErrors([
                'checklist_template_id' => 'Template ou área inválidos para este condomínio.',
            ]);
        }

        $vistoria->update([
            'checklist_template_id' => $template->id,
            'area_id' => $area->id,
        ]);

        $created = $this->vistoriaService->aplicarTemplate($vistoria, $template, $area);

        if ($created === 0) {
            return back()->with('success', 'Template já estava aplicado para esta área.');
        }

        return back()->with('success', "Template aplicado com {$created} itens.");
    }

    public function destroy(Condominio $condominio, Vistoria $vistoria): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $vistoria->condominio_id);
        $this->ensureEditable($vistoria);
        $vistoria->delete();

        return redirect()
            ->route('condominios.context.vistorias.index', $condominio)
            ->with('success', 'Vistoria removida com sucesso.');
    }

    public function finalizar(Condominio $condominio, Vistoria $vistoria): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $vistoria->condominio_id);

        if ($vistoria->status === 'finalizada') {
            return back()->with('success', 'Esta vistoria já está finalizada.');
        }

        $this->ensureEditable($vistoria);

        if ($vistoria->itens()->count() === 0) {
            return back()->withErrors([
                'vistoria' => 'Adicione ao menos 1 item antes de finalizar.',
            ]);
        }

        $pendencias = $this->vistoriaService->pendenciasEvidencia($vistoria);
        if ($pendencias > 0) {
            return back()->withErrors([
                'vistoria' => "Existem {$pendencias} item(ns) não-OK sem foto/observação. Resolva antes de finalizar.",
            ]);
        }

        $vistoria->update([
            'status' => 'finalizada',
            'iniciada_em' => $vistoria->iniciada_em ?? now(),
            'finalizada_em' => now(),
        ]);

        return redirect()
            ->route('condominios.context.vistorias.show', ['condominio' => $condominio, 'vistoria' => $vistoria])
            ->with('success', 'Vistoria finalizada com sucesso.');
    }

    public function reabrir(Request $request, Condominio $condominio, Vistoria $vistoria): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $vistoria->condominio_id);

        if (! in_array($vistoria->status, ['finalizada', 'cancelada'], true)) {
            return back()->with('success', 'A vistoria já está aberta.');
        }

        $motivo = trim((string) $request->input('motivo'));
        $ator = auth()->user()?->name ?? auth()->user()?->email ?? 'sistema';
        $log = '['.now()->format('d/m/Y H:i')."] Vistoria reaberta por {$ator}";
        if ($motivo !== '') {
            $log .= " | Motivo: {$motivo}";
        }

        $observacoesAtuais = trim((string) $vistoria->observacoes);
        $observacoesAtualizadas = trim($observacoesAtuais.($observacoesAtuais !== '' ? PHP_EOL : '').$log);

        $vistoria->update([
            'status' => 'em_andamento',
            'finalizada_em' => null,
            'iniciada_em' => $vistoria->iniciada_em ?? now(),
            'observacoes' => $observacoesAtualizadas,
        ]);

        return redirect()
            ->route('condominios.context.vistorias.show', ['condominio' => $condominio, 'vistoria' => $vistoria])
            ->with('success', 'Vistoria reaberta com sucesso.');
    }

    public function pdf(Condominio $condominio, Vistoria $vistoria)
    {
        $this->assertSameCondominio($condominio, $vistoria->condominio_id);
        $relatorio = $this->relatorioService->gerarVistoria($vistoria, auth()->user());

        return redirect()
            ->route('condominios.context.relatorios.download', [
                'condominio' => $condominio,
                'relatorio' => $relatorio,
            ]);
    }

    private function validatedData(Request $request, Condominio $condominio, ?Vistoria $vistoria = null): array
    {
        $data = $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:30',
                Rule::unique('vistorias', 'codigo')
                    ->where(fn ($query) => $query->where('condominio_id', $condominio->id))
                    ->ignore($vistoria?->id),
            ],
            'tipo' => ['required', Rule::in(array_keys($this->tipos()))],
            'status' => ['required', Rule::in(array_keys($this->statusList()))],
            'competencia' => ['nullable', 'date'],
            'iniciada_em' => ['nullable', 'date'],
            'finalizada_em' => ['nullable', 'date'],
            'responsavel_nome' => ['nullable', 'string', 'max:120'],
            'observacoes' => ['nullable', 'string'],
            'risco_nivel' => ['nullable', Rule::in(['neutro', 'baixo', 'medio', 'alto'])],
            'area_id' => ['nullable', 'uuid', Rule::exists('areas', 'id')],
            'checklist_template_id' => ['nullable', 'uuid', Rule::exists('checklist_templates', 'id')],
        ]);

        if (! empty($data['area_id'])) {
            $area = Area::query()->find($data['area_id']);
            if (! $area || (string) $area->condominio_id !== (string) $condominio->id) {
                abort(422, 'Área inválida para este condomínio.');
            }
        }

        if (! empty($data['checklist_template_id'])) {
            $template = ChecklistTemplate::query()->find($data['checklist_template_id']);
            if (! $template || (string) $template->condominio_id !== (string) $condominio->id) {
                abort(422, 'Template inválido para este condomínio.');
            }
        }

        if (! empty($data['checklist_template_id']) && empty($data['area_id'])) {
            abort(422, 'Selecione a área para aplicar template.');
        }

        $data['condominio_id'] = $condominio->id;
        $data['risco_geral'] = match ($data['risco_nivel'] ?? null) {
            'neutro' => 0,
            'baixo' => 25,
            'medio' => 55,
            'alto' => 85,
            default => $vistoria?->risco_geral ?? 0,
        };
        unset($data['risco_nivel']);

        return $data;
    }

    private function tipos(): array
    {
        return [
            'rotina' => 'Rotina',
            'extraordinaria' => 'Extraordinária',
            'pos_ocorrencia' => 'Pós ocorrência',
        ];
    }

    private function statusList(): array
    {
        return [
            'rascunho' => 'Rascunho',
            'em_andamento' => 'Em andamento',
            'finalizada' => 'Finalizada',
            'cancelada' => 'Cancelada',
        ];
    }

    private function itemStatusList(): array
    {
        return [
            'ok' => 'OK',
            'danificado' => 'Danificado',
            'ausente' => 'Ausente',
            'improvisado' => 'Improvisado',
        ];
    }

    private function criticidades(): array
    {
        return [
            'baixa' => 'Baixa',
            'media' => 'Média',
            'alta' => 'Alta',
            'critica' => 'Crítica',
        ];
    }

    private function quickObservacoes(): array
    {
        return [
            'Placa caída',
            'Extintor vencido',
            'Lâmpada apagada',
            'Corrimão solto',
            'Sinalização ausente',
        ];
    }

    private function ensureEditable(Vistoria $vistoria): void
    {
        if (in_array($vistoria->status, ['finalizada', 'cancelada'], true)) {
            abort(403, 'Vistoria finalizada/cancelada não pode ser editada.');
        }
    }

    private function assertSameCondominio(Condominio $condominio, string $resourceCondominioId): void
    {
        if ((string) $condominio->id !== (string) $resourceCondominioId) {
            abort(404);
        }
    }
}
