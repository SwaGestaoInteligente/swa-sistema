@extends('layouts.app')

@section('title', 'Unidades | SWA')

@section('content')
    @php($indexRoute = route('condominios.context.unidades.index', $condominio))
    @php($createRoute = route('condominios.context.unidades.create', $condominio))

    <section class="browse-layout">
        <div class="browse-main">
            <section class="card stack browse-list-card">
                <div class="browse-header">
                    <div class="page-head" style="margin-bottom:0;">
                        <div>
                            <h1>Unidades</h1>
                            <div class="muted">Área de consulta para apartamentos e salas, separada do cadastro operacional.</div>
                        </div>
                        <a class="btn-primary" href="{{ $createRoute }}">Nova unidade</a>
                    </div>
                    <div class="browse-highlight">
                        <strong>Consulte antes de editar</strong>
                        Use os filtros por bloco e pavimento para localizar a unidade certa e evitar ajuste no endereço errado.
                    </div>
                </div>

                <form method="GET" action="{{ $indexRoute }}" class="form-grid" style="margin:14px 0;">
                    <div class="field">
                        <label for="numero">Número</label>
                        <input id="numero" name="numero" value="{{ $filters['numero'] ?? '' }}" placeholder="101">
                    </div>
                    <div class="field">
                        <label for="bloco_id">Bloco</label>
                        <select id="bloco_id" name="bloco_id">
                            <option value="">Todos</option>
                            @foreach ($blocos as $bloco)
                                <option value="{{ $bloco->id }}" @selected(($filters['bloco_id'] ?? '') === $bloco->id)>
                                    {{ $bloco->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="pavimento_id">Pavimento</label>
                        <select id="pavimento_id" name="pavimento_id">
                            <option value="">Todos</option>
                            @foreach ($pavimentos as $pavimento)
                                <option value="{{ $pavimento->id }}" data-bloco="{{ $pavimento->bloco_id }}" @selected(($filters['pavimento_id'] ?? '') === $pavimento->id)>
                                    {{ $pavimento->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="tipo">Tipo</label>
                        <select id="tipo" name="tipo">
                            <option value="">Todos</option>
                            <option value="apto" @selected(($filters['tipo'] ?? '') === 'apto')>Apartamento</option>
                            <option value="sala" @selected(($filters['tipo'] ?? '') === 'sala')>Sala</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="">Todos</option>
                            <option value="ocupado" @selected(($filters['status'] ?? '') === 'ocupado')>Ocupado</option>
                            <option value="vago" @selected(($filters['status'] ?? '') === 'vago')>Vago</option>
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
                                <th>Número</th>
                                <th>Bloco</th>
                                <th>Pavimento</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th style="width:200px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($unidades as $unidade)
                                <tr>
                                    <td>{{ $unidade->numero }}</td>
                                    <td>{{ $unidade->bloco?->nome ?? '-' }}</td>
                                    <td>{{ $unidade->pavimento?->nome ?? '-' }}</td>
                                    <td>{{ $unidade->tipo === 'apto' ? 'Apartamento' : 'Sala' }}</td>
                                    <td>{{ $unidade->status === 'ocupado' ? 'Ocupado' : 'Vago' }}</td>
                                    <td>
                                        <div class="actions">
                                            <a class="link-btn" href="{{ route('condominios.context.unidades.edit', ['condominio' => $condominio, 'unidade' => $unidade]) }}">Editar</a>
                                            <form method="POST" action="{{ route('condominios.context.unidades.destroy', ['condominio' => $condominio, 'unidade' => $unidade]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="link-btn link-danger" type="submit" onclick="return confirm('Excluir esta unidade?')">Excluir</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="muted">Nenhuma unidade cadastrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top:12px;">
                    {{ $unidades->links() }}
                </div>
            </section>
        </div>

        <aside class="browse-side">
            <section class="card browse-aside-card">
                <h2 class="panel-title">Leitura rápida</h2>
                <div class="insight-list">
                    <div class="insight-item">
                        <strong>Filtro encadeado</strong>
                        <span>Bloco e pavimento trabalham juntos para reduzir erro na busca.</span>
                    </div>
                    <div class="insight-item">
                        <strong>Base para conflitos</strong>
                        <span>Unidades corretas refletem direto nas telas de conflitos e histórico.</span>
                    </div>
                </div>
            </section>

            <section class="card browse-aside-card">
                <h2 class="panel-title">Atalhos</h2>
                <div class="entry-side-actions">
                    <a class="btn" href="{{ $createRoute }}">Cadastrar unidade</a>
                    <a class="btn" href="{{ route('condominios.context.pavimentos.index', $condominio) }}">Ver pavimentos</a>
                    <a class="btn" href="{{ route('condominios.context.ajuda', $condominio) }}">Ajuda desta área</a>
                </div>
            </section>
        </aside>
    </section>

    <script>
        (() => {
            const bloco = document.getElementById('bloco_id');
            const pavimento = document.getElementById('pavimento_id');

            if (!bloco || !pavimento) {
                return;
            }

            const refreshPavimentos = () => {
                const blocoId = bloco.value;
                [...pavimento.options].forEach((option, index) => {
                    if (index === 0) {
                        option.hidden = false;
                        return;
                    }
                    option.hidden = !!blocoId && option.dataset.bloco !== blocoId;
                });
            };

            bloco.addEventListener('change', refreshPavimentos);
            refreshPavimentos();
        })();
    </script>
@endsection
