@extends('layouts.app')

@section('title', 'Editar vistoria | SWA')
@section('hide_nav', '1')

@section('content')
    @php($updateRoute = route('condominios.context.vistorias.update', ['condominio' => $condominio, 'vistoria' => $vistoria]))
    @php($showRoute = route('condominios.context.vistorias.show', ['condominio' => $condominio, 'vistoria' => $vistoria]))
    @php($photoRoute = route('condominios.context.vistorias.itens.create', ['condominio' => $condominio, 'vistoria' => $vistoria]))

    <section class="entry-layout">
        <div class="entry-main">
            <section class="card stack entry-form-card">
                <div class="entry-header">
                    <span class="hero-eyebrow">Area de cadastro</span>
                    <div class="page-head" style="margin-bottom:0;">
                        <div>
                            <h1>Editar vistoria</h1>
                            <div class="muted">Codigo {{ $vistoria->codigo }} · ajuste os dados base antes de seguir para campo</div>
                        </div>
                    </div>
                </div>

                <article class="guide">
                    <h2>Fotos da vistoria</h2>
                    <p>Esta area centraliza a manutencao da vistoria. Se estiver em campo, abra o cadastro de item para registrar evidencias na hora.</p>
                    <div class="field-actions">
                        <a class="btn-photo" href="{{ $photoRoute }}">Tirar foto agora</a>
                        <a class="btn" href="{{ $showRoute }}">Voltar para detalhes</a>
                    </div>
                </article>

                <form method="POST" action="{{ $updateRoute }}">
                    @method('PUT')
                    @include('vistorias.form', ['buttonLabel' => 'Atualizar'])
                </form>
            </section>
        </div>

        <aside class="entry-side">
            <section class="card entry-aside-card">
                <h2 class="panel-title">Fluxo recomendado</h2>
                <div class="insight-list">
                    <div class="insight-item">
                        <strong>1. Ajuste a vistoria</strong>
                        <span>Revise titulo, area, responsavel e status para manter o contexto correto.</span>
                    </div>
                    <div class="insight-item">
                        <strong>2. Lance evidencias</strong>
                        <span>Use o botao de foto para acelerar o preenchimento no celular.</span>
                    </div>
                    <div class="insight-item">
                        <strong>3. Revise pendencias</strong>
                        <span>A tela de detalhes concentra itens, anexos e pendencias antes do fechamento.</span>
                    </div>
                </div>
            </section>

            <section class="card entry-aside-card">
                <h2 class="panel-title">Atalhos</h2>
                <div class="entry-side-actions">
                    <a class="btn" href="{{ route('condominios.context.vistorias.index', $condominio) }}">Voltar para lista</a>
                    <a class="btn" href="{{ $showRoute }}">Detalhes da vistoria</a>
                    <a class="btn" href="{{ route('condominios.context.dashboard', $condominio) }}">Painel do condominio</a>
                </div>
            </section>
        </aside>
    </section>
@endsection
