@extends('layouts.app')

@section('title', 'Ocorrências de funcionários | SWA')

@section('content')
    @php($indexRoute = route('condominios.context.ocorrencias.index', $condominio))
    @php($newRoute = route('condominios.context.ocorrencias.create', $condominio))

    <section class="browse-layout">
        <div class="browse-main">
            <section class="card browse-list-card">
                <div class="browse-header">
                    <div class="page-head" style="margin-bottom:0;">
                        <div>
                            <h1>Ocorrências de funcionários</h1>
                            <div class="muted">Consulta estruturada para histórico disciplinar e acompanhamento operacional.</div>
                        </div>
                        <a class="btn-primary" href="{{ $newRoute }}">Nova ocorrência</a>
                    </div>
                    <div class="browse-highlight">
                        <strong>Área de consulta</strong>
                        Use os filtros para localizar protocolos e evitar abertura duplicada antes de registrar uma nova ocorrência.
                    </div>
                </div>

                <form method="GET" action="{{ $indexRoute }}" class="form-grid" style="margin:14px 0;">
                    <div class="field">
                        <label for="protocolo">Protocolo</label>
                        <input id="protocolo" name="protocolo" value="{{ $filters['protocolo'] ?? '' }}">
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
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="">Todos</option>
                            @foreach ($statusList as $key => $label)
                                <option value="{{ $key }}" @selected(($filters['status'] ?? '') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
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
                                <th>Protocolo</th>
                                <th>Funcionário</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Anexos</th>
                                <th>Ocorrido em</th>
                                <th style="width:240px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ocorrencias as $ocorrencia)
                                <tr>
                                    <td>{{ $ocorrencia->protocolo }}</td>
                                    <td>{{ $ocorrencia->funcionario_nome ?: '-' }}</td>
                                    <td>{{ $tipos[$ocorrencia->tipo] ?? $ocorrencia->tipo }}</td>
                                    <td>{{ $statusList[$ocorrencia->status] ?? $ocorrencia->status }}</td>
                                    <td>{{ $ocorrencia->anexos->count() }}</td>
                                    <td>{{ optional($ocorrencia->ocorrido_em)->format('d/m/Y H:i') ?: '-' }}</td>
                                    <td>
                                        <div class="actions">
                                            <a class="link-btn" href="{{ route('condominios.context.ocorrencias.edit', ['condominio' => $condominio, 'ocorrencia' => $ocorrencia]) }}">Editar</a>
                                            <form method="POST" action="{{ route('condominios.context.ocorrencias.destroy', ['condominio' => $condominio, 'ocorrencia' => $ocorrencia]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="link-btn link-danger" type="submit" onclick="return confirm('Remover esta ocorrência?')">Excluir</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="muted">Nenhuma ocorrência registrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top:12px;">
                    {{ $ocorrencias->links() }}
                </div>
            </section>
        </div>

        <aside class="browse-side">
            <section class="card browse-aside-card">
                <h2 class="panel-title">Leitura rápida</h2>
                <div class="insight-list">
                    <div class="insight-item">
                        <strong>Consulte o histórico</strong>
                        <span>Filtros e anexos ajudam a revisar reincidência antes de aplicar nova medida.</span>
                    </div>
                    <div class="insight-item">
                        <strong>Registro formal</strong>
                        <span>Esta lista deve servir como consulta; a edição fica restrita ao caso correto.</span>
                    </div>
                </div>
            </section>

            <section class="card browse-aside-card">
                <h2 class="panel-title">Atalhos</h2>
                <div class="entry-side-actions">
                    <a class="btn" href="{{ $newRoute }}">Registrar ocorrência</a>
                    <a class="btn" href="{{ route('condominios.context.dashboard', $condominio) }}">Painel do condomínio</a>
                    <a class="btn" href="{{ route('condominios.context.ajuda', $condominio) }}">Ajuda desta área</a>
                </div>
            </section>
        </aside>
    </section>
@endsection
