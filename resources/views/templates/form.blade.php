@csrf

@php
    $cancelRoute = route('condominios.context.templates.index', $condominio);
    $categoriaAtual = old('categoria', $template->categoria);
@endphp

<div class="form-grid">
    <div class="field">
        <label>Condomínio</label>
        <input value="{{ $condominio->nome }} ({{ $condominio->codigo }})" disabled>
    </div>

    <div class="field">
        <label for="nome">Nome</label>
        <input id="nome" name="nome" value="{{ old('nome', $template->nome) }}" maxlength="120" required placeholder="Ex.: Segurança geral">
    </div>

    <div class="field">
        <label for="categoria">Categoria</label>
        <select id="categoria" name="categoria">
            <option value="">Sem categoria</option>
            @foreach ($categorias as $key => $label)
                <option value="{{ $key }}" @selected($categoriaAtual === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <label class="checkbox">
        <input type="hidden" name="ativo" value="0">
        <input type="checkbox" name="ativo" value="1" @checked(old('ativo', $template->ativo ?? true))>
        Template ativo
    </label>
</div>

<div class="guide" style="margin-top:12px;">
    <h2>Itens do template</h2>
    <p>Esses itens serão criados automaticamente quando o template for aplicado na vistoria.</p>

    <div id="template-items" class="stack" style="margin-top:10px;">
        @php($itensAntigos = old('itens'))
        @if (is_array($itensAntigos) && count($itensAntigos) > 0)
            @foreach ($itensAntigos as $index => $item)
                <div class="item-card template-item" data-index="{{ $index }}">
                    <div class="form-grid">
                        <div class="field">
                            <label>Título do item</label>
                            <input name="itens[{{ $index }}][titulo_item]" value="{{ $item['titulo_item'] ?? '' }}" required maxlength="150">
                        </div>
                        <div class="field">
                            <label>Categoria</label>
                            <select name="itens[{{ $index }}][categoria]" required>
                                @foreach ($itemCategorias as $catKey => $catLabel)
                                    <option value="{{ $catKey }}" @selected(($item['categoria'] ?? '') === $catKey)>{{ $catLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Ordem</label>
                            <input type="number" min="0" name="itens[{{ $index }}][ordem]" value="{{ $item['ordem'] ?? ($index + 1) }}">
                        </div>
                        <label class="checkbox">
                            <input type="hidden" name="itens[{{ $index }}][obrigatorio_foto_se_nao_ok]" value="0">
                            <input type="checkbox" name="itens[{{ $index }}][obrigatorio_foto_se_nao_ok]" value="1" @checked(($item['obrigatorio_foto_se_nao_ok'] ?? '1') == '1')>
                            Foto obrigatória quando não OK
                        </label>
                    </div>
                    <div class="actions" style="margin-top:8px;">
                        <button type="button" class="link-btn link-danger remove-template-item">Remover item</button>
                    </div>
                </div>
            @endforeach
        @elseif($template->exists && $template->relationLoaded('itens') && $template->itens->isNotEmpty())
            @foreach ($template->itens->sortBy('ordem')->values() as $index => $item)
                <div class="item-card template-item" data-index="{{ $index }}">
                    <div class="form-grid">
                        <div class="field">
                            <label>Título do item</label>
                            <input name="itens[{{ $index }}][titulo_item]" value="{{ $item->titulo_item }}" required maxlength="150">
                        </div>
                        <div class="field">
                            <label>Categoria</label>
                            <select name="itens[{{ $index }}][categoria]" required>
                                @foreach ($itemCategorias as $catKey => $catLabel)
                                    <option value="{{ $catKey }}" @selected($item->categoria === $catKey)>{{ $catLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Ordem</label>
                            <input type="number" min="0" name="itens[{{ $index }}][ordem]" value="{{ $item->ordem }}">
                        </div>
                        <label class="checkbox">
                            <input type="hidden" name="itens[{{ $index }}][obrigatorio_foto_se_nao_ok]" value="0">
                            <input type="checkbox" name="itens[{{ $index }}][obrigatorio_foto_se_nao_ok]" value="1" @checked($item->obrigatorio_foto_se_nao_ok)>
                            Foto obrigatória quando não OK
                        </label>
                    </div>
                    <div class="actions" style="margin-top:8px;">
                        <button type="button" class="link-btn link-danger remove-template-item">Remover item</button>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="dash-actions" style="margin-top:10px;">
        <button type="button" class="btn" id="add-template-item">+ Adicionar item</button>
    </div>
</div>

<div class="form-actions">
    <a class="btn" href="{{ $cancelRoute }}">Cancelar</a>
    <button class="btn-primary" type="submit">{{ $buttonLabel }}</button>
</div>

<template id="template-item-template">
    <div class="item-card template-item" data-index="__INDEX__">
        <div class="form-grid">
            <div class="field">
                <label>Título do item</label>
                <input name="itens[__INDEX__][titulo_item]" required maxlength="150" placeholder="Ex.: Extintor com selo válido">
            </div>
            <div class="field">
                <label>Categoria</label>
                <select name="itens[__INDEX__][categoria]" required>
                    @foreach ($itemCategorias as $catKey => $catLabel)
                        <option value="{{ $catKey }}">{{ $catLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>Ordem</label>
                <input type="number" min="0" name="itens[__INDEX__][ordem]" value="0">
            </div>
            <label class="checkbox">
                <input type="hidden" name="itens[__INDEX__][obrigatorio_foto_se_nao_ok]" value="0">
                <input type="checkbox" name="itens[__INDEX__][obrigatorio_foto_se_nao_ok]" value="1" checked>
                Foto obrigatória quando não OK
            </label>
        </div>
        <div class="actions" style="margin-top:8px;">
            <button type="button" class="link-btn link-danger remove-template-item">Remover item</button>
        </div>
    </div>
</template>

<script>
    (() => {
        const container = document.getElementById('template-items');
        const addButton = document.getElementById('add-template-item');
        const template = document.getElementById('template-item-template');
        let nextIndex = container.querySelectorAll('.template-item').length;

        const mountRemoveAction = (scope) => {
            scope.querySelectorAll('.remove-template-item').forEach((button) => {
                button.onclick = () => {
                    const card = button.closest('.template-item');
                    if (card) {
                        card.remove();
                    }
                };
            });
        };

        mountRemoveAction(container);

        if (addButton && template) {
            addButton.addEventListener('click', () => {
                const html = template.innerHTML.replaceAll('__INDEX__', String(nextIndex));
                nextIndex += 1;
                const wrapper = document.createElement('div');
                wrapper.innerHTML = html.trim();
                const node = wrapper.firstElementChild;
                if (!node) {
                    return;
                }
                container.appendChild(node);
                mountRemoveAction(node);
            });
        }

        if (container.querySelectorAll('.template-item').length === 0 && addButton) {
            addButton.click();
        }
    })();
</script>
