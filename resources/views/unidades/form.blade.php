@csrf

@php
    $cancelRoute = route('condominios.context.unidades.index', $condominio);
    $selectedBloco = old('bloco_id', $unidade->bloco_id);
    $selectedPavimento = old('pavimento_id', $unidade->pavimento_id);
    $tipoAtual = old('tipo', $unidade->tipo ?? 'apto');
    $statusAtual = old('status', $unidade->status ?? 'ocupado');
@endphp

<div class="form-grid">
    <div class="field">
        <label>Condomínio</label>
        <input value="{{ $condominio->nome }} ({{ $condominio->codigo }})" disabled>
    </div>

    <div class="field">
        <label for="numero">Número</label>
        <input id="numero" name="numero" value="{{ old('numero', $unidade->numero) }}" maxlength="30" required placeholder="Ex.: 101">
    </div>

    <div class="field">
        <label for="bloco_id">Bloco</label>
        <select id="bloco_id" name="bloco_id" required>
            <option value="">Selecionar bloco</option>
            @foreach ($blocos as $bloco)
                <option value="{{ $bloco->id }}" @selected($selectedBloco === $bloco->id)>
                    {{ $bloco->nome }}{{ $bloco->codigo ? ' - '.$bloco->codigo : '' }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label for="pavimento_id">Pavimento</label>
        <select id="pavimento_id" name="pavimento_id" required>
            <option value="">Selecionar pavimento</option>
            @foreach ($pavimentos as $pavimento)
                <option value="{{ $pavimento->id }}" data-bloco="{{ $pavimento->bloco_id }}" @selected($selectedPavimento === $pavimento->id)>
                    {{ $pavimento->nome }}{{ $pavimento->codigo ? ' - '.$pavimento->codigo : '' }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label for="tipo">Tipo</label>
        <select id="tipo" name="tipo" required>
            <option value="apto" @selected($tipoAtual === 'apto')>Apartamento</option>
            <option value="sala" @selected($tipoAtual === 'sala')>Sala</option>
        </select>
    </div>

    <div class="field">
        <label for="status">Status</label>
        <select id="status" name="status" required>
            <option value="ocupado" @selected($statusAtual === 'ocupado')>Ocupado</option>
            <option value="vago" @selected($statusAtual === 'vago')>Vago</option>
        </select>
    </div>
</div>

<div class="form-actions">
    <a class="btn" href="{{ $cancelRoute }}">Cancelar</a>
    <button class="btn-primary" type="submit">{{ $buttonLabel }}</button>
</div>

<script>
    (() => {
        const bloco = document.getElementById('bloco_id');
        const pavimento = document.getElementById('pavimento_id');

        if (!bloco || !pavimento) {
            return;
        }

        const refreshPavimentos = () => {
            const blocoId = bloco.value;
            let hasVisibleSelected = false;

            [...pavimento.options].forEach((option, index) => {
                if (index === 0) {
                    option.hidden = false;
                    return;
                }

                const visible = !blocoId || option.dataset.bloco === blocoId;
                option.hidden = !visible;

                if (!visible && option.selected) {
                    option.selected = false;
                }

                if (visible && option.selected) {
                    hasVisibleSelected = true;
                }
            });

            if (!hasVisibleSelected) {
                pavimento.value = '';
            }
        };

        bloco.addEventListener('change', refreshPavimentos);
        refreshPavimentos();
    })();
</script>
