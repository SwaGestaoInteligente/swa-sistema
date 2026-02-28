@extends('layouts.app')

@section('title', 'Painel | SWA')

@section('content')
    <section class="stack">
        <article class="hero-panel">
            <div class="hero-grid">
                <div class="hero-copy">
                    <span class="hero-eyebrow">Painel Central</span>
                    <h1 class="hero-title">Operacao pronta para celular e escritorio</h1>
                    <p class="hero-subtitle">
                        O SWA centraliza a base dos condominios, a execucao das vistorias e a entrega dos relatorios.
                        O objetivo aqui e simples: abrir o condominio certo e seguir o fluxo sem perder tempo.
                    </p>
                    <div class="hero-actions">
                        <a class="btn-primary" href="{{ route('condominios.index') }}">Abrir condominios</a>
                        <a class="btn" href="{{ route('condominios.create') }}">Novo condominio</a>
                        <a class="btn" href="{{ route('ajuda.index') }}">Como usar</a>
                    </div>
                </div>
                <div class="hero-side">
                    <div class="hero-badge">
                        <span class="hero-badge-label">Base ativa</span>
                        <span class="hero-badge-value">{{ $condominiosCount }}</span>
                        <span class="muted">condominios em operacao</span>
                    </div>
                    <div class="hero-badge">
                        <span class="hero-badge-label">Plataforma</span>
                        <span class="hero-badge-value">Online</span>
                        <span class="muted">deploy validado no Fly.io</span>
                    </div>
                </div>
            </div>
        </article>

        <div class="metric-strip">
            <article class="metric-card">
                <div class="metric-label">Condominios</div>
                <div class="metric-value">{{ $condominiosCount }}</div>
                <div class="metric-note">Base total disponivel para acesso.</div>
            </article>
            <article class="metric-card">
                <div class="metric-label">Usuarios</div>
                <div class="metric-value">{{ $usuariosCount }}</div>
                <div class="metric-note">Pessoas ligadas ao dominio SWA.</div>
            </article>
            <article class="metric-card">
                <div class="metric-label">Vistorias</div>
                <div class="metric-value">{{ $vistoriasCount }}</div>
                <div class="metric-note">Lancamentos ja registrados na operacao.</div>
            </article>
            <article class="metric-card">
                <div class="metric-label">Status</div>
                <div class="metric-value">100%</div>
                <div class="metric-note">Aplicacao publicada e respondendo.</div>
            </article>
        </div>

        @php($baseMax = max($condominiosCount, $usuariosCount, $vistoriasCount, 1))

        <article class="soft-panel">
            <div class="page-head" style="margin-bottom:0;">
                <div>
                    <h2 class="panel-title">Leitura visual da base</h2>
                    <div class="muted">Comparativo rapido entre volume de condominios, usuarios e vistorias.</div>
                </div>
                <span class="chip">Atualizacao ao vivo</span>
            </div>
            <div class="dash-trend" style="grid-template-columns: repeat(3, minmax(64px, 1fr)); margin-top: 14px;">
                <div class="dash-trend-col">
                    <div class="dash-trend-value">{{ $condominiosCount }}</div>
                    <div class="dash-trend-bar" style="height: {{ max(18, intval(($condominiosCount / $baseMax) * 140)) }}px;"></div>
                    <div class="dash-trend-label">Condominios</div>
                </div>
                <div class="dash-trend-col">
                    <div class="dash-trend-value">{{ $usuariosCount }}</div>
                    <div class="dash-trend-bar" style="height: {{ max(18, intval(($usuariosCount / $baseMax) * 140)) }}px; background: linear-gradient(180deg, #7f9dd5 0%, #1a6ea2 100%);"></div>
                    <div class="dash-trend-label">Usuarios</div>
                </div>
                <div class="dash-trend-col">
                    <div class="dash-trend-value">{{ $vistoriasCount }}</div>
                    <div class="dash-trend-bar" style="height: {{ max(18, intval(($vistoriasCount / $baseMax) * 140)) }}px; background: linear-gradient(180deg, #69bdb0 0%, #2f9c8f 100%);"></div>
                    <div class="dash-trend-label">Vistorias</div>
                </div>
            </div>
        </article>

        <div class="cockpit-grid">
            <article class="soft-panel">
                <h2 class="panel-title">Rota rapida para um usuario leigo</h2>
                <div class="insight-list">
                    <div class="insight-item">
                        <strong>1. Escolha o condominio</strong>
                        <span>Entre pela lista principal e selecione o ambiente correto.</span>
                    </div>
                    <div class="insight-item">
                        <strong>2. Cadastre a estrutura</strong>
                        <span>Blocos, pavimentos, unidades e areas precisam existir antes da vistoria.</span>
                    </div>
                    <div class="insight-item">
                        <strong>3. Execute em campo</strong>
                        <span>Use o assistente, fotos e observacoes para registrar evidencias.</span>
                    </div>
                    <div class="insight-item">
                        <strong>4. Gere e envie</strong>
                        <span>Finalize, emita o PDF e dispare o relatorio quando necessario.</span>
                    </div>
                </div>
            </article>

            <article class="soft-panel">
                <h2 class="panel-title">Radar operacional</h2>
                <div class="status-rail">
                    <div class="status-item">
                        <div class="status-item-head">
                            <strong>Pronto para campo</strong>
                            <span>{{ $vistoriasCount > 0 ? 'ativo' : 'preparar' }}</span>
                        </div>
                        <div class="muted">
                            {{ $vistoriasCount > 0 ? 'Ja existe historico de vistoria para validar em campo.' : 'Cadastre o primeiro roteiro de vistoria.' }}
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="status-item-head">
                            <strong>Backups internos</strong>
                            <span>ok</span>
                        </div>
                        <div class="muted">O modulo de backup ja esta publicado e pronto para uso.</div>
                    </div>
                    <div class="status-item">
                        <div class="status-item-head">
                            <strong>Suporte ao usuario</strong>
                            <span>ajuda</span>
                        </div>
                        <div class="muted">A central de ajuda interna explica o fluxo do inicio ao fim.</div>
                    </div>
                </div>
            </article>
        </div>
    </section>
@endsection
