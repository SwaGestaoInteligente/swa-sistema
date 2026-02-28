@extends('layouts.app')

@section('title', 'Configuração de e-mails | SWA')

@section('content')
    <section class="card stack">
        <div class="page-head">
            <div>
                <h1>Destinatários de relatório</h1>
                <div class="muted">Síndico, conselho, administradora e outros.</div>
            </div>
            <a class="btn-primary" href="{{ route('condominios.context.relatorios.index', $condominio) }}">Ir para enviar relatório</a>
        </div>

        <div class="browse-highlight">
            <strong>Cadastro nesta tela</strong>
            Os destinatários são cadastrados aqui. O envio do relatório acontece na tela <strong>Relatórios</strong>.
        </div>

        <article class="guide">
            <h2>Novo destinatário</h2>
            <form method="POST" action="{{ route('condominios.context.emails.store', $condominio) }}" class="form-grid">
                @csrf
                <div class="field">
                    <label for="nome">Nome</label>
                    <input id="nome" name="nome" maxlength="120" required>
                </div>
                <div class="field">
                    <label for="email">E-mail</label>
                    <input id="email" name="email" type="email" maxlength="150" required>
                </div>
                <div class="field">
                    <label for="tipo">Tipo</label>
                    <select id="tipo" name="tipo" required>
                        @foreach ($tipos as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <label class="checkbox">
                    <input type="checkbox" name="ativo" value="1" checked>
                    Destinatário ativo
                </label>
                <div class="form-actions" style="grid-column:1/-1;margin-top:0;">
                    <button class="btn-primary" type="submit">Salvar destinatário</button>
                </div>
            </form>
        </article>

        <article class="guide">
            <h2>Lista de destinatários</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Tipo</th>
                            <th>Ativo</th>
                            <th style="width:280px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($emails as $email)
                            <tr>
                                <td>{{ $email->nome }}</td>
                                <td>{{ $email->email }}</td>
                                <td>{{ $tipos[$email->tipo] ?? $email->tipo }}</td>
                                <td>{{ $email->ativo ? 'Sim' : 'Não' }}</td>
                                <td>
                                    <details>
                                        <summary class="link-btn" style="display:inline-flex;cursor:pointer;">Editar</summary>
                                        <form method="POST" action="{{ route('condominios.context.emails.update', ['condominio' => $condominio, 'email' => $email]) }}" class="form-grid" style="margin-top:8px;">
                                            @csrf
                                            @method('PUT')
                                            <div class="field">
                                                <label>Nome</label>
                                                <input name="nome" value="{{ $email->nome }}" required>
                                            </div>
                                            <div class="field">
                                                <label>E-mail</label>
                                                <input name="email" type="email" value="{{ $email->email }}" required>
                                            </div>
                                            <div class="field">
                                                <label>Tipo</label>
                                                <select name="tipo" required>
                                                    @foreach ($tipos as $key => $label)
                                                        <option value="{{ $key }}" @selected($email->tipo === $key)>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <label class="checkbox">
                                                <input type="checkbox" name="ativo" value="1" @checked($email->ativo)>
                                                Ativo
                                            </label>
                                            <div class="form-actions" style="grid-column:1/-1;margin-top:0;">
                                                <button class="btn-primary" type="submit">Salvar edição</button>
                                            </div>
                                        </form>
                                    </details>
                                    <form method="POST" action="{{ route('condominios.context.emails.destroy', ['condominio' => $condominio, 'email' => $email]) }}" style="margin-top:8px;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="link-btn link-danger" type="submit" onclick="return confirm('Remover este destinatário?')">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="muted">Nenhum destinatário cadastrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection
