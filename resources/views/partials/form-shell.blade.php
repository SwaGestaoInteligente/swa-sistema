@php($currentCondominio = $condominio ?? request()->route('condominio'))
@php($resolvedFormData = $formData ?? [])

<section class="entry-layout">
    <div class="entry-main">
        <section class="card stack entry-form-card">
            <div class="entry-header">
                <span class="hero-eyebrow">{{ $eyebrow ?? 'Area de cadastro' }}</span>
                <div class="page-head" style="margin-bottom:0;">
                    <div>
                        <h1>{{ $title }}</h1>
                        @if (!empty($subtitle))
                            <div class="muted">{{ $subtitle }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ $formAction }}" @if (!empty($enctype)) enctype="{{ $enctype }}" @endif>
                @if (!empty($formMethod))
                    @method($formMethod)
                @endif

                @include($formView, array_merge($resolvedFormData, ['buttonLabel' => $buttonLabel]))
            </form>
        </section>
    </div>

    <aside class="entry-side">
        <section class="card entry-aside-card">
            <h2 class="panel-title">Como preencher</h2>
            <div class="insight-list">
                <div class="insight-item">
                    <strong>Preencha o essencial</strong>
                    <span>Cadastre agora apenas os dados que sustentam a operacao e o contexto correto.</span>
                </div>
                <div class="insight-item">
                    <strong>Revise antes de salvar</strong>
                    <span>Nomes, codigos e relacoes erradas geram confusao nas telas de vistoria e relatorio.</span>
                </div>
                <div class="insight-item">
                    <strong>Salve e siga o fluxo</strong>
                    <span>Depois de salvar, volte para a lista ou para o painel do condominio para continuar.</span>
                </div>
            </div>
        </section>

        <section class="card entry-aside-card">
            <h2 class="panel-title">Atalhos</h2>
            <div class="entry-side-actions">
                <a class="btn" href="{{ $backRoute }}">{{ $backLabel ?? 'Voltar para lista' }}</a>
                @if ($currentCondominio)
                    <a class="btn" href="{{ route('condominios.context.dashboard', $currentCondominio) }}">Painel do condominio</a>
                    <a class="btn" href="{{ route('condominios.context.ajuda', $currentCondominio) }}">Ajuda desta area</a>
                @else
                    <a class="btn" href="{{ route('dashboard') }}">Painel geral</a>
                    <a class="btn" href="{{ route('ajuda.index') }}">Ajuda geral</a>
                @endif
            </div>
        </section>
    </aside>
</section>
