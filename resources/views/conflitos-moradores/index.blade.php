@extends('layouts.app')

@section('title', 'Conflitos de moradores | SWA')

@section('content')
    @php($indexRoute = route('condominios.context.conflitos.index', $condominio))
    @php($newRoute = route('condominios.context.conflitos.create', $condominio))

    <section class="browse-layout">
        <div class="browse-main">
            <section class="card browse-list-card">
                <div class="browse-header">
                    <div class="page-head" style="margin-bottom:0;">
                        <div>
                            <h1>Conflitos de moradores</h1>
                            <div class="muted">Consulte histórico, filtre por tipo/status e só entre em edição quando necessário.</div>
                        </div>
                        <a class="btn-primary" href="{{ $newRoute }}">Novo conflito</a>
                    </div>
                    <div class="browse-highlight">
                        <strong>Área de consulta</strong>
                        Esta lista organiza ocorrências entre moradores e evita abrir cadastro novo sem confirmar se já existe protocolo em andamento.
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
                                <th>Unidade</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Moradores</th>
                                <th>Anexos</th>
                                <th>Ocorrido em</th>
                                <th style="width:240px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($conflitos as $conflito)
                                <tr>
                                    <td>{{ $conflito->protocolo }}</td>
                                    <td>{{ $conflito->unidade->numero ?? $conflito->unidade ?? '-' }}</td>
                                    <td>{{ $tipos[$conflito->tipo] ?? $conflito->tipo }}</td>
                                    <td>{{ $statusList[$conflito->status] ?? $conflito->status }}</td>
                                    <td>{{ $conflito->morador_a_nome ?: '-' }} x {{ $conflito->morador_b_nome ?: '-' }}</td>
                                    <td>{{ $conflito->anexos->count() }}</td>
                                    <td>{{ optional($conflito->ocorrido_em)->format('d/m/Y H:i') ?: '-' }}</td>
                                    <td>
                                        <div class="actions">
                                            <a class="link-btn" href="{{ route('condominios.context.conflitos.edit', ['condominio' => $condominio, 'conflito' => $conflito]) }}">Editar</a>
                                            <form method="POST" action="{{ route('condominios.context.conflitos.destroy', ['condominio' => $condominio, 'conflito' => $conflito]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="link-btn link-danger" type="submit" onclick="return confirm('Remover este conflito?')">Excluir</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="muted">Nenhum conflito registrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top:12px;">
                    {{ $conflitos->links() }}
                </div>
            </section>
        </div>

        <aside class="browse-side">
            <section class="card browse-aside-card">
                <h2 class="panel-title">Leitura rápida</h2>
                <div class="insight-list">
                    <div class="insight-item">
                        <strong>Consulte o protocolo</strong>
                        <span>Evite duplicidade verificando se o caso já está aberto ou em mediação.</span>
                    </div>
                    <div class="insight-item">
                        <strong>Revise anexos</strong>
                        <span>A coluna de anexos ajuda a identificar casos que ainda precisam de evidência.</span>
                    </div>
                </div>
            </section>

            <section class="card browse-aside-card">
                <h2 class="panel-title">Atalhos</h2>
                <div class="entry-side-actions">
                    <a class="btn" href="{{ $newRoute }}">Registrar conflito</a>
                    <a class="btn" href="{{ route('condominios.context.unidades.index', $condominio) }}">Ver unidades</a>
                    <a class="btn" href="{{ route('condominios.context.ajuda', $condominio) }}">Ajuda desta área</a>
                </div>
            </section>
        </aside>
    </section>
@endsection
