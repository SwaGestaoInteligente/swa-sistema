@extends('layouts.app')

@section('title', 'Áreas | SWA')

@section('content')
    @php($indexRoute = route('condominios.context.areas.index', $condominio))
    @php($createRoute = route('condominios.context.areas.create', ['condominio' => $condominio]))

    <section class="browse-layout">
        <div class="browse-main">
            <section class="card browse-list-card">
                <div class="browse-header">
                    <div class="page-head" style="margin-bottom:0;">
                        <div>
                            <h1>Áreas</h1>
                            <div class="muted">Consulte ambientes internos e externos em uma área própria de leitura e filtro.</div>
                        </div>
                        <a class="btn-primary" href="{{ $createRoute }}">Nova área</a>
                    </div>
                    <div class="browse-highlight">
                        <strong>Mapa operacional</strong>
                        As áreas alimentam as vistorias. Mantenha esta lista organizada antes de lançar itens de campo.
                    </div>
                </div>

                <form method="GET" action="{{ $indexRoute }}" class="form-grid" style="margin:14px 0;">
                    <div class="field">
                        <label for="bloco_id">Bloco</label>
                        <select id="bloco_id" name="bloco_id">
                            <option value="">Todos</option>
                            @foreach ($blocos as $bloco)
                                <option value="{{ $bloco->id }}" @selected(($filters['bloco_id'] ?? '') === $bloco->id)>
                                    {{ $bloco->nome }} ({{ $bloco->codigo }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="pavimento_id">Pavimento</label>
                        <select id="pavimento_id" name="pavimento_id">
                            <option value="">Todos</option>
                            @foreach ($pavimentos as $pavimento)
                                <option value="{{ $pavimento->id }}" @selected(($filters['pavimento_id'] ?? '') === $pavimento->id)>
                                    {{ $pavimento->nome }} ({{ $pavimento->codigo }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="tipo">Tipo</label>
                        <select id="tipo" name="tipo">
                            <option value="">Todos</option>
                            <option value="externa" @selected(($filters['tipo'] ?? '') === 'externa')>Externa</option>
                            <option value="comum" @selected(($filters['tipo'] ?? '') === 'comum')>Comum</option>
                            <option value="tecnica" @selected(($filters['tipo'] ?? '') === 'tecnica')>Técnica</option>
                            <option value="seguranca" @selected(($filters['tipo'] ?? '') === 'seguranca')>Segurança</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="nome">Nome</label>
                        <input id="nome" name="nome" value="{{ $filters['nome'] ?? '' }}" placeholder="Ex.: Portaria principal">
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
                                <th>Bloco</th>
                                <th>Pavimento</th>
                                <th>Tipo</th>
                                <th>Código</th>
                                <th>Nome</th>
                                <th>Ativa</th>
                                <th style="width:160px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($areas as $area)
                                <tr>
                                    <td>{{ $area->bloco->nome ?? '-' }}</td>
                                    <td>{{ $area->pavimento->nome ?? '-' }}</td>
                                    <td>{{ ucfirst($area->tipo) }}</td>
                                    <td>{{ $area->codigo }}</td>
                                    <td>{{ $area->nome }}</td>
                                    <td>{{ $area->ativa ? 'Sim' : 'Não' }}</td>
                                    <td>
                                        <div class="actions">
                                            <a class="link-btn" href="{{ route('condominios.context.areas.edit', ['condominio' => $condominio, 'area' => $area]) }}">Editar</a>
                                            <form method="POST" action="{{ route('condominios.context.areas.destroy', ['condominio' => $condominio, 'area' => $area]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="link-btn link-danger" type="submit" onclick="return confirm('Remover esta área?')">Excluir</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="muted">Nenhuma área cadastrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top:12px;">
                    {{ $areas->links() }}
                </div>
            </section>
        </div>

        <aside class="browse-side">
            <section class="card browse-aside-card">
                <h2 class="panel-title">Leitura rápida</h2>
                <div class="insight-list">
                    <div class="insight-item">
                        <strong>Filtre por contexto</strong>
                        <span>Bloco, pavimento e tipo ajudam a manter a vistoria ligada ao local certo.</span>
                    </div>
                    <div class="insight-item">
                        <strong>Base da vistoria</strong>
                        <span>Sem área correta, o checklist em campo perde contexto e o PDF fica inconsistente.</span>
                    </div>
                </div>
            </section>

            <section class="card browse-aside-card">
                <h2 class="panel-title">Atalhos</h2>
                <div class="entry-side-actions">
                    <a class="btn" href="{{ $createRoute }}">Cadastrar área</a>
                    <a class="btn" href="{{ route('condominios.context.vistorias.index', $condominio) }}">Ver vistorias</a>
                    <a class="btn" href="{{ route('condominios.context.ajuda', $condominio) }}">Ajuda desta área</a>
                </div>
            </section>
        </aside>
    </section>
@endsection
