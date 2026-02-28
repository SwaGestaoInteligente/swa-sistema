@extends('layouts.app')

@section('title', 'Blocos | SWA')

@section('content')
    @php($indexRoute = route('condominios.context.blocos.index', $condominio))
    @php($createRoute = route('condominios.context.blocos.create', $condominio))

    <section class="browse-layout">
        <div class="browse-main">
            <section class="card browse-list-card">
                <div class="browse-header">
                    <div class="page-head" style="margin-bottom:0;">
                        <div>
                            <h1>Blocos</h1>
                            <div class="muted">Consulte e filtre a estrutura principal antes de abrir novos cadastros.</div>
                        </div>
                        <a class="btn-primary" href="{{ $createRoute }}">Novo bloco</a>
                    </div>
                    <div class="browse-highlight">
                        <strong>Área de consulta</strong>
                        Use esta tela para localizar blocos existentes. Quando precisar cadastrar ou corrigir, entre pelo botão de ação.
                    </div>
                </div>

                <form method="GET" action="{{ $indexRoute }}" class="form-grid" style="margin:14px 0;">
                    <div class="field">
                        <label for="nome">Nome</label>
                        <input id="nome" name="nome" value="{{ $filters['nome'] ?? '' }}" placeholder="Ex.: Torre A">
                    </div>
                    <div class="field">
                        <label for="ativo">Status</label>
                        <select id="ativo" name="ativo">
                            <option value="">Todos</option>
                            <option value="1" @selected(($filters['ativo'] ?? '') === '1')>Ativo</option>
                            <option value="0" @selected(($filters['ativo'] ?? '') === '0')>Inativo</option>
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
                                <th>Código</th>
                                <th>Nome</th>
                                <th>Descrição</th>
                                <th>Ordem</th>
                                <th>Ativo</th>
                                <th style="width:240px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($blocos as $bloco)
                                <tr>
                                    <td>{{ $bloco->codigo }}</td>
                                    <td>{{ $bloco->nome }}</td>
                                    <td>{{ $bloco->descricao ?: '-' }}</td>
                                    <td>{{ $bloco->ordem }}</td>
                                    <td>{{ $bloco->ativo ? 'Sim' : 'Não' }}</td>
                                    <td>
                                        <div class="actions">
                                            <a class="link-btn" href="{{ route('condominios.context.pavimentos.index', ['condominio' => $condominio, 'bloco_id' => $bloco->id]) }}">Pavimentos</a>
                                            <a class="link-btn" href="{{ route('condominios.context.blocos.edit', ['condominio' => $condominio, 'bloco' => $bloco]) }}">Editar</a>
                                            <form method="POST" action="{{ route('condominios.context.blocos.destroy', ['condominio' => $condominio, 'bloco' => $bloco]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="link-btn link-danger" type="submit" onclick="return confirm('Remover este bloco?')">Excluir</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="muted">Nenhum bloco cadastrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top:12px;">
                    {{ $blocos->links() }}
                </div>
            </section>
        </div>

        <aside class="browse-side">
            <section class="card browse-aside-card">
                <h2 class="panel-title">Leitura rápida</h2>
                <div class="insight-list">
                    <div class="insight-item">
                        <strong>Blocos ativos</strong>
                        <span>Use o filtro de status para enxergar a estrutura válida antes de expandir a operação.</span>
                    </div>
                    <div class="insight-item">
                        <strong>Ponte para pavimentos</strong>
                        <span>O atalho “Pavimentos” evita abrir a tela errada na hora de continuar o cadastro.</span>
                    </div>
                </div>
            </section>

            <section class="card browse-aside-card">
                <h2 class="panel-title">Atalhos</h2>
                <div class="entry-side-actions">
                    <a class="btn" href="{{ $createRoute }}">Cadastrar novo bloco</a>
                    <a class="btn" href="{{ route('condominios.context.dashboard', $condominio) }}">Painel do condomínio</a>
                    <a class="btn" href="{{ route('condominios.context.ajuda', $condominio) }}">Ajuda desta área</a>
                </div>
            </section>
        </aside>
    </section>
@endsection
