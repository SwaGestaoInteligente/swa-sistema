<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Bloco;
use App\Models\Condominio;
use App\Models\ConflitoMorador;
use App\Models\OcorrenciaFuncionario;
use App\Models\Pavimento;
use App\Models\Vistoria;
use App\Models\VistoriaItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CondominioController extends Controller
{
    public function contextRedirect(Condominio $condominio): RedirectResponse
    {
        $this->assertAccess($condominio);

        return redirect()->route('condominios.context.dashboard', $condominio);
    }

    public function dashboard(Condominio $condominio): View
    {
        $this->assertAccess($condominio);

        $vistoriaStatuses = collect(
            Vistoria::query()
                ->where('condominio_id', $condominio->id)
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->all()
        );

        $conflitoStatuses = collect(
            ConflitoMorador::query()
                ->where('condominio_id', $condominio->id)
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->all()
        );

        $ocorrenciaStatuses = collect(
            OcorrenciaFuncionario::query()
                ->where('condominio_id', $condominio->id)
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->all()
        );

        $itensTotal = VistoriaItem::query()
            ->where('condominio_id', $condominio->id)
            ->count();

        $itensOk = VistoriaItem::query()
            ->where('condominio_id', $condominio->id)
            ->where('status', 'ok')
            ->count();

        $trend = $this->vistoriaTrend($condominio->id);

        return view('condominios.dashboard', [
            'condominio' => $condominio,
            'stats' => [
                'blocos' => Bloco::query()->where('condominio_id', $condominio->id)->count(),
                'pavimentos' => Pavimento::query()->where('condominio_id', $condominio->id)->count(),
                'areas' => Area::query()->where('condominio_id', $condominio->id)->count(),
                'unidades' => $condominio->unidades()->count(),
                'vistorias' => Vistoria::query()->where('condominio_id', $condominio->id)->count(),
                'conflitos' => ConflitoMorador::query()->where('condominio_id', $condominio->id)->count(),
                'ocorrencias' => OcorrenciaFuncionario::query()->where('condominio_id', $condominio->id)->count(),
                'templates' => $condominio->templates()->count(),
                'emails' => $condominio->emails()->count(),
                'relatorios' => $condominio->relatorios()->count(),
            ],
            'distribuicoes' => [
                'vistorias' => $this->statusDistribution(
                    $vistoriaStatuses,
                    ['rascunho', 'em_andamento', 'finalizada', 'cancelada'],
                    [
                        'rascunho' => 'Rascunho',
                        'em_andamento' => 'Em andamento',
                        'finalizada' => 'Finalizada',
                        'cancelada' => 'Cancelada',
                    ]
                ),
                'conflitos' => $this->statusDistribution(
                    $conflitoStatuses,
                    ['em_analise', 'advertido', 'resolvido', 'judicial'],
                    [
                        'em_analise' => 'Em analise',
                        'advertido' => 'Advertido',
                        'resolvido' => 'Resolvido',
                        'judicial' => 'Judicial',
                    ]
                ),
                'ocorrencias' => $this->statusDistribution(
                    $ocorrenciaStatuses,
                    ['registrada', 'advertencia', 'suspensao', 'encaminhado_juridico', 'encerrada'],
                    [
                        'registrada' => 'Registrada',
                        'advertencia' => 'Advertencia',
                        'suspensao' => 'Suspensao',
                        'encaminhado_juridico' => 'Encaminhado juridico',
                        'encerrada' => 'Encerrada',
                    ]
                ),
            ],
            'trend' => $trend,
            'qualidade' => [
                'itens_total' => $itensTotal,
                'itens_ok' => $itensOk,
                'itens_nao_ok' => max($itensTotal - $itensOk, 0),
                'conformidade_percentual' => $itensTotal > 0
                    ? (int) round(($itensOk / $itensTotal) * 100)
                    : 0,
            ],
            'ultimos' => [
                'vistoria' => Vistoria::query()
                    ->where('condominio_id', $condominio->id)
                    ->latest('updated_at')
                    ->first(['id', 'codigo', 'status', 'updated_at']),
                'conflito' => ConflitoMorador::query()
                    ->where('condominio_id', $condominio->id)
                    ->latest('updated_at')
                    ->first(['id', 'protocolo', 'status', 'updated_at']),
                'ocorrencia' => OcorrenciaFuncionario::query()
                    ->where('condominio_id', $condominio->id)
                    ->latest('updated_at')
                    ->first(['id', 'protocolo', 'status', 'updated_at']),
            ],
        ]);
    }

    public function index(): View
    {
        $this->authorize('viewAny', Condominio::class);

        $user = auth()->user();
        $condominiosQuery = Condominio::query();

        if ($user && ! $user->isPlatformAdmin()) {
            $condominiosQuery->whereIn('id', $user->condominios()->pluck('condominios.id'));
        }

        $statusCounts = collect(
            (clone $condominiosQuery)
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->all()
        );

        $totalCondominios = (int) $statusCounts->sum();

        return view('condominios.index', [
            'condominios' => Condominio::query()
                ->when($user && ! $user->isPlatformAdmin(), function ($query) use ($user) {
                    $query->whereIn('id', $user->condominios()->pluck('condominios.id'));
                })
                ->orderBy('nome')
                ->paginate(15),
            'resumo' => [
                'total' => $totalCondominios,
                'ativos' => (int) $statusCounts->get('ativo', 0),
                'inativos' => (int) $statusCounts->get('inativo', 0),
                'suspensos' => (int) $statusCounts->get('suspenso', 0),
            ],
            'statusDistribuicao' => $this->statusDistribution(
                $statusCounts,
                ['ativo', 'inativo', 'suspenso'],
                ['ativo' => 'Ativo', 'inativo' => 'Inativo', 'suspenso' => 'Suspenso']
            ),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Condominio::class);

        return view('condominios.create', [
            'condominio' => new Condominio([
                'timezone' => 'America/Sao_Paulo',
                'status' => 'ativo',
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Condominio::class);

        $data = $this->validatedData($request);

        $condominio = Condominio::query()->create($data);
        $user = $request->user();

        if ($user && ! $user->canAccessCondominio((string) $condominio->id)) {
            $condominio->usuariosSistema()->syncWithoutDetaching([
                $user->id => ['role' => 'admin'],
            ]);
        }

        return redirect()
            ->route('condominios.context.dashboard', $condominio)
            ->with('success', 'Condomínio criado com sucesso. Próximo passo: cadastre a estrutura interna.');
    }

    public function edit(Condominio $condominio): View
    {
        $this->authorize('update', $condominio);

        return view('condominios.edit', [
            'condominio' => $condominio,
        ]);
    }

    public function update(Request $request, Condominio $condominio): RedirectResponse
    {
        $this->authorize('update', $condominio);

        $data = $this->validatedData($request, $condominio);

        $condominio->update($data);

        return redirect()
            ->route('condominios.index')
            ->with('success', 'Condomínio atualizado com sucesso.');
    }

    public function destroy(Condominio $condominio): RedirectResponse
    {
        $this->authorize('delete', $condominio);

        $condominio->delete();

        return redirect()
            ->route('condominios.index')
            ->with('success', 'Condomínio removido com sucesso.');
    }

    private function validatedData(Request $request, ?Condominio $condominio = null): array
    {
        $id = $condominio?->id;

        return $request->validate([
            'codigo' => ['required', 'string', 'max:20', Rule::unique('condominios', 'codigo')->ignore($id)],
            'nome' => ['required', 'string', 'max:150'],
            'cnpj' => ['nullable', 'string', 'max:14', Rule::unique('condominios', 'cnpj')->ignore($id)],
            'cep' => ['nullable', 'string', 'max:8'],
            'logradouro' => ['nullable', 'string', 'max:150'],
            'numero' => ['nullable', 'string', 'max:20'],
            'bairro' => ['nullable', 'string', 'max:100'],
            'cidade' => ['nullable', 'string', 'max:100'],
            'uf' => ['nullable', 'string', 'max:2'],
            'timezone' => ['required', 'string', 'max:60'],
            'status' => ['required', Rule::in(['ativo', 'inativo', 'suspenso'])],
        ]);
    }

    private function statusDistribution(Collection $counts, array $statusOrder, array $labelMap): array
    {
        $total = (int) $counts->sum();

        return collect($statusOrder)
            ->map(function (string $status) use ($counts, $labelMap, $total) {
                $value = (int) ($counts->get($status, 0));

                return [
                    'status' => $status,
                    'label' => $labelMap[$status] ?? ucfirst(str_replace('_', ' ', $status)),
                    'total' => $value,
                    'percentual' => $total > 0 ? (int) round(($value / $total) * 100) : 0,
                ];
            })
            ->values()
            ->all();
    }

    private function vistoriaTrend(string $condominioId, int $meses = 6): array
    {
        $inicio = now()->startOfMonth()->subMonths($meses - 1);
        $serie = [];

        for ($index = 0; $index < $meses; $index++) {
            $mes = $inicio->copy()->addMonths($index);
            $serie[$mes->format('Y-m')] = [
                'label' => $mes->format('m/y'),
                'valor' => 0,
            ];
        }

        $vistorias = Vistoria::query()
            ->where('condominio_id', $condominioId)
            ->where(function ($query) use ($inicio) {
                $query
                    ->whereDate('competencia', '>=', $inicio)
                    ->orWhereDate('created_at', '>=', $inicio);
            })
            ->get(['competencia', 'created_at']);

        foreach ($vistorias as $vistoria) {
            $data = $vistoria->competencia ?? $vistoria->created_at;

            if (! $data) {
                continue;
            }

            $chave = $data->copy()->startOfMonth()->format('Y-m');

            if (array_key_exists($chave, $serie)) {
                $serie[$chave]['valor']++;
            }
        }

        $labels = array_values(array_column($serie, 'label'));
        $valores = array_values(array_column($serie, 'valor'));

        return [
            'labels' => $labels,
            'valores' => $valores,
            'maximo' => max(array_merge([1], $valores)),
        ];
    }

    private function assertAccess(Condominio $condominio, bool $write = false): void
    {
        $ability = $write ? 'update' : 'view';
        $this->authorize($ability, $condominio);
    }
}
