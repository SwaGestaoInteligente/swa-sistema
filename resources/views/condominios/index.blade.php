@extends('layouts.app')

@section('title', 'Condomínios | SWA')

@section('content')
    <section class="stack">
        <article class="hero-panel">
            <div class="hero-grid">
                <div class="hero-copy">
                    <span class="hero-eyebrow">Lista raiz</span>
                    <h1 class="hero-title">Condominios em contexto unico</h1>
                    <p class="hero-subtitle">
                        Esta e a porta de entrada do sistema. Primeiro voce escolhe o condominio e, so depois,
                        navega pelos modulos internos daquele contexto.
                    </p>
                    <div class="hero-actions">
                        <a class="btn-primary" href="{{ route('condominios.create') }}">Novo condominio</a>
                        <a class="btn" href="{{ route('ajuda.index') }}">Fluxo de uso</a>
                    </div>
                </div>
                <div class="hero-side">
                    <div class="hero-badge">
                        <span class="hero-badge-label">Ativos</span>
                        <span class="hero-badge-value">{{ $resumo['ativos'] }}</span>
                        <span class="muted">operando normalmente</span>
                    </div>
                    <div class="hero-badge">
                        <span class="hero-badge-label">Total</span>
                        <span class="hero-badge-value">{{ $resumo['total'] }}</span>
                        <span class="muted">condominios cadastrados</span>
                    </div>
                </div>
            </div>
        </article>

        <div class="metric-strip">
            <article class="metric-card">
                <div class="metric-label">Total</div>
                <div class="metric-value">{{ $resumo['total'] }}</div>
                <div class="metric-note">Condominios cadastrados na base.</div>
            </article>
            <article class="metric-card">
                <div class="metric-label">Ativos</div>
                <div class="metric-value">{{ $resumo['ativos'] }}</div>
                <div class="metric-note">Operacao liberada.</div>
            </article>
            <article class="metric-card">
                <div class="metric-label">Inativos</div>
                <div class="metric-value">{{ $resumo['inativos'] }}</div>
                <div class="metric-note">Sem uso ou aguardando retomada.</div>
            </article>
            <article class="metric-card">
                <div class="metric-label">Suspensos</div>
                <div class="metric-value">{{ $resumo['suspensos'] }}</div>
                <div class="metric-note">Revisar contrato e acesso.</div>
            </article>
        </div>

        <article class="soft-panel">
            <h2>Distribuicao por status</h2>
            <div class="dash-progress-list">
                @foreach ($statusDistribuicao as $item)
                    <div class="dash-progress-row">
                        <div class="dash-progress-head">
                            <strong>{{ $item['label'] }}</strong>
                            <span>{{ $item['total'] }} ({{ $item['percentual'] }}%)</span>
                        </div>
                        <div class="dash-progress-track">
                            <div class="dash-progress-fill" style="width: {{ $item['percentual'] }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </article>

        <section class="card stack">
            <div class="page-head">
                <div>
                    <h1>Carteira de condominios</h1>
                    <div class="muted">Entre no contexto certo para continuar o trabalho.</div>
                </div>
            </div>

            <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>Cidade/UF</th>
                        <th>Status</th>
                        <th style="width:240px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($condominios as $condominio)
                        <tr>
                            <td>{{ $condominio->codigo }}</td>
                            <td>{{ $condominio->nome }}</td>
                            <td>{{ $condominio->cidade ?: '-' }}/{{ $condominio->uf ?: '-' }}</td>
                            <td>{{ ucfirst($condominio->status) }}</td>
                            <td>
                                <div class="actions">
                                    <a class="link-btn link-strong" href="{{ route('condominios.context.dashboard', $condominio) }}">Entrar</a>
                                    <a class="link-btn" href="{{ route('condominios.edit', $condominio) }}">Editar</a>
                                    <form method="POST" action="{{ route('condominios.destroy', $condominio) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="link-btn link-danger" type="submit" onclick="return confirm('Remover este condomínio?')">Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="muted">Nenhum condomínio cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>

            <div style="margin-top:12px;">
                {{ $condominios->links() }}
            </div>
        </section>
    </section>
@endsection
