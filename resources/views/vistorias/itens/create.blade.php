@extends('layouts.app')

@section('title', 'Novo item da vistoria | SWA')
@section('hide_nav', '1')

@section('content')
    @php($showRoute = route('condominios.context.vistorias.show', ['condominio' => $condominio, 'vistoria' => $vistoria]))
    @php($storeRoute = route('condominios.context.vistorias.itens.store', ['condominio' => $condominio, 'vistoria' => $vistoria]))
    @php($areasRoute = route('condominios.context.areas.create', $condominio))

    <section class="entry-layout">
        <div class="entry-main">
            <section class="card stack entry-form-card">
                <div class="entry-header">
                    <span class="hero-eyebrow">Area de cadastro</span>
                    <div class="page-head" style="margin-bottom:0;">
                        <div>
                            <h1>Novo item da vistoria {{ $vistoria->codigo }}</h1>
                            <div class="muted">Registre o item inspecionado em uma area separada da consulta, com foco em foto e evidencia.</div>
                        </div>
                        <a class="btn" href="{{ $showRoute }}">Voltar</a>
                    </div>
                </div>

                @if ($areas->isEmpty())
                    <div class="flash error">
                        Você precisa cadastrar ao menos uma área antes de lançar itens da vistoria.
                        <a href="{{ $areasRoute }}" style="text-decoration:underline;">Cadastrar área agora</a>
                    </div>
                @else
                    <div class="flash success">
                        Regra: quando o status for diferente de OK, foto e observação são obrigatórias.
                    </div>
                    <form method="POST" action="{{ $storeRoute }}" enctype="multipart/form-data" data-offline-queue="vistoria-item-create">
                        @csrf

                        <div class="form-grid">
                            <div class="field">
                                <label for="area_id">Área</label>
                                <select id="area_id" name="area_id" required>
                                    <option value="">Selecione</option>
                                    @foreach ($areas as $area)
                                        <option value="{{ $area->id }}" @selected(old('area_id', $item->area_id) === $area->id)>
                                            {{ $area->nome }} ({{ $area->codigo }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="field">
                                <label for="item_codigo">Código do item</label>
                                <input id="item_codigo" name="item_codigo" maxlength="40" value="{{ old('item_codigo', $item->item_codigo) }}" placeholder="Ex.: EXT-01">
                            </div>

                            <div class="field">
                                <label for="item_nome">Nome do item</label>
                                <input id="item_nome" name="item_nome" required maxlength="150" value="{{ old('item_nome', $item->item_nome) }}" placeholder="Ex.: Extintor do corredor">
                            </div>

                            <div class="field">
                                <label for="categoria">Categoria</label>
                                @php($categoriaAtual = old('categoria', $item->categoria))
                                <select id="categoria" name="categoria" required>
                                    @foreach ($categorias as $key => $label)
                                        <option value="{{ $key }}" @selected($categoriaAtual === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="field">
                                <label for="status">Status</label>
                                @php($statusAtual = old('status', $item->status))
                                <select id="status" name="status" data-status-select required>
                                    @foreach ($statusList as $key => $label)
                                        <option value="{{ $key }}" @selected($statusAtual === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="field">
                                <label for="criticidade">Criticidade</label>
                                @php($criticidadeAtual = old('criticidade', $item->criticidade))
                                <select id="criticidade" name="criticidade" required>
                                    @foreach ($criticidades as $key => $label)
                                        <option value="{{ $key }}" @selected($criticidadeAtual === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="field" style="grid-column:1/-1;">
                                <label>Status rápido (modo campo)</label>
                                <div class="field-action-grid" data-status-controls="status">
                                    @foreach ($statusList as $key => $label)
                                        <button type="button" class="field-chip-btn status-chip" data-target="status" data-value="{{ $key }}">
                                            {{ $label }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="field">
                                <label for="inspecionado_em">Inspecionado em</label>
                                <input id="inspecionado_em" name="inspecionado_em" type="datetime-local" value="{{ old('inspecionado_em', $item->inspecionado_em) }}">
                            </div>

                            <div class="field" style="grid-column:1/-1;">
                                <label>Registro de evidência</label>
                                <div class="evidence-panel" id="createEvidencePanel" data-status-target="status">
                                    <div class="evidence-status-line">
                                        <div>
                                            <strong style="display:block;">Quando a foto é obrigatória</strong>
                                            <span class="evidence-status-copy" data-evidence-copy>Se o item estiver OK, a foto é opcional.</span>
                                        </div>
                                        <span class="evidence-status-pill" data-evidence-pill>Opcional</span>
                                    </div>
                                    <div class="evidence-steps">
                                        <div class="evidence-step">
                                            <strong>1. Tire a foto</strong>
                                            Use a câmera traseira do celular ou escolha um PDF/já salvo.
                                        </div>
                                        <div class="evidence-step">
                                            <strong>2. Explique o que foi visto</strong>
                                            Um comentário curto evita dúvida na revisão e no PDF.
                                        </div>
                                    </div>
                                    <div class="form-grid" style="margin:0;">
                                        <div class="field">
                                            <label for="foto">Foto principal (celular)</label>
                                            <input id="foto" name="foto" type="file" accept=".jpg,.jpeg,.png,.pdf,image/*,application/pdf" capture="environment" data-evidence-file>
                                        </div>
                                        <div class="field">
                                            <label for="foto_comentario">Comentário da foto principal</label>
                                            <input id="foto_comentario" name="foto_comentario" maxlength="500" value="{{ old('foto_comentario') }}" placeholder="Ex.: detalhe frontal, equipamento vencido..." data-evidence-comment>
                                        </div>
                                    </div>
                                    <div class="field" style="margin:0;">
                                        <label>Fotos adicionais + comentários</label>
                                        <div id="extraEvidenceRows" class="stack"></div>
                                        <button type="button" class="link-btn" id="addEvidenceRow">+ Adicionar outra foto</button>
                                    </div>
                                    <span class="muted">No Android, o botão abre a câmera traseira. Para status diferente de OK, foto e observação são exigidas ao salvar.</span>
                                </div>
                            </div>

                            <div class="field" style="grid-column:1/-1;">
                                <label for="observacao">Detalhes</label>
                                <textarea id="observacao" name="observacao" data-observacao-input placeholder="Ex.: Extintor sem lacre, vencimento 01/2025, suporte solto">{{ old('observacao', $item->observacao) }}</textarea>
                            </div>

                            <div class="actions" style="grid-column:1/-1;">
                                @foreach ($quickObs as $obs)
                                    <button type="button" class="link-btn quick-obs" data-target="observacao">{{ $obs }}</button>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-actions">
                            <a class="btn" href="{{ $showRoute }}">Cancelar</a>
                            <button class="btn-primary" type="submit">Salvar item e voltar para a vistoria</button>
                        </div>
                    </form>
                @endif
            </section>
        </div>

        <aside class="entry-side">
            <section class="card entry-aside-card">
                <h2 class="panel-title">Como preencher</h2>
                <div class="insight-list">
                    <div class="insight-item">
                        <strong>Escolha a area certa</strong>
                        <span>O item precisa ficar vinculado ao local correto para refletir no relatorio final.</span>
                    </div>
                    <div class="insight-item">
                        <strong>Priorize a evidencia</strong>
                        <span>Se houver desvio, registre foto e detalhe objetivo no mesmo momento.</span>
                    </div>
                    <div class="insight-item">
                        <strong>Use os atalhos</strong>
                        <span>Os chips de observacao aceleram o preenchimento em campo sem perder padrao.</span>
                    </div>
                </div>
            </section>

            <section class="card entry-aside-card">
                <h2 class="panel-title">Atalhos</h2>
                <div class="entry-side-actions">
                    <a class="btn" href="{{ $showRoute }}">Detalhes da vistoria</a>
                    <a class="btn" href="{{ route('condominios.context.vistorias.edit', ['condominio' => $condominio, 'vistoria' => $vistoria]) }}">Editar vistoria</a>
                    <a class="btn" href="{{ route('condominios.context.ajuda', $condominio) }}">Ajuda desta area</a>
                </div>
            </section>
        </aside>
    </section>

    <script>
        const addEvidenceButton = document.getElementById('addEvidenceRow');
        const extraEvidenceRows = document.getElementById('extraEvidenceRows');

        if (addEvidenceButton && extraEvidenceRows) {
            const buildEvidenceRow = () => {
                const wrapper = document.createElement('div');
                wrapper.className = 'form-grid';
                wrapper.innerHTML = `
                    <div class="field">
                        <label>Arquivo</label>
                        <input name="fotos[]" type="file" accept=".jpg,.jpeg,.png,.pdf,image/*,application/pdf" capture="environment" data-evidence-file>
                    </div>
                    <div class="field">
                        <label>Comentário</label>
                        <input name="foto_comentarios[]" maxlength="500" placeholder="Ex.: visão lateral da área, ponto danificado..." data-evidence-comment>
                    </div>
                    <div class="actions" style="align-items:flex-end;">
                        <button type="button" class="link-btn link-danger remove-evidence-row">Remover</button>
                    </div>
                `;

                const removeButton = wrapper.querySelector('.remove-evidence-row');
                if (removeButton) {
                    removeButton.addEventListener('click', () => wrapper.remove());
                }

                return wrapper;
            };

            addEvidenceButton.addEventListener('click', () => {
                extraEvidenceRows.appendChild(buildEvidenceRow());
            });
        }

        const bindStatusControls = (selectId, panelId, observationId) => {
            const select = document.getElementById(selectId);
            const panel = document.getElementById(panelId);
            const observation = document.getElementById(observationId);
            const chips = document.querySelectorAll(`.status-chip[data-target="${selectId}"]`);

            if (!select || !panel) {
                return;
            }

            const copy = panel.querySelector('[data-evidence-copy]');
            const pill = panel.querySelector('[data-evidence-pill]');
            const fileInput = panel.querySelector('[data-evidence-file]');
            const commentInput = panel.querySelector('[data-evidence-comment]');

            const sync = () => {
                const needsEvidence = ['danificado', 'ausente', 'improvisado'].includes(select.value);
                const hasComment = !!((commentInput?.value || '').trim() || (observation?.value || '').trim());
                const hasExtraFile = Array.from(document.querySelectorAll('#extraEvidenceRows input[type="file"][data-evidence-file]'))
                    .some((input) => input.files && input.files.length > 0);
                const hasExtraComment = Array.from(document.querySelectorAll('#extraEvidenceRows input[data-evidence-comment]'))
                    .some((input) => (input.value || '').trim() !== '');
                const hasFile = !!(fileInput?.files && fileInput.files.length > 0) || hasExtraFile;
                const evidenceReady = !needsEvidence || (hasFile && (hasComment || hasExtraComment));

                chips.forEach((chip) => {
                    chip.classList.toggle('active', chip.dataset.value === select.value);
                });

                panel.classList.toggle('required', needsEvidence && !evidenceReady);
                panel.classList.toggle('ready', evidenceReady);

                if (!copy || !pill) {
                    return;
                }

                if (!needsEvidence) {
                    pill.textContent = 'Opcional';
                    copy.textContent = 'Se o item estiver OK, você pode salvar sem foto. Ainda assim, a evidência ajuda no histórico.';
                    return;
                }

                if (evidenceReady) {
                    pill.textContent = 'Pronto';
                    copy.textContent = 'Foto e descrição preenchidas. O item já atende a regra de evidência para status diferente de OK.';
                    return;
                }

                pill.textContent = 'Obrigatório';
                copy.textContent = 'Status diferente de OK: anexe foto e registre observação antes de salvar.';
            };

            chips.forEach((chip) => {
                chip.addEventListener('click', () => {
                    select.value = chip.dataset.value;
                    sync();
                });
            });

            select.addEventListener('change', sync);
            observation?.addEventListener('input', sync);
            commentInput?.addEventListener('input', sync);
            fileInput?.addEventListener('change', sync);
            extraEvidenceRows?.addEventListener('change', (event) => {
                if (event.target.matches('input[type="file"], input[name="foto_comentarios[]"]')) {
                    sync();
                }
            });
            extraEvidenceRows?.addEventListener('input', (event) => {
                if (event.target.matches('input[name="foto_comentarios[]"]')) {
                    sync();
                }
            });

            sync();
        };

        bindStatusControls('status', 'createEvidencePanel', 'observacao');

        document.querySelectorAll('.quick-obs').forEach((button) => {
            button.addEventListener('click', () => {
                const target = document.getElementById(button.dataset.target);
                if (!target) {
                    return;
                }
                const text = button.textContent.trim();
                const current = target.value.trim();
                target.value = current === '' ? text : `${current}; ${text}`;
                target.focus();
            });
        });
    </script>
@endsection
