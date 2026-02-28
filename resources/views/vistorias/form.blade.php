@csrf

@php
    $cancelRoute = route('condominios.context.vistorias.index', $condominio);
    $tipoAtual = old('tipo', $vistoria->tipo ?? 'rotina');
    $statusAtual = old('status', $vistoria->status ?? 'rascunho');
    $competencia = old('competencia', optional($vistoria->competencia)->format('Y-m-d'));
    $iniciada = old('iniciada_em', optional($vistoria->iniciada_em)->format('Y-m-d\TH:i'));
    $finalizada = old('finalizada_em', optional($vistoria->finalizada_em)->format('Y-m-d\TH:i'));
    $selectedArea = old('area_id', $vistoria->area_id);
    $selectedTemplate = old('checklist_template_id', $vistoria->checklist_template_id);
    $riscoAtual = (int) old('risco_geral', $vistoria->risco_geral ?? 0);
    $riscoNivelAtual = old(
        'risco_nivel',
        $riscoAtual <= 0 ? 'neutro' : ($riscoAtual <= 33 ? 'baixo' : ($riscoAtual <= 66 ? 'medio' : 'alto'))
    );
@endphp

<div class="form-grid">
    <div class="field">
        <label>Condomínio</label>
        <input value="{{ $condominio->nome }} ({{ $condominio->codigo }})" disabled>
    </div>

    <div class="field">
        <label for="codigo">Código da vistoria</label>
        <input id="codigo" name="codigo" value="{{ old('codigo', $vistoria->codigo) }}" required maxlength="30" placeholder="Ex.: VIS-2026-0001">
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
        <label for="competencia">Competência</label>
        <input id="competencia" name="competencia" type="date" value="{{ $competencia }}">
    </div>

    <div class="field">
        <label for="responsavel_nome">Responsável</label>
        <input id="responsavel_nome" name="responsavel_nome" maxlength="120" value="{{ old('responsavel_nome', $vistoria->responsavel_nome) }}" placeholder="Nome do inspetor">
    </div>

    <div class="field">
        <label for="area_id">Área principal</label>
        <select id="area_id" name="area_id">
            <option value="">Selecionar área</option>
            @foreach ($areas as $area)
                <option value="{{ $area->id }}" @selected($selectedArea === $area->id)>
                    {{ $area->nome }} ({{ $area->tipo }}) {{ $area->codigo ? '- '.$area->codigo : '' }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label for="checklist_template_id">Template de checklist</label>
        <select id="checklist_template_id" name="checklist_template_id">
            <option value="">Sem template</option>
            @foreach ($templates as $template)
                <option value="{{ $template->id }}" @selected($selectedTemplate === $template->id)>
                    {{ $template->nome }}{{ $template->categoria ? ' - '.$template->categoria : '' }}
                </option>
            @endforeach
        </select>
        <span class="muted">Ao selecionar template, escolha também a área para popular os itens.</span>
    </div>

    <div class="field">
        <label for="iniciada_em">Iniciada em</label>
        <input id="iniciada_em" name="iniciada_em" type="datetime-local" value="{{ $iniciada }}">
    </div>

    <div class="field">
        <label for="finalizada_em">Finalizada em</label>
        <input id="finalizada_em" name="finalizada_em" type="datetime-local" value="{{ $finalizada }}">
    </div>

    <div class="field">
        <label for="risco_nivel">Risco inicial</label>
        <select id="risco_nivel" name="risco_nivel">
            <option value="neutro" @selected($riscoNivelAtual === 'neutro')>Neutro</option>
            <option value="baixo" @selected($riscoNivelAtual === 'baixo')>Baixo</option>
            <option value="medio" @selected($riscoNivelAtual === 'medio')>Médio</option>
            <option value="alto" @selected($riscoNivelAtual === 'alto')>Alto</option>
        </select>
    </div>

    <div class="field" style="grid-column:1/-1;">
        <label for="observacoes">Observações</label>
        <textarea id="observacoes" name="observacoes" placeholder="Detalhes gerais da vistoria">{{ old('observacoes', $vistoria->observacoes) }}</textarea>
    </div>
</div>

<div class="form-actions">
    <a class="btn" href="{{ $cancelRoute }}">Cancelar</a>
    <button class="btn-primary" type="submit">{{ $buttonLabel }}</button>
</div>

<script>
    (() => {
        const areaInput = document.getElementById('area_id');
        const templateInput = document.getElementById('checklist_template_id');

        if (!areaInput || !templateInput) {
            return;
        }

        const validateTemplate = () => {
            if (templateInput.value !== '' && areaInput.value === '') {
                areaInput.setCustomValidity('Selecione a área para aplicar o template.');
            } else {
                areaInput.setCustomValidity('');
            }
        };

        templateInput.addEventListener('change', validateTemplate);
        areaInput.addEventListener('change', validateTemplate);
        validateTemplate();
    })();
</script>
