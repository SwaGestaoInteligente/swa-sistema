@extends('layouts.app')

@section('title', 'Vistorias | SWA')

@section('content')
    @php($indexRoute = route('condominios.context.vistorias.index', $condominio))
    @php($createRoute = route('condominios.context.vistorias.create', $condominio))
    @php($wizardRoute = route('condominios.context.vistorias.wizard', $condominio))
    @php($reportsRoute = route('condominios.context.relatorios.index', $condominio))

    <section class="browse-layout">
        <div class="browse-main">
            <section class="card stack browse-list-card">
                <div class="browse-header">
                    <div class="page-head" style="margin-bottom:0;">
                        <div>
                            <h1>Vistorias</h1>
                            <div class="muted">Aqui fica a leitura operacional das vistorias. O cadastro e o modo campo entram pelos atalhos.</div>
                        </div>
                        <div class="dash-actions">
                            <a class="btn" href="{{ $wizardRoute }}">Modo campo</a>
                            <a class="btn" href="{{ $reportsRoute }}">Relatórios</a>
                            <a class="btn-primary" href="{{ $createRoute }}">Nova vistoria</a>
                        </div>
                    </div>

                    <article class="guide" style="margin:0;">
                        <h2>Como registrar uma vistoria</h2>
                        <p>1) clique em Nova vistoria, 2) abra Detalhes, 3) adicione itens com foto, 4) finalize e baixe o PDF.</p>
                    </article>
                </div>

                <form method="GET" action="{{ $indexRoute }}" class="form-grid" style="margin:14px 0;">
                    <div class="field">
                        <label for="codigo">Código</label>
                        <input id="codigo" name="codigo" value="{{ $filters['codigo'] ?? '' }}" placeholder="VIS-2026-0001">
                    </div>
                    <div class="field">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="">Todos</option>
                            @foreach ($statusList as $key => $label)
                                <option value="{{ $key }}" @selected(($filters['status'] ?? '') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="tipo">Tipo</label>
                        <select id="tipo" name="tipo">
                            <option value="">Todos</option>
                            @foreach ($tipos as $key => $label)
                                <option value="{{ $key }}" @selected(($filters['tipo'] ?? '') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="de">Competência de</label>
                        <input id="de" name="de" type="date" value="{{ $filters['de'] ?? '' }}">
                    </div>
                    <div class="field">
                        <label for="ate">Competência até</label>
                        <input id="ate" name="ate" type="date" value="{{ $filters['ate'] ?? '' }}">
                    </div>
                    <div class="field">
                        <label for="risco_min">Risco mínimo (%)</label>
                        <input id="risco_min" name="risco_min" type="number" min="0" max="100" value="{{ $filters['risco_min'] ?? '' }}">
                    </div>
                    <div class="field">
                        <label for="risco_max">Risco máximo (%)</label>
                        <input id="risco_max" name="risco_max" type="number" min="0" max="100" value="{{ $filters['risco_max'] ?? '' }}">
                    </div>
                    <div class="form-actions" style="grid-column:1/-1;margin-top:0;">
                        <a class="btn" href="{{ $indexRoute }}">Limpar</a>
                        <button class="btn-primary" type="submit">Filtrar</button>
                    </div>
                </form>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Área/Template</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Risco</th>
                                <th>Itens</th>
                                <th style="width:220px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($vistorias as $vistoria)
                                <tr>
                                    <td>{{ $vistoria->codigo }}</td>
                                    <td>
                                        <strong>{{ $vistoria->area?->nome ?? '-' }}</strong>
                                        <div class="muted" style="margin-top:2px;">
                                            {{ $vistoria->template?->nome ?? 'Sem template' }}
                                        </div>
                                    </td>
                                    <td>{{ str_replace('_', ' ', ucfirst($vistoria->tipo)) }}</td>
                                    <td>{{ str_replace('_', ' ', ucfirst($vistoria->status)) }}</td>
                                    <td>
                                        <span class="risk-badge risk-{{ $vistoria->risco_nivel }}">
                                            {{ $vistoria->risco_nivel_label }}
                                        </span>
                                    </td>
                                    <td>{{ $vistoria->itens_count }}</td>
                                    <td>
                                        <div class="actions actions-mobile">
                                            @if (in_array($vistoria->status, ['rascunho', 'em_andamento'], true))
                                                <a class="link-btn link-strong" href="{{ route('condominios.context.vistorias.show', ['condominio' => $condominio, 'vistoria' => $vistoria]) }}">Continuar</a>
                                            @endif
                                            <a class="link-btn" href="{{ route('condominios.context.vistorias.show', ['condominio' => $condominio, 'vistoria' => $vistoria]) }}">Detalhes</a>
                                            <a class="link-btn" href="{{ route('condominios.context.vistorias.pdf', ['condominio' => $condominio, 'vistoria' => $vistoria]) }}">Gerar PDF</a>
                                            @if (! in_array($vistoria->status, ['finalizada', 'cancelada'], true))
                                                <a class="link-btn" href="{{ route('condominios.context.vistorias.edit', ['condominio' => $condominio, 'vistoria' => $vistoria]) }}">Editar</a>
                                            @endif
                                            <form method="POST" action="{{ route('condominios.context.vistorias.destroy', ['condominio' => $condominio, 'vistoria' => $vistoria]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="link-btn link-danger" type="submit" onclick="return confirm('Remover esta vistoria?')" @disabled(in_array($vistoria->status, ['finalizada', 'cancelada'], true))>Excluir</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="muted">Nenhuma vistoria cadastrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top:12px;">
                    {{ $vistorias->links() }}
                </div>
            </section>
        </div>

        <aside class="browse-side">
            <section class="card browse-aside-card">
                <h2 class="panel-title">Leitura rápida</h2>
                <div class="insight-list">
                    <div class="insight-item">
                        <strong>Continue do ponto certo</strong>
                        <span>Use “Continuar” nas vistorias abertas e “Detalhes” para revisar evidências e pendências.</span>
                    </div>
                    <div class="insight-item">
                        <strong>Relatório em um clique</strong>
                        <span>Da própria linha você já consegue gerar o PDF sem abrir outra tela.</span>
                    </div>
                </div>
            </section>

            <section class="card browse-aside-card">
                <h2 class="panel-title">Atalhos</h2>
                <div class="entry-side-actions">
                    <a class="btn" href="{{ $wizardRoute }}">Abrir modo campo</a>
                    <a class="btn" href="{{ $createRoute }}">Cadastrar vistoria</a>
                    <a class="btn" href="{{ $reportsRoute }}">Ver relatórios</a>
                </div>
            </section>
        </aside>
    </section>
@endsection
