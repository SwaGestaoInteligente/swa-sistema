@extends('layouts.app')

@section('title', 'Assistente de vistoria | SWA')

@section('content')
    <section class="stack">
        <article class="field-hero">
            <div class="field-hero-grid">
                <div>
                    <div class="field-badge-row">
                        <span class="field-badge">Modo campo</span>
                        <span class="field-badge">Fluxo guiado</span>
                        <span class="field-badge">Uso no celular</span>
                    </div>
                    <h1 class="field-hero-title">Assistente de vistoria</h1>
                    <p class="field-hero-subtitle">
                        Condominio {{ $condominio->nome }}. Esta tela foi organizada para um usuario leigo seguir o processo sem se perder:
                        preparar a base, abrir a vistoria, registrar evidencia e concluir com relatorio.
                    </p>
                    <div class="hero-actions" style="margin-top:14px;">
                        <a class="btn-primary" href="{{ route('condominios.context.vistorias.create', $condominio) }}">Nova vistoria</a>
                        <a class="btn" href="{{ route('condominios.context.vistorias.index', $condominio) }}">Ver vistorias</a>
                        <a class="btn" href="{{ route('condominios.context.ajuda', $condominio) }}">Ajuda</a>
                    </div>
                </div>
                <div class="field-hero-side">
                    <div class="field-hero-metric">
                        <strong>Blocos prontos</strong>
                        <span>{{ $estrutura['blocos'] }}</span>
                    </div>
                    <div class="field-hero-metric">
                        <strong>Pavimentos prontos</strong>
                        <span>{{ $estrutura['pavimentos'] }}</span>
                    </div>
                    <div class="field-hero-metric">
                        <strong>Areas prontas</strong>
                        <span>{{ $estrutura['areas'] }}</span>
                    </div>
                </div>
            </div>
        </article>

        <section class="field-step-grid">
            <article class="field-step-card">
                <div class="field-step-top">
                    <div class="field-step-num">1</div>
                    <h2 class="field-step-title">Confirme a base</h2>
                </div>
                <p class="field-step-text">Antes de vistoriar, valide se a estrutura minima do condominio existe e esta correta.</p>
                <div class="field-mini-grid">
                    <div class="field-mini-card">
                        <strong>{{ $estrutura['blocos'] }} blocos</strong>
                        <span>Cadastre torres e setores.</span>
                    </div>
                    <div class="field-mini-card">
                        <strong>{{ $estrutura['pavimentos'] }} pavimentos</strong>
                        <span>Organize a estrutura vertical por bloco.</span>
                    </div>
                    <div class="field-mini-card">
                        <strong>{{ $estrutura['areas'] }} areas</strong>
                        <span>Inclua portaria, garagem, escadas e demais pontos de inspeção.</span>
                    </div>
                </div>
                <div class="entry-side-actions">
                    <a class="btn" href="{{ route('condominios.context.blocos.index', $condominio) }}">Abrir blocos</a>
                    <a class="btn" href="{{ route('condominios.context.pavimentos.index', $condominio) }}">Abrir pavimentos</a>
                    <a class="btn" href="{{ route('condominios.context.areas.index', $condominio) }}">Abrir areas</a>
                </div>
            </article>

            <article class="field-step-card">
                <div class="field-step-top">
                    <div class="field-step-num">2</div>
                    <h2 class="field-step-title">Abra a vistoria</h2>
                </div>
                <p class="field-step-text">Crie a vistoria com area e template certos. Isso prepara o checklist para a execucao em campo.</p>
                <div class="field-mini-grid">
                    <div class="field-mini-card">
                        <strong>Area correta</strong>
                        <span>Garante que o item fique vinculado ao local certo no relatorio.</span>
                    </div>
                    <div class="field-mini-card">
                        <strong>Template certo</strong>
                        <span>Evita recriar itens e padroniza a inspeção.</span>
                    </div>
                </div>
                <div class="entry-side-actions">
                    <a class="btn-primary" href="{{ route('condominios.context.vistorias.create', $condominio) }}">Criar vistoria</a>
                    <a class="btn" href="{{ route('condominios.context.templates.index', $condominio) }}">Ver templates</a>
                </div>
            </article>

            <article class="field-step-card">
                <div class="field-step-top">
                    <div class="field-step-num">3</div>
                    <h2 class="field-step-title">Registre a evidencia</h2>
                </div>
                <p class="field-step-text">Durante a inspeção, selecione o status e use a camera do celular para registrar as fotos no mesmo momento.</p>
                <div class="field-mini-grid">
                    <div class="field-mini-card">
                        <strong>Status claro</strong>
                        <span>OK, Danificado, Ausente ou Improvisado.</span>
                    </div>
                    <div class="field-mini-card">
                        <strong>Regra obrigatoria</strong>
                        <span>Se nao for OK, foto e observacao sao obrigatorias.</span>
                    </div>
                    <div class="field-mini-card">
                        <strong>Atalhos rapidos</strong>
                        <span>Use os chips para preencher observacoes em poucos toques.</span>
                    </div>
                </div>
            </article>
        </section>

        <article class="guide">
            <h2>Vistorias abertas para continuar</h2>
            <p>Retome exatamente de onde parou. Esta area evita abrir uma vistoria nova sem necessidade.</p>

            @if ($abertas->isEmpty())
                <div class="muted" style="margin-top:10px;">Nenhuma vistoria aberta no momento.</div>
            @else
                <div class="field-open-list" style="margin-top:12px;">
                    @foreach ($abertas as $vistoria)
                        <article class="field-open-card">
                            <div class="field-open-head">
                                <div>
                                    <div style="font-size:18px;font-weight:800;color:#0f2144;">{{ $vistoria->codigo }}</div>
                                    <div class="muted" style="margin-top:3px;">Atualizada em {{ $vistoria->updated_at?->format('d/m H:i') }}</div>
                                </div>
                                <a class="btn-primary" href="{{ route('condominios.context.vistorias.show', ['condominio' => $condominio, 'vistoria' => $vistoria]) }}">Continuar</a>
                            </div>
                            <div class="field-open-meta">
                                <div class="meta-box">
                                    <strong>Status</strong>
                                    <span>{{ str_replace('_', ' ', ucfirst($vistoria->status)) }}</span>
                                </div>
                                <div class="meta-box">
                                    <strong>Risco</strong>
                                    <span>{{ $vistoria->risco_nivel_label }}</span>
                                </div>
                                <div class="meta-box">
                                    <strong>Acao</strong>
                                    <span>Retomar execucao</span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </article>

        <section class="field-step-grid" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
            <article class="field-step-card">
                <div class="field-step-top">
                    <div class="field-step-num">4</div>
                    <h2 class="field-step-title">Revise pendencias</h2>
                </div>
                <p class="field-step-text">Antes de fechar, confirme se nenhum item nao OK ficou sem foto ou sem observacao.</p>
                <div class="entry-side-actions">
                    <a class="btn" href="{{ route('condominios.context.vistorias.index', $condominio) }}">Abrir lista de vistorias</a>
                </div>
            </article>

            <article class="field-step-card">
                <div class="field-step-top">
                    <div class="field-step-num">5</div>
                    <h2 class="field-step-title">Feche e entregue</h2>
                </div>
                <p class="field-step-text">Com pendencias zeradas, finalize a vistoria, gere o PDF e envie o relatorio para os destinatarios certos.</p>
                <div class="entry-side-actions">
                    <a class="btn" href="{{ route('condominios.context.relatorios.index', $condominio) }}">Abrir central de relatorios</a>
                    <a class="btn" href="{{ route('condominios.context.emails.index', $condominio) }}">Configurar e-mails</a>
                </div>
            </article>
        </section>
    </section>
@endsection
