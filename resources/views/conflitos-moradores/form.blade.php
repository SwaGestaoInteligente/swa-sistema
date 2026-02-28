@csrf

@php($cancelRoute = route('condominios.context.conflitos.index', $condominio))
@php($ocorridoEm = old('ocorrido_em', $conflito->ocorrido_em ? \Illuminate\Support\Carbon::parse($conflito->ocorrido_em)->format('Y-m-d\TH:i') : null))
@php($tipoAtual = old('tipo', $conflito->tipo))
@php($statusAtual = old('status', $conflito->status))

<div class="form-grid">
    <div class="field">
        <label>Condomínio</label>
        <input value="{{ $condominio->nome }}" disabled>
    </div>

    <div class="field">
        <label for="protocolo">Protocolo</label>
        <input id="protocolo" name="protocolo" value="{{ old('protocolo', $conflito->protocolo) }}" maxlength="40" required>
    </div>

    <div class="field">
        <label for="ocorrido_em">Ocorrido em</label>
        <input id="ocorrido_em" name="ocorrido_em" type="datetime-local" value="{{ $ocorridoEm }}" required>
    </div>

    <div class="field">
        <label for="unidade_id">Unidade</label>
        <select id="unidade_id" name="unidade_id">
            <option value="">Não vinculada</option>
            @foreach ($unidades as $unidade)
                <option value="{{ $unidade->id }}" @selected(old('unidade_id', $conflito->unidade_id) === $unidade->id)>
                    Unidade {{ $unidade->numero }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label for="unidade">Unidade (texto livre)</label>
        <input id="unidade" name="unidade" value="{{ old('unidade', $conflito->unidade) }}" maxlength="40" placeholder="Ex.: Bloco B / 302">
    </div>

    <div class="field">
        <label for="morador_a_id">Morador A (cadastro)</label>
        <select id="morador_a_id" name="morador_a_id">
            <option value="">Selecionar por nome livre</option>
            @foreach ($moradores as $morador)
                <option value="{{ $morador->id }}" @selected(old('morador_a_id', $conflito->morador_a_id) === $morador->id)>{{ $morador->nome }}</option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label for="morador_a_nome">Morador A (nome manual)</label>
        <input id="morador_a_nome" name="morador_a_nome" value="{{ old('morador_a_nome', $conflito->morador_a_nome) }}" maxlength="150">
    </div>

    <div class="field">
        <label for="morador_b_id">Morador B (cadastro)</label>
        <select id="morador_b_id" name="morador_b_id">
            <option value="">Selecionar por nome livre</option>
            @foreach ($moradores as $morador)
                <option value="{{ $morador->id }}" @selected(old('morador_b_id', $conflito->morador_b_id) === $morador->id)>{{ $morador->nome }}</option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label for="morador_b_nome">Morador B (nome manual)</label>
        <input id="morador_b_nome" name="morador_b_nome" value="{{ old('morador_b_nome', $conflito->morador_b_nome) }}" maxlength="150">
    </div>

    <div class="field">
        <label for="tipo">Tipo</label>
        <select id="tipo" name="tipo" required>
            @foreach ($tipos as $key => $label)
                <option value="{{ $key }}" @selected($tipoAtual === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label for="status">Status</label>
        <select id="status" name="status" required>
            @foreach ($statusList as $key => $label)
                <option value="{{ $key }}" @selected($statusAtual === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label for="tratado_por">Tratado por</label>
        <input id="tratado_por" name="tratado_por" value="{{ old('tratado_por', $conflito->tratado_por) }}" maxlength="120">
    </div>

    <div class="field" style="grid-column:1/-1;">
        <label for="relato">Relato</label>
        <textarea id="relato" name="relato" required>{{ old('relato', $conflito->relato) }}</textarea>
    </div>

    <div class="field" style="grid-column:1/-1;">
        <label for="testemunhas_text">Testemunhas (1 por linha)</label>
        <textarea id="testemunhas_text" name="testemunhas_text">{{ old('testemunhas_text', $testemunhasText) }}</textarea>
    </div>

    <div class="field" style="grid-column:1/-1;">
        <label for="tentativa_mediacao">Tentativa de mediação</label>
        <textarea id="tentativa_mediacao" name="tentativa_mediacao">{{ old('tentativa_mediacao', $conflito->tentativa_mediacao) }}</textarea>
    </div>

    <div class="field" style="grid-column:1/-1;">
        <label for="anexo">Anexo (foto ou PDF)</label>
        <input id="anexo" name="anexo" type="file" accept=".jpg,.jpeg,.png,.pdf,image/*,application/pdf">
    </div>

    @if ($conflito->exists && $conflito->anexos->isNotEmpty())
        <div class="field" style="grid-column:1/-1;">
            <label>Anexos atuais</label>
            <div class="actions">
                @foreach ($conflito->anexos as $anexo)
                    @php($anexoDownloadUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute('condominios.context.anexos.download.signed', now()->addMinutes(30), ['condominio' => $condominio, 'anexo' => $anexo]))
                    <a class="link-btn" href="{{ $anexoDownloadUrl }}">
                        {{ $anexo->file_name }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>

<div class="form-actions">
    <a class="btn" href="{{ $cancelRoute }}">Cancelar</a>
    <button class="btn-primary" type="submit">{{ $buttonLabel }}</button>
</div>
