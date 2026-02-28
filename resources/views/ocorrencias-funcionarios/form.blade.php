@csrf

@php($cancelRoute = route('condominios.context.ocorrencias.index', $condominio))
@php($ocorridoEm = old('ocorrido_em', $ocorrencia->ocorrido_em ? \Illuminate\Support\Carbon::parse($ocorrencia->ocorrido_em)->format('Y-m-d\TH:i') : null))
@php($tipoAtual = old('tipo', $ocorrencia->tipo))
@php($statusAtual = old('status', $ocorrencia->status))

<div class="form-grid">
    <div class="field">
        <label>Condomínio</label>
        <input value="{{ $condominio->nome }}" disabled>
    </div>

    <div class="field">
        <label for="protocolo">Protocolo</label>
        <input id="protocolo" name="protocolo" value="{{ old('protocolo', $ocorrencia->protocolo) }}" maxlength="40" required>
    </div>

    <div class="field">
        <label for="ocorrido_em">Ocorrido em</label>
        <input id="ocorrido_em" name="ocorrido_em" type="datetime-local" value="{{ $ocorridoEm }}" required>
    </div>

    <div class="field">
        <label for="funcionario_id">Funcionário (cadastro)</label>
        <select id="funcionario_id" name="funcionario_id">
            <option value="">Selecionar por nome livre</option>
            @foreach ($funcionarios as $funcionario)
                <option value="{{ $funcionario->id }}" @selected(old('funcionario_id', $ocorrencia->funcionario_id) === $funcionario->id)>{{ $funcionario->nome }}</option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label for="funcionario_nome">Funcionário (nome manual)</label>
        <input id="funcionario_nome" name="funcionario_nome" value="{{ old('funcionario_nome', $ocorrencia->funcionario_nome) }}" maxlength="150">
    </div>

    <div class="field">
        <label for="cargo">Cargo</label>
        <input id="cargo" name="cargo" value="{{ old('cargo', $ocorrencia->cargo) }}" maxlength="100">
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
        <label for="reincidencia_nivel">Reincidência</label>
        <input id="reincidencia_nivel" name="reincidencia_nivel" type="number" min="0" max="10" value="{{ old('reincidencia_nivel', $ocorrencia->reincidencia_nivel ?? 0) }}">
    </div>

    <div class="field">
        <label for="testemunha_nome">Testemunha (nome)</label>
        <input id="testemunha_nome" name="testemunha_nome" value="{{ old('testemunha_nome', $ocorrencia->testemunha_nome) }}" maxlength="150">
    </div>

    <div class="field">
        <label for="testemunha_contato">Testemunha (contato)</label>
        <input id="testemunha_contato" name="testemunha_contato" value="{{ old('testemunha_contato', $ocorrencia->testemunha_contato) }}" maxlength="100">
    </div>

    <div class="field">
        <label for="medida_aplicada">Medida aplicada</label>
        <input id="medida_aplicada" name="medida_aplicada" value="{{ old('medida_aplicada', $ocorrencia->medida_aplicada) }}" maxlength="150">
    </div>

    <div class="field" style="grid-column:1/-1;">
        <label for="relato_detalhado">Relato detalhado</label>
        <textarea id="relato_detalhado" name="relato_detalhado" required>{{ old('relato_detalhado', $ocorrencia->relato_detalhado) }}</textarea>
    </div>

    <div class="field" style="grid-column:1/-1;">
        <label for="anexo">Anexo (foto ou PDF)</label>
        <input id="anexo" name="anexo" type="file" accept=".jpg,.jpeg,.png,.pdf,image/*,application/pdf">
    </div>

    @if ($ocorrencia->exists && $ocorrencia->anexos->isNotEmpty())
        <div class="field" style="grid-column:1/-1;">
            <label>Anexos atuais</label>
            <div class="actions">
                @foreach ($ocorrencia->anexos as $anexo)
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
