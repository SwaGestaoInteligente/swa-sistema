@csrf

@php($selectedBloco = old('bloco_id', $pavimento->bloco_id))
@php($cancelRoute = route('condominios.context.pavimentos.index', ['condominio' => $condominio, 'bloco_id' => $selectedBloco]))

<div class="form-grid">
    <div class="field">
        <label>Condomínio</label>
        <input value="{{ $condominio->nome }}" disabled>
    </div>

    <div class="field">
        <label for="bloco_id">Bloco</label>
        <select id="bloco_id" name="bloco_id" required>
            <option value="">Selecione</option>
            @foreach ($blocos as $bloco)
                <option value="{{ $bloco->id }}" @selected(old('bloco_id', $pavimento->bloco_id) === $bloco->id)>
                    {{ $bloco->nome }} ({{ $bloco->codigo }})
                </option>
            @endforeach
        </select>
        <span class="muted">Selecione um bloco do mesmo condomínio informado.</span>
    </div>

    <div class="field">
        <label for="codigo">Código</label>
        <input id="codigo" name="codigo" value="{{ old('codigo', $pavimento->codigo) }}" required maxlength="30">
    </div>

    <div class="field">
        <label for="nome">Nome</label>
        <input id="nome" name="nome" value="{{ old('nome', $pavimento->nome) }}" required maxlength="120">
    </div>

    <div class="field">
        <label for="nivel">Nível</label>
        <input id="nivel" name="nivel" type="number" value="{{ old('nivel', $pavimento->nivel ?? 0) }}">
    </div>

    <div class="field">
        <label for="ordem">Ordem</label>
        <input id="ordem" name="ordem" type="number" min="0" value="{{ old('ordem', $pavimento->ordem ?? 0) }}">
    </div>
</div>

<label class="checkbox">
    <input type="checkbox" name="ativo" value="1" @checked(old('ativo', $pavimento->ativo ?? true))>
    Pavimento ativo
</label>

<div class="form-actions">
    <a class="btn" href="{{ $cancelRoute }}">Cancelar</a>
    <button class="btn-primary" type="submit">{{ $buttonLabel }}</button>
</div>
