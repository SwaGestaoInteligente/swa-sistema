@csrf

@php($selectedBloco = old('bloco_id', $area->bloco_id))
@php($selectedPavimento = old('pavimento_id', $area->pavimento_id))
@php($cancelRoute = route('condominios.context.areas.index', ['condominio' => $condominio, 'bloco_id' => $selectedBloco, 'pavimento_id' => $selectedPavimento]))

<div class="form-grid">
    <div class="field">
        <label>Condomínio</label>
        <input value="{{ $condominio->nome }}" disabled>
    </div>

    <div class="field">
        <label for="bloco_id">Bloco (opcional)</label>
        <select id="bloco_id" name="bloco_id">
            <option value="">Sem bloco</option>
            @foreach ($blocos as $bloco)
                <option value="{{ $bloco->id }}" @selected(old('bloco_id', $area->bloco_id) === $bloco->id)>
                    {{ $bloco->nome }} ({{ $bloco->codigo }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label for="pavimento_id">Pavimento (opcional)</label>
        <select id="pavimento_id" name="pavimento_id">
            <option value="">Sem pavimento</option>
            @foreach ($pavimentos as $pavimento)
                <option value="{{ $pavimento->id }}" @selected(old('pavimento_id', $area->pavimento_id) === $pavimento->id)>
                    {{ $pavimento->nome }} ({{ $pavimento->codigo }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label for="tipo">Tipo</label>
        @php($tipoAtual = old('tipo', $area->tipo ?? 'comum'))
        <select id="tipo" name="tipo" required>
            <option value="externa" @selected($tipoAtual === 'externa')>Externa</option>
            <option value="comum" @selected($tipoAtual === 'comum')>Comum</option>
            <option value="tecnica" @selected($tipoAtual === 'tecnica')>Técnica</option>
            <option value="seguranca" @selected($tipoAtual === 'seguranca')>Segurança</option>
        </select>
    </div>

    <div class="field">
        <label for="codigo">Código</label>
        <input id="codigo" name="codigo" value="{{ old('codigo', $area->codigo) }}" required maxlength="30">
    </div>

    <div class="field">
        <label for="nome">Nome</label>
        <input id="nome" name="nome" value="{{ old('nome', $area->nome) }}" required maxlength="120">
    </div>

    <div class="field" style="grid-column:1/-1;">
        <label for="descricao">Descrição</label>
        <textarea id="descricao" name="descricao">{{ old('descricao', $area->descricao) }}</textarea>
    </div>
</div>

<label class="checkbox">
    <input type="checkbox" name="ativa" value="1" @checked(old('ativa', $area->ativa ?? true))>
    Área ativa
</label>

<div class="form-actions">
    <a class="btn" href="{{ $cancelRoute }}">Cancelar</a>
    <button class="btn-primary" type="submit">{{ $buttonLabel }}</button>
</div>
