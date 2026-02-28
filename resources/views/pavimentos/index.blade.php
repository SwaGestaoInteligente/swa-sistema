@extends('layouts.app')

@section('title', 'Pavimentos | SWA')

@section('content')
    @php($indexRoute = route('condominios.context.pavimentos.index', $condominio))
    @php($createRoute = route('condominios.context.pavimentos.create', $condominio))

    <section class="browse-layout">
        <div class="browse-main">
            <section class="card browse-list-card">
                <div class="browse-header">
                    <div class="page-head" style="margin-bottom:0;">
                        <div>
                            <h1>Pavimentos</h1>
                            <div class="muted">Filtre por bloco e mantenha a leitura da estrutura vertical em uma área separada do cadastro.</div>
                        </div>
                        <a class="btn-primary" href="{{ $createRoute }}">Novo pavimento</a>
                    </div>
                    <div class="browse-highlight">
                        <strong>Área de consulta</strong>
                        Confira a hierarquia do condomínio aqui. Use o botão de cadastro apenas quando precisar incluir ou ajustar um pavimento.
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
                        <label for="nome">Nome</label>
                        <input id="nome" name="nome" value="{{ $filters['nome'] ?? '' }}" placeholder="Ex.: 1º andar">
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
                                <th>Bloco</th>
                                <th>Código</th>
                                <th>Nome</th>
                                <th>Nível</th>
                                <th>Ordem</th>
                                <th>Ativo</th>
                                <th style="width:240px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pavimentos as $pavimento)
                                <tr>
                                    <td>{{ $pavimento->bloco->nome ?? '-' }}</td>
                                    <td>{{ $pavimento->codigo }}</td>
                                    <td>{{ $pavimento->nome }}</td>
                                    <td>{{ $pavimento->nivel }}</td>
                                    <td>{{ $pavimento->ordem }}</td>
                                    <td>{{ $pavimento->ativo ? 'Sim' : 'Não' }}</td>
                                    <td>
                                        <div class="actions">
                                            <a class="link-btn" href="{{ route('condominios.context.areas.index', ['condominio' => $condominio, 'bloco_id' => $pavimento->bloco_id, 'pavimento_id' => $pavimento->id]) }}">Áreas</a>
                                            <a class="link-btn" href="{{ route('condominios.context.pavimentos.edit', ['condominio' => $condominio, 'pavimento' => $pavimento]) }}">Editar</a>
                                            <form method="POST" action="{{ route('condominios.context.pavimentos.destroy', ['condominio' => $condominio, 'pavimento' => $pavimento]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="link-btn link-danger" type="submit" onclick="return confirm('Remover este pavimento?')">Excluir</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="muted">Nenhum pavimento cadastrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top:12px;">
                    {{ $pavimentos->links() }}
                </div>
            </section>
        </div>

        <aside class="browse-side">
            <section class="card browse-aside-card">
                <h2 class="panel-title">Leitura rápida</h2>
                <div class="insight-list">
                    <div class="insight-item">
                        <strong>Filtre por bloco</strong>
                        <span>Isso reduz erro operacional quando o condomínio tem muitas torres.</span>
                    </div>
                    <div class="insight-item">
                        <strong>Ponte para áreas</strong>
                        <span>Use o atalho “Áreas” para continuar a configuração do mesmo local.</span>
                    </div>
                </div>
            </section>

            <section class="card browse-aside-card">
                <h2 class="panel-title">Atalhos</h2>
                <div class="entry-side-actions">
                    <a class="btn" href="{{ $createRoute }}">Cadastrar pavimento</a>
                    <a class="btn" href="{{ route('condominios.context.blocos.index', $condominio) }}">Ver blocos</a>
                    <a class="btn" href="{{ route('condominios.context.ajuda', $condominio) }}">Ajuda desta área</a>
                </div>
            </section>
        </aside>
    </section>
@endsection
