@csrf

@php($cancelRoute = route('condominios.context.blocos.index', $condominio))

<div class="form-grid">
    <div class="field">
        <label>Condomínio</label>
        <input value="{{ $condominio->nome }}" disabled>
    </div>
    <div class="field">
        <label for="codigo">Código</label>
        <input id="codigo" name="codigo" value="{{ old('codigo', $bloco->codigo) }}" required maxlength="30">
    </div>
    <div class="field">
        <label for="nome">Nome</label>
        <input id="nome" name="nome" value="{{ old('nome', $bloco->nome) }}" required maxlength="120">
    </div>
    <div class="field">
        <label for="ordem">Ordem</label>
        <input id="ordem" name="ordem" type="number" min="0" value="{{ old('ordem', $bloco->ordem ?? 0) }}">
    </div>
    <div class="field" style="grid-column:1/-1;">
        <label for="descricao">Descrição</label>
        <textarea id="descricao" name="descricao" placeholder="Ex.: Torre residencial com 12 pavimentos">{{ old('descricao', $bloco->descricao) }}</textarea>
    </div>
</div>

<label class="checkbox">
    <input type="checkbox" name="ativo" value="1" @checked(old('ativo', $bloco->ativo ?? true))>
    Bloco ativo
</label>

<div class="form-actions">
    <a class="btn" href="{{ $cancelRoute }}">Cancelar</a>
    <button class="btn-primary" type="submit">{{ $buttonLabel }}</button>
</div>
