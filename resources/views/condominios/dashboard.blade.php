@extends('layouts.app')

@section('title', 'Painel do condomínio | SWA')

@section('content')
    <style>
    /* ═══════════════════════════════════════════════════
       DASHBOARD — campo-first mobile
       ═══════════════════════════════════════════════════ */

    /* ── Hero campo (mobile only) ── */
    .campo-hero {
        display: none; /* só mobile */
    }

    @media (max-width: 700px) {

        /* ── Oculta analytics e métricas no mobile ── */
        .metric-strip     { display: none !important; }
        .cockpit-grid     { display: none !important; }
        .guide            { display: none !important; }

        /* ── Hero painel: compacto no mobile ── */
        .hero-panel {
            border-radius: 14px;
            padding: 16px 14px 14px;
            margin-bottom: 0;
        }
        .hero-grid { display: block !important; }
        .hero-copy h1.hero-title { font-size: 20px; margin-bottom: 4px; }
        .hero-subtitle { font-size: 13px; line-height: 1.4; }
        .hero-side { display: none !important; }
        .hero-actions { display: none !important; }

        /* ── Bloco campo-hero: só aparece no mobile ── */
        .campo-hero {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 14px 0 0;
        }

        /* Botões de ação de campo grandes */
        .campo-cta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .campo-btn-primary {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 64px;
            background: #0f766e;
            color: #fff;
            font-size: 16px;
            font-weight: 800;
            border-radius: 14px;
            text-decoration: none;
            text-align: center;
            line-height: 1.2;
            padding: 10px 8px;
            border: none;
            cursor: pointer;
        }
        .campo-btn-secondary {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 64px;
            background: #f0fdf4;
            color: #0f766e;
            font-size: 15px;
            font-weight: 700;
            border-radius: 14px;
            border: 2px solid #0f766e;
            text-decoration: none;
            text-align: center;
            line-height: 1.2;
            padding: 10px 8px;
        }

        /* Card "continuar vistoria" */
        .campo-continuar {
            background: #fef9c3;
            border: 2px solid #f59e0b;
            border-radius: 14px;
            padding: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: inherit;
        }
        .campo-continuar-icon {
            font-size: 28px;
            line-height: 1;
            flex-shrink: 0;
        }
        .campo-continuar-body { flex: 1; min-width: 0; }
        .campo-continuar-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #92400e;
            margin-bottom: 2px;
        }
        .campo-continuar-code {
            font-size: 16px;
            font-weight: 800;
            color: #1e293b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .campo-continuar-meta {
            font-size: 12px;
            color: #78350f;
            margin-top: 2px;
        }
        .campo-continuar-arrow {
            font-size: 20px;
            color: #f59e0b;
            flex-shrink: 0;
        }

        /* Stats resumo mobile */
        .campo-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }
        .campo-stat {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 11px 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .campo-stat-value {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
        }
        .campo-stat-label {
            font-size: 12px;
            color: #64748b;
            line-height: 1.2;
        }

        /* Conformidade badge */
        .campo-conf {
            background: #f0fdf4;
            border: 1.5px solid #86efac;
            border-radius: 12px;
            padding: 11px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .campo-conf-label { font-size: 13px; color: #166534; }
        .campo-conf-value { font-size: 22px; font-weight: 800; color: #166534; }

        /* Links rápidos mobile */
        .campo-links {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }
        .campo-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            text-decoration: none;
            text-align: center;
            padding: 8px;
        }
        .campo-link:active { background: #e2e8f0; }

        /* grid-cards no mobile: só para cards secundários */
        .grid-cards .metric-card { border-radius: 10px; }

        /* Oculta cards de estrutura secundária no mobile */
        .grid-cards.dash-secondary { display: none !important; }
    }
    /* ═══ fim campo-first ═══════════════════════════════ */
    </style>

    <section class="stack">

        {{-- ── Hero (nome + subtítulo) ── --}}
        <article class="hero-panel">
            <div class="hero-grid">
                <div class="hero-copy">
                    <h1 class="hero-title">{{ $condominio->nome }}</h1>
                    <p class="hero-subtitle">
                        Codigo {{ $condominio->codigo }} · {{ $condominio->cidade ?: '-' }}/{{ $condominio->uf ?: '-' }}
                    </p>
                    {{-- Ações desktop (ocultas no mobile, substituídas pelo campo-hero) --}}
                    <div class="hero-actions">
                        <a class="btn" href="{{ route('condominios.context.vistorias.wizard', $condominio) }}">Assistente</a>
                        <a class="btn-primary" href="{{ route('condominios.context.vistorias.create', $condominio) }}">Nova vistoria</a>
                    </div>
                </div>
                <div class="hero-side">
                    <div class="hero-badge">
                        <span class="hero-badge-label">Conformidade</span>
                        <span class="hero-badge-value">{{ $qualidade['conformidade_percentual'] }}%</span>
                        <span class="muted">{{ $qualidade['itens_ok'] }} itens ok de {{ $qualidade['itens_total'] }}</span>
                    </div>
                    <div class="hero-badge">
                        <span class="hero-badge-label">Ultima vistoria</span>
                        <span class="hero-badge-value">{{ $ultimos['vistoria'] ? $ultimos['vistoria']->codigo : '-' }}</span>
                        <span class="muted">{{ $ultimos['vistoria'] ? $ultimos['vistoria']->updated_at?->format('d/m H:i') : 'Sem lancamento recente' }}</span>
                    </div>
                </div>
            </div>
        </article>

        {{-- ══════════════════════════════════════════
             CAMPO-HERO — visível só no mobile
             ══════════════════════════════════════════ --}}
        <div class="campo-hero">

            {{-- Continuar vistoria em andamento --}}
            @if ($ultimos['vistoria'] && ! in_array($ultimos['vistoria']->status, ['finalizada', 'cancelada'], true))
                <a
                    class="campo-continuar"
                    href="{{ route('condominios.context.vistorias.show', ['condominio' => $condominio, 'vistoria' => $ultimos['vistoria']]) }}"
                >
                    <div class="campo-continuar-icon">📋</div>
                    <div class="campo-continuar-body">
                        <div class="campo-continuar-label">Vistoria em andamento</div>
                        <div class="campo-continuar-code">{{ $ultimos['vistoria']->codigo }}</div>
                        <div class="campo-continuar-meta">
                            {{ $ultimos['vistoria']->updated_at?->format('d/m H:i') }}
                            @if ($ultimos['vistoria']->area)
                                · {{ $ultimos['vistoria']->area->nome }}
                            @endif
                        </div>
                    </div>
                    <div class="campo-continuar-arrow">›</div>
                </a>
            @endif

            {{-- CTAs de campo --}}
            <div class="campo-cta-grid">
                <a class="campo-btn-primary" href="{{ route('condominios.context.vistorias.create', $condominio) }}">
                    + Nova<br>Vistoria
                </a>
                <a class="campo-btn-secondary" href="{{ route('condominios.context.vistorias.wizard', $condominio) }}">
                    Assistente<br>de Campo
                </a>
            </div>

            {{-- Stats resumo --}}
            <div class="campo-stats">
                <div class="campo-stat">
                    <div>
                        <div class="campo-stat-value">{{ $stats['vistorias'] }}</div>
                        <div class="campo-stat-label">Vistorias</div>
                    </div>
                </div>
                <div class="campo-stat">
                    <div>
                        <div class="campo-stat-value">{{ $stats['areas'] }}</div>
                        <div class="campo-stat-label">Areas</div>
                    </div>
                </div>
                <div class="campo-stat">
                    <div>
                        <div class="campo-stat-value">{{ $stats['templates'] }}</div>
                        <div class="campo-stat-label">Templates</div>
                    </div>
                </div>
                <div class="campo-stat">
                    <div>
                        <div class="campo-stat-value">{{ $qualidade['itens_nao_ok'] }}</div>
                        <div class="campo-stat-label">Itens nao OK</div>
                    </div>
                </div>
            </div>

            {{-- Conformidade --}}
            <div class="campo-conf">
                <span class="campo-conf-label">Conformidade geral</span>
                <span class="campo-conf-value">{{ $qualidade['conformidade_percentual'] }}%</span>
            </div>

            {{-- Links rápidos --}}
            <div class="campo-links">
                <a class="campo-link" href="{{ route('condominios.context.vistorias.index', $condominio) }}">Lista de vistorias</a>
                <a class="campo-link" href="{{ route('condominios.context.relatorios.index', $condominio) }}">Relatorios</a>
                <a class="campo-link" href="{{ route('condominios.context.areas.index', $condominio) }}">Areas</a>
                <a class="campo-link" href="{{ route('condominios.context.templates.index', $condominio) }}">Templates</a>
                <a class="campo-link" href="{{ route('condominios.context.conflitos.index', $condominio) }}">Conflitos</a>
                <a class="campo-link" href="{{ route('condominios.context.ocorrencias.index', $condominio) }}">Ocorrencias</a>
                <a class="campo-link" href="{{ route('condominios.context.emails.index', $condominio) }}">E-mails</a>
                <a class="campo-link" href="{{ route('condominios.context.backups.index', $condominio) }}">Backups</a>
            </div>
        </div>

        {{-- ── Métricas de estrutura (desktop) ── --}}
        <div class="metric-strip">
            <article class="metric-card">
                <div class="metric-label">Blocos</div>
                <div class="metric-value">{{ $stats['blocos'] }}</div>
                <div class="metric-note">Estrutura vertical.</div>
            </article>
            <article class="metric-card">
                <div class="metric-label">Pavimentos</div>
                <div class="metric-value">{{ $stats['pavimentos'] }}</div>
                <div class="metric-note">Distribuidos por bloco.</div>
            </article>
            <article class="metric-card">
                <div class="metric-label">Unidades</div>
                <div class="metric-value">{{ $stats['unidades'] }}</div>
                <div class="metric-note">Apartamentos e salas.</div>
            </article>
            <article class="metric-card">
                <div class="metric-label">Areas</div>
                <div class="metric-value">{{ $stats['areas'] }}</div>
                <div class="metric-note">Pontos internos e externos.</div>
            </article>
        </div>

        <div class="grid-cards dash-secondary">
            <article class="metric-card">
                <div class="metric-label">Templates</div>
                <div class="metric-value">{{ $stats['templates'] }}</div>
                <div class="metric-note">Checklist padronizado.</div>
            </article>
            <article class="metric-card">
                <div class="metric-label">Vistorias</div>
                <div class="metric-value">{{ $stats['vistorias'] }}</div>
                <div class="metric-note">Campo e auditoria.</div>
            </article>
            <article class="metric-card">
                <div class="metric-label">Conflitos</div>
                <div class="metric-value">{{ $stats['conflitos'] }}</div>
                <div class="metric-note">Ocorrencias entre moradores.</div>
            </article>
            <article class="metric-card">
                <div class="metric-label">Ocorrencias</div>
                <div class="metric-value">{{ $stats['ocorrencias'] }}</div>
                <div class="metric-note">Historico de funcionarios.</div>
            </article>
            <article class="metric-card">
                <div class="metric-label">Relatorios</div>
                <div class="metric-value">{{ $stats['relatorios'] }}</div>
                <div class="metric-note">PDFs persistidos.</div>
            </article>
            <article class="metric-card">
                <div class="metric-label">E-mails</div>
                <div class="metric-value">{{ $stats['emails'] }}</div>
                <div class="metric-note">Destinatarios ativos.</div>
            </article>
        </div>

        {{-- ── Guia (desktop) ── --}}
        <article class="guide">
            <h2>Fluxo recomendado</h2>
            <p>Crie a base estrutural primeiro e depois execute a vistoria em modo campo no celular.</p>
            <div class="guide-steps">
                <div class="guide-step">
                    <div class="num">1</div>
                    <div class="title">Blocos e pavimentos</div>
                    <div class="text">Monte a estrutura do condominio.</div>
                    <a class="link" href="{{ route('condominios.context.blocos.index', $condominio) }}">Abrir estrutura</a>
                </div>
                <div class="guide-step">
                    <div class="num">2</div>
                    <div class="title">Unidades e areas</div>
                    <div class="text">Detalhe locais vistoriaveis.</div>
                    <a class="link" href="{{ route('condominios.context.unidades.index', $condominio) }}">Abrir unidades</a>
                </div>
                <div class="guide-step">
                    <div class="num">3</div>
                    <div class="title">Templates</div>
                    <div class="text">Padronize checklist por categoria.</div>
                    <a class="link" href="{{ route('condominios.context.templates.index', $condominio) }}">Abrir templates</a>
                </div>
                <div class="guide-step">
                    <div class="num">4</div>
                    <div class="title">Vistoria no campo</div>
                    <div class="text">Registre status, foto e observacao.</div>
                    <a class="link" href="{{ route('condominios.context.vistorias.wizard', $condominio) }}">Abrir assistente</a>
                </div>
                <div class="guide-step">
                    <div class="num">5</div>
                    <div class="title">Relatorio e envio</div>
                    <div class="text">Gere PDF e envie por link assinado.</div>
                    <a class="link" href="{{ route('condominios.context.relatorios.index', $condominio) }}">Abrir relatorios</a>
                </div>
            </div>
        </article>

        {{-- ── Analytics cockpit (desktop) ── --}}
        <div class="cockpit-grid">
            <article class="soft-panel">
                <h2 class="panel-title">Conformidade do checklist</h2>
                <div style="display:grid;grid-template-columns:auto 1fr;gap:12px;align-items:center;">
                    <div class="dash-ring" style="background: conic-gradient(#0f766e {{ $qualidade['conformidade_percentual'] }}%, #e6edf7 0);">
                        <div class="dash-ring-inner">{{ $qualidade['conformidade_percentual'] }}%</div>
                    </div>
                    <div class="status-rail">
                        <div class="status-item">
                            <div class="status-item-head">
                                <strong>Itens avaliados</strong>
                                <span>{{ $qualidade['itens_total'] }}</span>
                            </div>
                            <div class="muted">Base total analisada na operacao.</div>
                        </div>
                        <div class="status-item">
                            <div class="status-item-head">
                                <strong>Conformes</strong>
                                <span>{{ $qualidade['itens_ok'] }}</span>
                            </div>
                            <div class="muted">Itens sem pendencia critica.</div>
                        </div>
                        <div class="status-item">
                            <div class="status-item-head">
                                <strong>Pendentes</strong>
                                <span>{{ $qualidade['itens_nao_ok'] }}</span>
                            </div>
                            <div class="muted">Itens que exigem correcao ou evidencia.</div>
                        </div>
                    </div>
                </div>
            </article>

            <article class="soft-panel">
                <h2>Evolucao de vistorias (6 meses)</h2>
                <div class="dash-trend">
                    @foreach ($trend['labels'] as $index => $label)
                        @php
                            $valor = $trend['valores'][$index] ?? 0;
                            $altura = max(8, (int) round(($valor / $trend['maximo']) * 120));
                        @endphp
                        <div class="dash-trend-col">
                            <div class="dash-trend-value">{{ $valor }}</div>
                            <div class="dash-trend-bar" style="height:{{ $altura }}px;"></div>
                            <div class="dash-trend-label">{{ $label }}</div>
                        </div>
                    @endforeach
                </div>
            </article>
        </div>

        <div class="cockpit-grid">
            <article class="soft-panel">
                <h2 class="panel-title">Acoes rapidas</h2>
                <div class="quick-grid">
                    <a class="btn-primary quick-primary" href="{{ route('condominios.context.vistorias.create', $condominio) }}">Nova vistoria</a>

                    <div class="quick-group">
                        <strong>Estrutura</strong>
                        <div class="quick-links">
                            <a href="{{ route('condominios.context.blocos.create', $condominio) }}">Bloco</a>
                            <a href="{{ route('condominios.context.pavimentos.create', $condominio) }}">Pavimento</a>
                            <a href="{{ route('condominios.context.unidades.create', $condominio) }}">Unidade</a>
                            <a href="{{ route('condominios.context.areas.create', $condominio) }}">Area</a>
                        </div>
                    </div>

                    <div class="quick-group">
                        <strong>Registros</strong>
                        <div class="quick-links">
                            <a href="{{ route('condominios.context.templates.create', $condominio) }}">Template</a>
                            <a href="{{ route('condominios.context.conflitos.create', $condominio) }}">Conflito</a>
                            <a href="{{ route('condominios.context.ocorrencias.create', $condominio) }}">Ocorrencia</a>
                        </div>
                    </div>

                    <div class="quick-group">
                        <strong>Operacao</strong>
                        <div class="quick-links">
                            <a href="{{ route('condominios.context.vistorias.wizard', $condominio) }}">Assistente</a>
                            <a href="{{ route('condominios.context.backups.index', $condominio) }}">Backups</a>
                            <a href="{{ route('condominios.context.relatorios.index', $condominio) }}">Relatorios</a>
                        </div>
                    </div>
                </div>
            </article>

            <article class="soft-panel">
                <h2 class="panel-title">Ultimas atualizacoes</h2>
                <div class="insight-list">
                    <div class="insight-item">
                        <strong>Vistoria</strong>
                        @if ($ultimos['vistoria'])
                            <span>
                                {{ $ultimos['vistoria']->codigo }} | {{ str_replace('_', ' ', $ultimos['vistoria']->status) }}
                                | {{ $ultimos['vistoria']->updated_at?->format('d/m H:i') }}
                            </span>
                        @else
                            <span>Sem vistoria.</span>
                        @endif
                    </div>
                    <div class="insight-item">
                        <strong>Conflito</strong>
                        @if ($ultimos['conflito'])
                            <span>
                                {{ $ultimos['conflito']->protocolo }} | {{ str_replace('_', ' ', $ultimos['conflito']->status) }}
                                | {{ $ultimos['conflito']->updated_at?->format('d/m H:i') }}
                            </span>
                        @else
                            <span>Sem conflito.</span>
                        @endif
                    </div>
                    <div class="insight-item">
                        <strong>Ocorrencia</strong>
                        @if ($ultimos['ocorrencia'])
                            <span>
                                {{ $ultimos['ocorrencia']->protocolo }} | {{ str_replace('_', ' ', $ultimos['ocorrencia']->status) }}
                                | {{ $ultimos['ocorrencia']->updated_at?->format('d/m H:i') }}
                            </span>
                        @else
                            <span>Sem ocorrencia.</span>
                        @endif
                    </div>
                </div>
            </article>
        </div>
    </section>
@endsection
