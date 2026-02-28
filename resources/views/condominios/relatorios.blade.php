@extends('layouts.app')

@section('title', 'Relatórios | SWA')

@section('content')
    <section class="browse-layout">
        <div class="browse-main">
            <section class="card stack browse-list-card">
                <div class="browse-header">
                    <div class="page-head" style="margin-bottom:0;">
                        <div>
                            <h1>Relatórios</h1>
                            <div class="muted">Geração, histórico e envio por e-mail em uma área de consulta separada das telas de cadastro.</div>
                        </div>
                        <a class="btn" href="{{ route('condominios.context.vistorias.index', $condominio) }}">Voltar para vistorias</a>
                    </div>
                    <div class="browse-highlight">
                        <strong>Área de consulta</strong>
                        Gere, baixe e envie PDFs daqui. O cadastro continua nas telas de vistoria e configuração de e-mails.
                    </div>
                </div>

                <article class="guide" style="margin-top:14px;">
                    <h2>Gerar novo PDF</h2>
                    <p>Escolha uma vistoria e gere o relatório completo.</p>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Status</th>
                                    <th>Atualizada</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($vistorias as $vistoria)
                                    <tr>
                                        <td>{{ $vistoria->codigo }}</td>
                                        <td>{{ str_replace('_', ' ', ucfirst($vistoria->status)) }}</td>
                                        <td>{{ $vistoria->updated_at?->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('condominios.context.relatorios.vistorias.gerar', ['condominio' => $condominio, 'vistoria' => $vistoria]) }}">
                                                @csrf
                                                <button class="link-btn link-strong" type="submit">Gerar PDF</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="muted">Nenhuma vistoria para gerar relatório.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>

                <article class="guide">
                    <h2>Histórico de relatórios</h2>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Arquivo</th>
                                    <th>Tipo</th>
                                    <th>Tamanho</th>
                                    <th>Gerado em</th>
                                    <th style="width:260px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($relatorios as $relatorio)
                                    <tr>
                                        <td>{{ $relatorio->file_name }}</td>
                                        <td>{{ strtoupper($relatorio->type) }}</td>
                                        <td>{{ number_format(($relatorio->size ?? 0) / 1024, 1, ',', '.') }} KB</td>
                                        <td>{{ $relatorio->created_at?->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="actions">
                                                <a class="link-btn" href="{{ route('condominios.context.relatorios.download', ['condominio' => $condominio, 'relatorio' => $relatorio]) }}">Baixar</a>
                                                @if ($emails->isNotEmpty())
                                                    <form method="POST" action="{{ route('condominios.context.relatorios.enviar', ['condominio' => $condominio, 'relatorio' => $relatorio]) }}">
                                                        @csrf
                                                        @foreach ($emails->take(1) as $email)
                                                            <input type="hidden" name="destinatarios[]" value="{{ $email->id }}">
                                                        @endforeach
                                                        <button class="link-btn" type="submit">Enviar p/ padrão</button>
                                                    </form>
                                                @endif
                                            </div>
                                            @if ($emails->isNotEmpty())
                                                <details style="margin-top:8px;">
                                                    <summary class="muted" style="cursor:pointer;">Envio avançado</summary>
                                                    <form method="POST" action="{{ route('condominios.context.relatorios.enviar', ['condominio' => $condominio, 'relatorio' => $relatorio]) }}" class="form-grid" style="margin-top:8px;">
                                                        @csrf
                                                        <div class="field" style="grid-column:1/-1;">
                                                            <label>Destinatários</label>
                                                            <div class="actions">
                                                                @foreach ($emails as $email)
                                                                    <label class="checkbox" style="margin:0;">
                                                                        <input type="checkbox" name="destinatarios[]" value="{{ $email->id }}">
                                                                        {{ $email->nome }} ({{ $email->email }})
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <div class="field" style="grid-column:1/-1;">
                                                            <label for="mensagem-{{ $relatorio->id }}">Mensagem</label>
                                                            <textarea id="mensagem-{{ $relatorio->id }}" name="mensagem" placeholder="Mensagem opcional para o e-mail"></textarea>
                                                        </div>
                                                        <label class="checkbox" style="grid-column:1/-1;">
                                                            <input type="checkbox" name="anexar_pdf" value="1">
                                                            Anexar PDF no e-mail (além do link seguro)
                                                        </label>
                                                        <div class="form-actions" style="grid-column:1/-1;margin-top:0;">
                                                            <button class="btn-primary" type="submit">Enviar</button>
                                                        </div>
                                                    </form>
                                                </details>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="muted">Nenhum relatório gerado ainda.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div style="margin-top:10px;">
                        {{ $relatorios->links() }}
                    </div>
                </article>
            </section>
        </div>

        <aside class="browse-side">
            <section class="card browse-aside-card">
                <h2 class="panel-title">Leitura rápida</h2>
                <div class="insight-list">
                    <div class="insight-item">
                        <strong>Gere do caso certo</strong>
                        <span>Use a primeira grade para disparar PDFs a partir das vistorias corretas.</span>
                    </div>
                    <div class="insight-item">
                        <strong>Envio controlado</strong>
                        <span>O histórico concentra download e e-mail sem precisar voltar para outras telas.</span>
                    </div>
                </div>
            </section>

            <section class="card browse-aside-card">
                <h2 class="panel-title">Atalhos</h2>
                <div class="entry-side-actions">
                    <a class="btn" href="{{ route('condominios.context.vistorias.index', $condominio) }}">Ver vistorias</a>
                    <a class="btn" href="{{ route('condominios.context.emails.index', $condominio) }}">Configurar e-mails</a>
                    <a class="btn" href="{{ route('condominios.context.ajuda', $condominio) }}">Ajuda desta área</a>
                </div>
            </section>
        </aside>
    </section>
@endsection
