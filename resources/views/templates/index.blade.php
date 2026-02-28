@extends('layouts.app')

@section('title', 'Templates | SWA')

@section('content')
    @php($createRoute = route('condominios.context.templates.create', $condominio))

    <section class="browse-layout">
        <div class="browse-main">
            <section class="card stack browse-list-card">
                <div class="browse-header">
                    <div class="page-head" style="margin-bottom:0;">
                        <div>
                            <h1>Templates de checklist</h1>
                            <div class="muted">Catálogo de padrões que abastecem as vistorias sem precisar recriar itens toda vez.</div>
                        </div>
                        <a class="btn-primary" href="{{ $createRoute }}">Novo template</a>
                    </div>

                    <article class="guide" style="margin:0;">
                        <h2>Uso recomendado</h2>
                        <p>Crie templates por categoria (Segurança, Sinalização, Extintores, Estrutural) e aplique na vistoria para popular itens automaticamente.</p>
                    </article>
                </div>

                <div class="table-wrap" style="margin-top:14px;">
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Categoria</th>
                                <th>Itens</th>
                                <th>Ativo</th>
                                <th style="width:220px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($templates as $template)
                                <tr>
                                    <td>{{ $template->nome }}</td>
                                    <td>{{ $template->categoria ? ucfirst(str_replace('_', ' ', $template->categoria)) : '-' }}</td>
                                    <td>{{ $template->itens_count }}</td>
                                    <td>{{ $template->ativo ? 'Sim' : 'Não' }}</td>
                                    <td>
                                        <div class="actions">
                                            <a class="link-btn" href="{{ route('condominios.context.templates.edit', ['condominio' => $condominio, 'template' => $template]) }}">Editar</a>
                                            <form method="POST" action="{{ route('condominios.context.templates.destroy', ['condominio' => $condominio, 'template' => $template]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="link-btn link-danger" type="submit" onclick="return confirm('Excluir este template?')">Excluir</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="muted">Nenhum template cadastrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top:12px;">
                    {{ $templates->links() }}
                </div>
            </section>
        </div>

        <aside class="browse-side">
            <section class="card browse-aside-card">
                <h2 class="panel-title">Leitura rápida</h2>
                <div class="insight-list">
                    <div class="insight-item">
                        <strong>Padronize o campo</strong>
                        <span>Templates bem definidos reduzem retrabalho no celular e mantêm o PDF consistente.</span>
                    </div>
                    <div class="insight-item">
                        <strong>Revise itens ativos</strong>
                        <span>Antes de iniciar uma vistoria nova, confirme se o template correto está ativo.</span>
                    </div>
                </div>
            </section>

            <section class="card browse-aside-card">
                <h2 class="panel-title">Atalhos</h2>
                <div class="entry-side-actions">
                    <a class="btn" href="{{ $createRoute }}">Cadastrar template</a>
                    <a class="btn" href="{{ route('condominios.context.vistorias.create', $condominio) }}">Nova vistoria</a>
                    <a class="btn" href="{{ route('condominios.context.ajuda', $condominio) }}">Ajuda desta área</a>
                </div>
            </section>
        </aside>
    </section>
@endsection
