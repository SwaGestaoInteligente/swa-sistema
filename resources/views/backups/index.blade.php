@extends('layouts.app')

@section('title', 'Backups | SWA')

@section('content')
    <section class="browse-layout">
        <div class="browse-main">
            <section class="card stack browse-list-card">
                <div class="browse-header">
                    <div class="page-head" style="margin-bottom:0;">
                        <div>
                            <h1>Backups do condomínio</h1>
                            <div class="muted">
                                Gere snapshots completos para contingência e auditoria.
                            </div>
                        </div>
                        <div class="dash-actions">
                            <form method="POST" action="{{ route('condominios.context.backups.store', $condominio) }}">
                                @csrf
                                <button class="btn-primary" type="submit">Gerar novo backup</button>
                            </form>
                        </div>
                    </div>

                    <article class="guide" style="margin:0;">
                        <h2>O que este backup inclui?</h2>
                        <p>Dados do condomínio + estrutura + vistorias + anexos/relatórios vinculados quando os arquivos existem no disco.</p>
                        <div class="guide-steps">
                            <div class="guide-step">
                                <div class="num">1</div>
                                <div class="title">Gerar</div>
                                <div class="text">Clique em "Gerar novo backup".</div>
                            </div>
                            <div class="guide-step">
                                <div class="num">2</div>
                                <div class="title">Baixar</div>
                                <div class="text">Use "Baixar" para salvar o arquivo .zip.</div>
                            </div>
                            <div class="guide-step">
                                <div class="num">3</div>
                                <div class="title">Guardar</div>
                                <div class="text">Mantenha cópia em local seguro (nuvem externa).</div>
                            </div>
                        </div>
                    </article>
                </div>

                <div class="table-wrap" style="margin-top:14px;">
                    <table>
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Arquivo</th>
                                <th>Tamanho</th>
                                <th>Status</th>
                                <th>Gerado por</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($backups as $backup)
                                <tr>
                                    <td>{{ $backup->created_at?->format('d/m/Y H:i') }}</td>
                                    <td>{{ $backup->file_name }}</td>
                                    <td>{{ number_format(($backup->size ?? 0) / 1024, 1, ',', '.') }} KB</td>
                                    <td>{{ ucfirst($backup->status) }}</td>
                                    <td>{{ $backup->generatedBy?->name ?? '-' }}</td>
                                    <td>
                                        <div class="actions">
                                            <a class="link-btn link-strong" href="{{ route('condominios.context.backups.download', ['condominio' => $condominio, 'backup' => $backup]) }}">
                                                Baixar
                                            </a>
                                            <form method="POST" action="{{ route('condominios.context.backups.destroy', ['condominio' => $condominio, 'backup' => $backup]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="link-btn link-danger" type="submit" onclick="return confirm('Remover este backup?')">Excluir</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="muted">Nenhum backup gerado ainda.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <aside class="browse-side">
            <section class="card browse-aside-card">
                <h2 class="panel-title">Leitura rápida</h2>
                <div class="insight-list">
                    <div class="insight-item">
                        <strong>Gere antes de mudar</strong>
                        <span>Use esta área como rotina padrão antes de qualquer ajuste estrutural.</span>
                    </div>
                    <div class="insight-item">
                        <strong>Baixe e guarde</strong>
                        <span>O backup só te protege de fato quando o .zip sai do servidor e fica salvo em local externo.</span>
                    </div>
                </div>
            </section>

            <section class="card browse-aside-card">
                <h2 class="panel-title">Atalhos</h2>
                <div class="entry-side-actions">
                    <form method="POST" action="{{ route('condominios.context.backups.store', $condominio) }}">
                        @csrf
                        <button class="btn-primary" type="submit">Gerar backup agora</button>
                    </form>
                    <a class="btn" href="{{ route('condominios.context.dashboard', $condominio) }}">Painel do condomínio</a>
                    <a class="btn" href="{{ route('condominios.context.ajuda', $condominio) }}">Ajuda desta área</a>
                </div>
            </section>
        </aside>
    </section>
@endsection
