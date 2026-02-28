@extends('layouts.app')

@section('title', 'Vistoria '.$vistoria->codigo.' | SWA')

@section('content')
    @php($isLocked = in_array($vistoria->status, ['finalizada', 'cancelada'], true))
    @php($showRoute = route('condominios.context.vistorias.show', ['condominio' => $condominio, 'vistoria' => $vistoria]))
    @php($editRoute = route('condominios.context.vistorias.edit', ['condominio' => $condominio, 'vistoria' => $vistoria]))
    @php($itemCreateRoute = route('condominios.context.vistorias.itens.create', ['condominio' => $condominio, 'vistoria' => $vistoria]))
    @php($pdfRoute = route('condominios.context.vistorias.pdf', ['condominio' => $condominio, 'vistoria' => $vistoria]))
    @php($wizardRoute = route('condominios.context.vistorias.wizard', $condominio))
    @php($indexRoute = route('condominios.context.vistorias.index', $condominio))
    @php($finalizarRoute = route('condominios.context.vistorias.finalizar', ['condominio' => $condominio, 'vistoria' => $vistoria]))
    @php($reabrirRoute = route('condominios.context.vistorias.reabrir', ['condominio' => $condominio, 'vistoria' => $vistoria]))

    <style>
    /* ═══════════════════════════════════════════════════
       CAMPO-FIRST: mobile UX para inspetor em campo
       ═══════════════════════════════════════════════════ */
    @media (max-width: 700px) {

        /* ── Layout: sem aside, full-width ── */
        .detail-layout   { display: block !important; }
        .detail-side     { display: none  !important; }
        .detail-main     { width: 100%    !important; min-width: 0; }
        .detail-card     { padding: 0 !important; border: none !important; background: transparent !important; box-shadow: none !important; }

        /* ── Esconde no mobile: header verbose, KPIs, tabela, guide verboso ── */
        .detail-header   .dash-actions   { display: none !important; }
        .detail-header   .browse-highlight { display: none !important; }
        .dash-kpi-grid   { display: none !important; }
        .table-wrap      { display: none !important; }
        .flash.success   { border-radius: 10px; }

        /* ── Barra de progresso sticky ── */
        .field-sticky-progress {
            position: sticky;
            top: 56px;
            z-index: 30;
            background: #fff;
            border-bottom: 2px solid #e2e8f0;
            padding: 9px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .field-sticky-progress .prog-track {
            flex: 1;
            height: 7px;
            background: #e2e8f0;
            border-radius: 99px;
            overflow: hidden;
        }
        .field-sticky-progress .prog-fill {
            height: 100%;
            background: #0f766e;
            border-radius: 99px;
            transition: width .3s;
        }
        .field-sticky-progress .prog-label {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            white-space: nowrap;
        }

        /* ── Cabeçalho da vistoria compacto ── */
        .field-mobile-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 12px 16px 8px;
        }
        .field-mobile-header h1 { font-size: 17px; margin: 0; line-height: 1.2; }
        .field-mobile-header .muted { font-size: 12px; margin-top: 2px; }
        .field-mobile-header-actions { display: flex; gap: 6px; }
        .field-mobile-header-actions a {
            font-size: 12px; padding: 6px 10px;
            border-radius: 8px; border: 1px solid #e2e8f0;
            background: #f8fafc; color: #475569; text-decoration: none;
            white-space: nowrap;
        }

        /* ── Alertas de fechamento ── */
        .field-lock-notice { margin: 0 16px 8px; }
        .field-actions { display: none !important; }

        /* ── Seção de template ── */
        .guide { border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 12px 14px; margin: 0 0 12px; }
        .guide h2 { font-size: 14px; margin-bottom: 4px; }
        .guide p  { font-size: 13px; color: #64748b; margin-bottom: 8px; }
        .guide .form-grid { gap: 8px; }

        /* ── Título do checklist ── */
        .field-checklist-title {
            padding: 4px 16px 8px;
            font-size: 14px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        /* ═══════════════════════════
           ITEM CARD — CAMPO-FIRST
           ═══════════════════════════ */
        .item-card {
            border-radius: 14px;
            border: 2px solid #e2e8f0;
            background: #fff;
            overflow: hidden;
            margin: 0 0 12px;
        }
        .item-card-pending { border-color: #f59e0b !important; }
        .item-card-locked  { opacity: .85; }

        /* topo do card: nome + badge */
        .item-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            padding: 13px 14px 10px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        .item-name { font-size: 15px; font-weight: 800; line-height: 1.25; }
        .item-area { font-size: 12px; color: #64748b; margin-top: 2px; }
        .item-state-row { display: flex; flex-direction: column; align-items: flex-end; gap: 3px; }

        /* alerta de pendência */
        .item-alert {
            margin: 8px 14px 0;
            padding: 9px 11px;
            background: #fef9c3;
            border-radius: 8px;
            font-size: 12px;
            color: #78350f;
            line-height: 1.4;
        }

        /* ── STATUS CHIPS: ação primária ── */
        .item-status-chips { padding: 12px 14px 0; }
        .item-status-chips > label { display: none; }
        .item-status-chips select  { display: none; }   /* select oculto; chips controlam */
        .field-action-grid {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr);
            gap: 7px;
        }
        .field-chip-btn {
            width: 100%;
            min-height: 48px;
            font-size: 14px;
            font-weight: 700;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            background: #f8fafc;
            color: #334155;
            cursor: pointer;
            transition: background .12s, border-color .12s, color .12s;
            line-height: 1.1;
        }
        .field-chip-btn:disabled { opacity: .45; cursor: default; }
        /* Estados ativos coloridos */
        .field-chip-btn.active[data-value="ok"]         { background:#dcfce7; border-color:#16a34a; color:#15803d; }
        .field-chip-btn.active[data-value="danificado"] { background:#fee2e2; border-color:#dc2626; color:#b91c1c; }
        .field-chip-btn.active[data-value="ausente"]    { background:#fef3c7; border-color:#ca8a04; color:#854d0e; }
        .field-chip-btn.active[data-value="improvisado"]{ background:#fef3c7; border-color:#d97706; color:#92400e; }
        .field-chip-btn.active[data-value="atencao"]    { background:#fff7ed; border-color:#f59e0b; color:#92400e; }
        .field-chip-btn.active                          { background:#e0f2fe; border-color:#0284c7; color:#0369a1; }

        /* ── Observação ── */
        .item-obs-field { padding: 10px 14px 0; }
        .item-obs-field > label {
            font-size: 12px; font-weight: 600; color: #475569;
            display: block; margin-bottom: 4px;
        }
        .item-obs-field textarea {
            min-height: 72px; font-size: 15px;
            border-radius: 10px; border: 1.5px solid #e2e8f0;
            padding: 10px 12px; resize: vertical; width: 100%; box-sizing: border-box;
        }

        /* ── Chips de obs rápida ── */
        .item-quick-obs {
            padding: 5px 14px 0;
            display: flex; flex-wrap: wrap; gap: 5px;
        }
        .item-quick-obs .link-btn {
            font-size: 11px; padding: 4px 9px;
            background: #f1f5f9; border: 1px solid #e2e8f0;
            border-radius: 6px; cursor: pointer; text-decoration: none; color: #475569;
        }

        /* ── Upload de foto / câmera ── */
        .item-camera-field { padding: 10px 14px 0; }
        .item-camera-field > label {
            font-size: 12px; font-weight: 600; color: #475569;
            display: block; margin-bottom: 6px;
        }
        .item-camera-field input[type="file"] {
            display: block; width: 100%; box-sizing: border-box;
            padding: 14px 12px;
            background: #f0fdf4;
            border: 2px dashed #16a34a;
            border-radius: 10px;
            font-size: 14px;
            color: #15803d;
            cursor: pointer;
        }
        .item-camera-field input[type="file"]:disabled {
            background: #f8fafc; border-color: #e2e8f0; color: #94a3b8;
        }
        .item-camera-comment {
            margin-top: 6px;
            border-radius: 8px; border: 1.5px solid #e2e8f0;
            padding: 8px 10px; font-size: 13px; width: 100%; box-sizing: border-box;
        }
        .add-evidence-row { margin-top: 6px; font-size: 12px; color: #0284c7; }

        /* ── Fotos existentes: strip de thumbs ── */
        .item-photos-strip {
            padding: 8px 14px 0;
            display: flex; gap: 7px; flex-wrap: wrap;
        }
        .item-photos-strip a { display: block; }
        .item-photo-thumb {
            width: 62px; height: 62px;
            border-radius: 8px; object-fit: cover;
            border: 1.5px solid #e2e8f0;
        }
        .item-file-thumb {
            display: flex; align-items: center; justify-content: center;
            width: 62px; height: 62px;
            background: #f1f5f9; border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            font-size: 10px; color: #0284c7; text-align: center; line-height: 1.2;
            text-decoration: none;
        }

        /* ── Oculto em campo: painel de evidência verbose, campos secundários ── */
        .evidence-panel         { display: none !important; }
        .item-secondary-fields  { display: none !important; }
        .item-delete-form       { display: none !important; }

        /* ── Botão salvar ── */
        .item-save-row {
            padding: 12px 14px 14px;
        }
        .item-save-row .btn-primary {
            display: block; width: 100%;
            min-height: 52px; font-size: 16px; font-weight: 700;
            border-radius: 12px;
        }
        .item-save-row .muted { font-size: 12px; color: #94a3b8; text-align: center; padding-top: 8px; }

        /* ── Barra sticky inferior ── */
        .sticky-mobile-actions { padding: 12px 16px; gap: 10px; }
        .sticky-mobile-actions .btn-photo,
        .sticky-mobile-actions .btn-primary {
            flex: 1; min-height: 50px; font-size: 15px; font-weight: 700;
        }
    }
    /* ═══ fim campo-first ═══════════════════════════════ */
    </style>

    {{-- ── Barra de progresso sticky (só aparece em mobile via CSS) ── --}}
    @if (! $vistoria->itens->isEmpty())
        <div class="field-sticky-progress">
            <div class="prog-track">
                <div class="prog-fill" style="width:{{ $resumoCampo['progresso'] }}%;"></div>
            </div>
            <div class="prog-label">
                {{ $resumoCampo['total'] - $resumoCampo['pendencias'] }}/{{ $resumoCampo['total'] }}
                @if ($resumoCampo['pendencias'] > 0)
                    · <span style="color:#dc2626;">{{ $resumoCampo['pendencias'] }} pend.</span>
                @else
                    · <span style="color:#16a34a;">OK</span>
                @endif
            </div>
        </div>
    @endif

    <section class="detail-layout">
        <div class="detail-main">
            <section class="card stack detail-card">
                {{-- ── Cabeçalho desktop (mantido) ── --}}
                <div class="detail-header">
                    <div class="page-head" style="margin-bottom:0;">
                        <div>
                            <h1>Vistoria {{ $vistoria->codigo }}</h1>
                            <div class="muted">
                                {{ str_replace('_', ' ', ucfirst($vistoria->status)) }}
                                ·
                                {{ $vistoria->competencia?->format('d/m/Y') ?: 'Sem competencia' }}
                            </div>
                        </div>
                        <div class="dash-actions">
                            <a class="btn" href="{{ $indexRoute }}">Voltar</a>
                            <a class="btn" href="{{ $wizardRoute }}">Modo campo</a>
                            <a class="btn" href="{{ $pdfRoute }}">Gerar PDF</a>
                            @if (! $isLocked)
                                <a class="btn-photo" href="{{ $itemCreateRoute }}">Novo item + foto</a>
                            @endif
                        </div>
                    </div>
                    <div class="browse-highlight">
                        <strong>Area de detalhe</strong>
                        Esta tela concentra o acompanhamento da vistoria. Use os atalhos para editar, lancar evidencia e fechar o processo sem sair do contexto.
                    </div>
                </div>

                {{-- ── KPIs desktop ── --}}
                <div class="dash-kpi-grid">
                    <article class="dash-kpi">
                        <div class="dash-kpi-title">Risco geral</div>
                        <div class="dash-kpi-value" style="font-size:30px;">
                            <span class="risk-badge risk-{{ $vistoria->risco_nivel }}">
                                {{ $vistoria->risco_nivel_label }}
                            </span>
                        </div>
                        <div class="dash-kpi-note">{{ $vistoria->risco_geral }}% calculado pelos itens.</div>
                    </article>
                    <article class="dash-kpi">
                        <div class="dash-kpi-title">Itens totais</div>
                        <div class="dash-kpi-value">{{ $resumoCampo['total'] }}</div>
                        <div class="dash-kpi-note">{{ $resumoCampo['ok'] }} OK / {{ $resumoCampo['nao_ok'] }} nao OK</div>
                    </article>
                    <article class="dash-kpi">
                        <div class="dash-kpi-title">Pendencias</div>
                        <div class="dash-kpi-value">{{ $resumoCampo['pendencias'] }}</div>
                        <div class="dash-kpi-note">Nao OK sem foto/observacao obrigatoria.</div>
                    </article>
                    <article class="dash-kpi">
                        <div class="dash-kpi-title">Progresso</div>
                        <div class="dash-kpi-value">{{ $resumoCampo['progresso'] }}%</div>
                        <div class="dash-progress-track" style="margin-top:8px;">
                            <div class="dash-progress-fill" style="width: {{ $resumoCampo['progresso'] }}%;"></div>
                        </div>
                    </article>
                </div>

                {{-- ── Alertas de status ── --}}
                @if ($isLocked)
                    <div class="flash success">
                        Vistoria em modo bloqueado. Reabra apenas se precisar complementar evidencias ou corrigir o fechamento.
                    </div>
                @elseif ($resumoCampo['pendencias'] > 0)
                    <div class="flash error">
                        Fechamento bloqueado: ha {{ $resumoCampo['pendencias'] }} pendencia(s) sem evidencia obrigatoria.
                    </div>
                @endif

                {{-- ── Tabela de detalhes desktop ── --}}
                <div class="table-wrap">
                    <table>
                        <tbody>
                            <tr>
                                <th>Area principal</th>
                                <td>{{ $vistoria->area?->nome ?? '-' }}</td>
                                <th>Template</th>
                                <td>{{ $vistoria->template?->nome ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Responsavel</th>
                                <td>{{ $vistoria->responsavel_nome ?: '-' }}</td>
                                <th>Tipo</th>
                                <td>{{ str_replace('_', ' ', ucfirst($vistoria->tipo)) }}</td>
                            </tr>
                            <tr>
                                <th>Inicio</th>
                                <td>{{ $vistoria->iniciada_em?->format('d/m/Y H:i') ?: '-' }}</td>
                                <th>Fim</th>
                                <td>{{ $vistoria->finalizada_em?->format('d/m/Y H:i') ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Observacoes</th>
                                <td colspan="3">{{ $vistoria->observacoes ?: '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- ── Aplicar template ── --}}
                @if (! $isLocked)
                    <article class="guide">
                        <h2>Aplicar template</h2>
                        <p>Escolha template + area para popular automaticamente os itens da vistoria.</p>
                        <form method="POST" action="{{ route('condominios.context.vistorias.aplicar-template', ['condominio' => $condominio, 'vistoria' => $vistoria]) }}" class="form-grid">
                            @csrf
                            <div class="field">
                                <label for="checklist_template_id">Template</label>
                                <select id="checklist_template_id" name="checklist_template_id" required>
                                    <option value="">Selecionar</option>
                                    @foreach ($templates as $template)
                                        <option value="{{ $template->id }}">
                                            {{ $template->nome }}{{ $template->categoria ? ' - '.$template->categoria : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label for="area_id">Area</label>
                                <select id="area_id" name="area_id" required>
                                    <option value="">Selecionar</option>
                                    @foreach ($areas as $area)
                                        <option value="{{ $area->id }}">{{ $area->nome }} ({{ $area->tipo }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-actions" style="grid-column:1/-1;margin-top:0;">
                                <button class="btn-primary" type="submit">Aplicar template</button>
                                <a class="btn-photo" href="{{ $itemCreateRoute }}">Adicionar item manual + foto</a>
                                <a class="btn" href="{{ $editRoute }}">Editar dados da vistoria</a>
                            </div>
                        </form>
                    </article>
                @endif

                {{-- ── Reabrir / Finalizar ── --}}
                @if ($isLocked)
                    <div class="flash success">
                        Vistoria bloqueada ({{ $vistoria->status }}).
                        <form method="POST" action="{{ $reabrirRoute }}" class="reopen-form">
                            @csrf
                            <input type="text" name="motivo" placeholder="Motivo da reabertura (opcional)">
                            <button class="btn-primary" type="submit" onclick="return confirm('Reabrir vistoria para continuar?')">Reabrir vistoria</button>
                        </form>
                    </div>
                @else
                    <div class="field-actions">
                        <form method="POST" action="{{ $finalizarRoute }}">
                            @csrf
                            <button class="btn-primary" type="submit" onclick="return confirm('Finalizar vistoria agora?')">Finalizar vistoria</button>
                        </form>
                        @if ($resumoCampo['pendencias'] > 0)
                            <span class="flash error" style="margin:0;">
                                Ha {{ $resumoCampo['pendencias'] }} pendencia(s) sem evidencia.
                            </span>
                        @endif
                    </div>
                @endif

                {{-- ══════════════════════════════════════════════
                     CHECKLIST — CAMPO-FIRST
                     ══════════════════════════════════════════════ --}}
                <div class="page-head">
                    <h1 style="font-size:22px;">Checklist em campo</h1>
                    <span class="muted">Toque no status, registre foto e salve.</span>
                </div>

                @if ($vistoria->itens->isEmpty())
                    <div class="muted">Sem itens ainda. Use "Aplicar template" ou "Adicionar item manual + foto".</div>
                @else
                    <div class="grid-cards">
                        @foreach ($vistoria->itens as $item)
                            @php($itemUpdateRoute = route('condominios.context.vistorias.itens.update', ['condominio' => $condominio, 'vistoria' => $vistoria, 'item' => $item]))
                            @php($itemDeleteRoute = route('condominios.context.vistorias.itens.destroy', ['condominio' => $condominio, 'vistoria' => $vistoria, 'item' => $item]))
                            @php($itemObsId = 'obs-'.$item->id)
                            @php($itemStatusId = 'status-'.$item->id)
                            @php($itemEvidencePanelId = 'evidence-panel-'.$item->id)
                            @php($itemEvidenceRowsId = 'evidence-rows-'.$item->id)
                            @php($itemAnexos = $item->anexos->sortByDesc('created_at'))
                            @php($itemHasImage = $itemAnexos->contains(fn ($a) => str_starts_with((string) $a->mime_type, 'image/')))
                            @php($itemPending = $item->status !== 'ok' && (($item->obrigatorio_foto_se_nao_ok && $itemAnexos->isEmpty()) || blank($item->observacao)))

                            <article @class(['item-card', 'item-card-pending' => $itemPending, 'item-card-locked' => $isLocked])>
                                <form
                                    method="POST"
                                    action="{{ $itemUpdateRoute }}"
                                    enctype="multipart/form-data"
                                    data-offline-queue="vistoria-item-update"
                                >
                                    @csrf
                                    @method('PUT')

                                    {{-- 1. Topo: nome + badge de status --}}
                                    <div class="item-card-top">
                                        <div>
                                            <div class="item-name">{{ $item->item_nome }}</div>
                                            <div class="item-area muted">
                                                {{ $item->area?->nome ?? '-' }} · {{ str_replace('_', ' ', $item->categoria) }}
                                            </div>
                                        </div>
                                        <div class="item-state-row">
                                            <span class="item-state item-state-{{ $item->status }}">
                                                {{ $statusList[$item->status] ?? ucfirst($item->status) }}
                                            </span>
                                            @if ($itemPending)
                                                <span class="item-flag item-flag-warning">Pendencia</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- 2. Alerta de pendência --}}
                                    @if ($itemPending)
                                        <div class="item-alert">
                                            Revise foto e observacao para liberar o fechamento.
                                        </div>
                                    @endif

                                    {{-- 3. STATUS CHIPS — ação primária em campo --}}
                                    <div class="item-status-chips">
                                        <label>Status rapido</label>
                                        <select id="{{ $itemStatusId }}" name="status" data-status-select @disabled($isLocked) required>
                                            @foreach ($statusList as $statusKey => $statusLabel)
                                                <option value="{{ $statusKey }}" @selected($item->status === $statusKey)>{{ $statusLabel }}</option>
                                            @endforeach
                                        </select>
                                        <div class="field-action-grid" data-status-controls="{{ $itemStatusId }}">
                                            @foreach ($statusList as $statusKey => $statusLabel)
                                                <button
                                                    type="button"
                                                    class="field-chip-btn status-chip"
                                                    data-target="{{ $itemStatusId }}"
                                                    data-value="{{ $statusKey }}"
                                                    @disabled($isLocked)
                                                >{{ $statusLabel }}</button>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- 4. Observação --}}
                                    <div class="item-obs-field">
                                        <label for="{{ $itemObsId }}">Observacao</label>
                                        <textarea
                                            id="{{ $itemObsId }}"
                                            name="observacao"
                                            @disabled($isLocked)
                                            placeholder="Descreva o que encontrou"
                                        >{{ $item->observacao }}</textarea>
                                    </div>

                                    {{-- 5. Chips de observação rápida --}}
                                    <div class="item-quick-obs">
                                        @foreach ($quickObs as $obs)
                                            <button
                                                type="button"
                                                class="link-btn quick-obs"
                                                data-target="{{ $itemObsId }}"
                                                @disabled($isLocked)
                                            >{{ $obs }}</button>
                                        @endforeach
                                    </div>

                                    {{-- 6. Upload de foto (câmera) --}}
                                    <div class="item-camera-field">
                                        <label>Foto / evidencia</label>
                                        <div id="{{ $itemEvidenceRowsId }}" class="stack">
                                            <div>
                                                <input
                                                    type="file"
                                                    name="fotos[]"
                                                    accept=".jpg,.jpeg,.png,.pdf,image/*,application/pdf"
                                                    capture="environment"
                                                    data-evidence-file
                                                    @disabled($isLocked)
                                                >
                                                <input
                                                    class="item-camera-comment"
                                                    name="foto_comentarios[]"
                                                    maxlength="500"
                                                    placeholder="Comentario da foto (opcional)"
                                                    data-evidence-comment
                                                    @disabled($isLocked)
                                                >
                                            </div>
                                        </div>
                                        @if (! $isLocked)
                                            <button
                                                type="button"
                                                class="link-btn add-evidence-row"
                                                data-target="{{ $itemEvidenceRowsId }}"
                                            >+ Outra foto</button>
                                        @endif
                                    </div>

                                    {{-- 7. Fotos existentes: strip de thumbs --}}
                                    @if ($itemAnexos->isNotEmpty())
                                        <div class="item-photos-strip">
                                            @foreach ($itemAnexos as $anexo)
                                                @php($url = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                                                    'condominios.context.anexos.download.signed',
                                                    now()->addMinutes(30),
                                                    ['condominio' => $condominio, 'anexo' => $anexo]
                                                ))
                                                @if (str_starts_with((string) $anexo->mime_type, 'image/'))
                                                    <a href="{{ $url }}" target="_blank">
                                                        <img class="item-photo-thumb" src="{{ $url }}" alt="Evidencia">
                                                    </a>
                                                @else
                                                    <a class="item-file-thumb" href="{{ $url }}" target="_blank">PDF</a>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- 8. Painel de evidência (visível no desktop, oculto no mobile via CSS) --}}
                                    <div
                                        id="{{ $itemEvidencePanelId }}"
                                        @class(['evidence-panel', 'required' => $itemPending, 'ready' => $item->status === 'ok' || ($itemHasImage && filled($item->observacao))])
                                        data-existing-image="{{ $itemHasImage ? 1 : 0 }}"
                                    >
                                        <div class="evidence-status-line">
                                            <div>
                                                <strong style="display:block;">Guia rapido de evidencia</strong>
                                                <span class="evidence-status-copy" data-evidence-copy>
                                                    @if ($item->status === 'ok')
                                                        Item em OK: a foto e opcional, mas continua util para historico.
                                                    @elseif ($itemHasImage && filled($item->observacao))
                                                        Evidencia completa: a regra de foto e observacao ja foi atendida.
                                                    @else
                                                        Status diferente de OK: registre foto e observacao para liberar o fechamento da vistoria.
                                                    @endif
                                                </span>
                                            </div>
                                            <span class="evidence-status-pill" data-evidence-pill>
                                                @if ($item->status === 'ok')
                                                    Opcional
                                                @elseif ($itemHasImage && filled($item->observacao))
                                                    Pronto
                                                @else
                                                    Obrigatorio
                                                @endif
                                            </span>
                                        </div>
                                        <div class="evidence-steps">
                                            <div class="evidence-step">
                                                <strong>1. Tire ou anexe a foto</strong>
                                                Use a primeira linha para camera rapida.
                                            </div>
                                            <div class="evidence-step">
                                                <strong>2. Descreva o achado</strong>
                                                A observacao precisa explicar o problema para o PDF.
                                            </div>
                                        </div>
                                    </div>

                                    {{-- 9. Campos secundários (ocultos no mobile via CSS) --}}
                                    <div class="item-secondary-fields">
                                        <div class="form-grid">
                                            <div class="field">
                                                <label>Criticidade</label>
                                                <select name="criticidade" @disabled($isLocked) required>
                                                    @foreach ($criticidades as $criticidadeKey => $criticidadeLabel)
                                                        <option value="{{ $criticidadeKey }}" @selected($item->criticidade === $criticidadeKey)>{{ $criticidadeLabel }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="field">
                                                <label>Inspecionado em</label>
                                                <input
                                                    type="datetime-local"
                                                    name="inspecionado_em"
                                                    value="{{ old('inspecionado_em.'.$item->id, optional($item->inspecionado_em)->format('Y-m-d\\TH:i')) }}"
                                                    @disabled($isLocked)
                                                >
                                            </div>
                                        </div>
                                        <label class="checkbox">
                                            <input type="hidden" name="obrigatorio_foto_se_nao_ok" value="0">
                                            <input
                                                type="checkbox"
                                                name="obrigatorio_foto_se_nao_ok"
                                                value="1"
                                                @checked($item->obrigatorio_foto_se_nao_ok)
                                                @disabled($isLocked)
                                            >
                                            Foto obrigatoria quando nao OK
                                        </label>
                                    </div>

                                    {{-- 10. Botão salvar --}}
                                    <div class="item-save-row">
                                        @if (! $isLocked)
                                            <button class="btn-primary" type="submit">Salvar</button>
                                        @else
                                            <div class="muted">Item bloqueado (vistoria finalizada).</div>
                                        @endif
                                    </div>
                                </form>

                                @if (! $isLocked)
                                    <form method="POST" action="{{ $itemDeleteRoute }}" class="item-delete-form" style="padding:0 14px 10px;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="link-btn link-danger" type="submit" onclick="return confirm('Excluir este item?')">Excluir</button>
                                    </form>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        <aside class="detail-side">
            <section class="card detail-aside-card">
                <h2 class="panel-title">Resumo da vistoria</h2>
                <div class="detail-meta-grid">
                    <div class="detail-meta-item">
                        <strong>Status</strong>
                        {{ str_replace('_', ' ', ucfirst($vistoria->status)) }}
                    </div>
                    <div class="detail-meta-item">
                        <strong>Competencia</strong>
                        {{ $vistoria->competencia?->format('d/m/Y') ?: 'Sem competencia' }}
                    </div>
                    <div class="detail-meta-item">
                        <strong>Area principal</strong>
                        {{ $vistoria->area?->nome ?? '-' }}
                    </div>
                    <div class="detail-meta-item">
                        <strong>Template</strong>
                        {{ $vistoria->template?->nome ?? 'Sem template' }}
                    </div>
                </div>
            </section>

            <section class="card detail-aside-card">
                <h2 class="panel-title">Proximas acoes</h2>
                <div class="entry-side-actions">
                    @if (! $isLocked)
                        <a class="btn-photo" href="{{ $itemCreateRoute }}">Novo item com foto</a>
                        <a class="btn" href="{{ $editRoute }}">Editar vistoria</a>
                    @endif
                    <a class="btn" href="{{ $pdfRoute }}">Gerar PDF</a>
                    <a class="btn" href="{{ $wizardRoute }}">Modo campo</a>
                    <a class="btn" href="{{ $indexRoute }}">Voltar para lista</a>
                </div>
            </section>

            <section class="card detail-aside-card">
                <h2 class="panel-title">Regras do fechamento</h2>
                <div class="insight-list">
                    <div class="insight-item">
                        <strong>Pendencias bloqueiam</strong>
                        <span>Se houver item nao OK sem evidencia obrigatoria, revise antes de finalizar.</span>
                    </div>
                    <div class="insight-item">
                        <strong>PDF sai desta tela</strong>
                        <span>Depois de revisar, gere o relatorio sem precisar voltar para outra lista.</span>
                    </div>
                    <div class="insight-item">
                        <strong>Reabertura controlada</strong>
                        <span>Vistorias finalizadas podem ser reabertas com motivo para continuar o registro.</span>
                    </div>
                </div>
            </section>
        </aside>
    </section>

    {{-- Barra sticky mobile (ja existia) --}}
    @if (! $isLocked)
        <div class="sticky-mobile-actions">
            <a class="btn-photo" href="{{ $itemCreateRoute }}">+ Item com foto</a>
            <form method="POST" action="{{ $finalizarRoute }}">
                @csrf
                <button class="btn-primary" type="submit">Finalizar</button>
            </form>
        </div>
    @endif

    <script>
        document.querySelectorAll('.add-evidence-row').forEach((button) => {
            button.addEventListener('click', () => {
                const target = document.getElementById(button.dataset.target);
                if (!target) return;

                const row = document.createElement('div');
                row.style.marginTop = '8px';
                row.innerHTML = `
                    <input type="file" name="fotos[]"
                        accept=".jpg,.jpeg,.png,.pdf,image/*,application/pdf"
                        capture="environment" data-evidence-file
                        style="display:block;width:100%;box-sizing:border-box;padding:14px 12px;background:#f0fdf4;border:2px dashed #16a34a;border-radius:10px;font-size:14px;color:#15803d;cursor:pointer;margin-bottom:6px;">
                    <input class="item-camera-comment" name="foto_comentarios[]" maxlength="500"
                        placeholder="Comentario da foto (opcional)" data-evidence-comment>
                    <button type="button" class="link-btn link-danger remove-evidence-row" style="margin-top:4px;font-size:12px;">Remover</button>
                `;
                const removeBtn = row.querySelector('.remove-evidence-row');
                if (removeBtn) removeBtn.addEventListener('click', () => row.remove());
                target.appendChild(row);
            });
        });

        const bindStatusControls = (selectId, panelId, observationId, rowsId) => {
            const select = document.getElementById(selectId);
            const panel  = document.getElementById(panelId);
            const observation = document.getElementById(observationId);
            const rows   = document.getElementById(rowsId);
            const chips  = document.querySelectorAll(`.status-chip[data-target="${selectId}"]`);

            if (!select || !panel) return;

            const copy = panel.querySelector('[data-evidence-copy]');
            const pill = panel.querySelector('[data-evidence-pill]');

            const sync = () => {
                const needsEvidence = ['danificado', 'ausente', 'improvisado'].includes(select.value);
                const hasExistingImage = panel.dataset.existingImage === '1';
                const hasNewImage = Array.from((rows || panel).querySelectorAll('input[type="file"][data-evidence-file]'))
                    .some((i) => i.files && i.files.length > 0);
                const hasComment = !!(observation?.value || '').trim()
                    || Array.from((rows || panel).querySelectorAll('input[data-evidence-comment]'))
                        .some((i) => (i.value || '').trim() !== '');
                const evidenceReady = !needsEvidence || ((hasExistingImage || hasNewImage) && hasComment);

                chips.forEach((chip) => {
                    chip.classList.toggle('active', chip.dataset.value === select.value);
                });

                panel.classList.toggle('required', needsEvidence && !evidenceReady);
                panel.classList.toggle('ready',    evidenceReady);

                if (!copy || !pill) return;

                if (!needsEvidence) {
                    pill.textContent = 'Opcional';
                    copy.textContent = 'Item em OK: a foto e opcional, mas continua util para historico.';
                    return;
                }
                if (evidenceReady) {
                    pill.textContent = 'Pronto';
                    copy.textContent = 'Evidencia completa: a regra de foto e observacao ja foi atendida.';
                    return;
                }
                pill.textContent = 'Obrigatorio';
                copy.textContent = 'Status diferente de OK: registre foto e observacao para liberar o fechamento da vistoria.';
            };

            chips.forEach((chip) => {
                chip.addEventListener('click', () => {
                    if (chip.disabled) return;
                    select.value = chip.dataset.value;
                    sync();
                });
            });

            select.addEventListener('change', sync);
            observation?.addEventListener('input', sync);
            rows?.addEventListener('change', (e) => {
                if (e.target.matches('input[type="file"][data-evidence-file], input[data-evidence-comment]')) sync();
            });
            rows?.addEventListener('input', (e) => {
                if (e.target.matches('input[data-evidence-comment]')) sync();
            });

            sync();
        };

        @foreach ($vistoria->itens as $item)
            bindStatusControls(
                'status-{{ $item->id }}',
                'evidence-panel-{{ $item->id }}',
                'obs-{{ $item->id }}',
                'evidence-rows-{{ $item->id }}'
            );
        @endforeach

        document.querySelectorAll('.quick-obs').forEach((button) => {
            button.addEventListener('click', () => {
                const target = document.getElementById(button.dataset.target);
                if (!target) return;
                const text = button.textContent.trim();
                const current = target.value.trim();
                target.value = current === '' ? text : `${current}; ${text}`;
                target.focus();
            });
        });
    </script>
@endsection
