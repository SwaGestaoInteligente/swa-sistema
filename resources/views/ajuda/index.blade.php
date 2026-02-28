@extends('layouts.app')

@section('title', 'Ajuda | SWA')

@section('content')
    @php($ctx = $condominio ?? request()->route('condominio'))

    <style>
        .help-block {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 14px;
            background: #fff;
        }
        .help-block h2 {
            margin: 0 0 10px;
            font-size: 20px;
            color: var(--primary-strong);
        }
        .help-block h3 {
            margin: 12px 0 8px;
            font-size: 16px;
            color: #23456e;
        }
        .help-list {
            list-style: decimal;
            margin: 0;
            padding-left: 20px;
            display: grid;
            gap: 8px;
            color: #36567d;
            font-size: 14px;
        }
        .help-list li {
            line-height: 1.45;
        }
        .help-tip {
            border: 1px solid #f1da9a;
            border-radius: 10px;
            padding: 10px;
            background: #fff9ea;
            color: #6f4e00;
            font-size: 13px;
        }
        .help-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 10px;
        }
        .help-kbd {
            display: inline-flex;
            align-items: center;
            border: 1px solid var(--line);
            border-bottom-width: 2px;
            border-radius: 6px;
            padding: 1px 7px;
            background: #f8fbff;
            color: #23456e;
            font-weight: 700;
            font-size: 12px;
        }
        .help-search {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px;
            background: #fff;
            display: grid;
            gap: 8px;
        }
        .help-search label {
            font-size: 14px;
            color: #2f4f77;
            font-weight: 700;
        }
        .help-search input {
            border: 1px solid var(--line);
            border-radius: 10px;
            min-height: 42px;
            padding: 10px 11px;
            font: inherit;
            background: #fff;
        }
        .help-search input:focus {
            outline: 2px solid rgba(12, 46, 105, 0.16);
            border-color: #95abcf;
        }
        .help-faq {
            display: grid;
            gap: 8px;
        }
        .faq-item {
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fff;
            padding: 10px 12px;
        }
        .faq-item summary {
            cursor: pointer;
            font-weight: 700;
            color: #23456e;
        }
        .faq-item p {
            margin: 8px 0 0;
            font-size: 14px;
            color: #456084;
            line-height: 1.45;
        }
    </style>

    <div class="card stack">
        <div class="page-head">
            <div>
                <h1>Central de Ajuda SWA</h1>
                <div class="muted">
                    Manual completo de uso do sistema, do primeiro acesso até fechar uma vistoria com PDF e envio por e-mail.
                </div>
            </div>
            <div class="actions">
                <button type="button" class="link-btn" onclick="window.print()">Imprimir guia</button>
                <a class="link-btn link-strong" href="#fluxo-fim-a-fim">Fluxo completo</a>
                <a class="link-btn" href="#faq">FAQ</a>
            </div>
        </div>

        <section class="help-search" data-help-search-item>
            <label for="help-search-input">Buscar no manual</label>
            <input
                id="help-search-input"
                type="search"
                placeholder="Ex.: tirar foto, vistoria, PDF, conflito, e-mail, template"
                autocomplete="off"
            >
            <div id="help-search-count" class="muted">Mostrando todo o conteúdo da ajuda.</div>
        </section>

        <section id="fluxo-fim-a-fim" class="guide" data-help-search-item>
            <h2>Fluxo completo (fim a fim)</h2>
            <p>Sequência recomendada para usar o app sem se perder:</p>
            <div class="guide-steps">
                <article class="guide-step">
                    <div class="num">1</div>
                    <div class="title">Entrar no sistema</div>
                    <div class="text">Faça login com seu usuário e senha no SWA.</div>
                </article>
                <article class="guide-step">
                    <div class="num">2</div>
                    <div class="title">Cadastrar condomínio</div>
                    <div class="text">Abra <strong>Condomínios</strong>, clique em <strong>Novo condomínio</strong> e salve.</div>
                </article>
                <article class="guide-step">
                    <div class="num">3</div>
                    <div class="title">Entrar no contexto</div>
                    <div class="text">Clique em <strong>Entrar</strong> no condomínio para abrir os módulos internos.</div>
                </article>
                <article class="guide-step">
                    <div class="num">4</div>
                    <div class="title">Cadastrar estrutura</div>
                    <div class="text">Cadastre <strong>Blocos, Pavimentos, Unidades e Áreas</strong>.</div>
                </article>
                <article class="guide-step">
                    <div class="num">5</div>
                    <div class="title">Criar templates</div>
                    <div class="text">Monte checklists reutilizáveis para agilizar as vistorias.</div>
                </article>
                <article class="guide-step">
                    <div class="num">6</div>
                    <div class="title">Abrir vistoria</div>
                    <div class="text">Use <strong>Vistorias</strong> e escolha área + template.</div>
                </article>
                <article class="guide-step">
                    <div class="num">7</div>
                    <div class="title">Registrar itens</div>
                    <div class="text">Marque status e anexe fotos quando houver não conformidade.</div>
                </article>
                <article class="guide-step">
                    <div class="num">8</div>
                    <div class="title">Finalizar vistoria</div>
                    <div class="text">Só finalize quando não houver pendências de evidência.</div>
                </article>
                <article class="guide-step">
                    <div class="num">9</div>
                    <div class="title">Gerar PDF</div>
                    <div class="text">No módulo de relatórios, gere o documento oficial da vistoria.</div>
                </article>
                <article class="guide-step">
                    <div class="num">10</div>
                    <div class="title">Enviar por e-mail</div>
                    <div class="text">Selecione destinatários e envie o relatório para síndico/conselho.</div>
                </article>
            </div>
        </section>

        <section class="help-block" data-help-search-item>
            <h2>Passo a passo detalhado</h2>

            <h3>1. Login e acesso inicial</h3>
            <ol class="help-list">
                <li>Abra a URL do sistema no navegador.</li>
                <li>Preencha e-mail e senha e clique em <strong>Entrar</strong>.</li>
                <li>Se aparecer <em>Credenciais inválidas</em>, confirme e-mail/senha e tente novamente.</li>
            </ol>

            <h3>2. Cadastro de condomínio</h3>
            <ol class="help-list">
                <li>Vá em <strong>Condomínios</strong>.</li>
                <li>Clique em <strong>Novo condomínio</strong>.</li>
                <li>Preencha os dados (nome, código, endereço, cidade/UF, status) e salve.</li>
                <li>Na lista, clique em <strong>Entrar</strong> para abrir o contexto interno.</li>
            </ol>

            <h3>3. Estrutura interna (ordem recomendada)</h3>
            <ol class="help-list">
                <li><strong>Blocos:</strong> cadastre os blocos do condomínio.</li>
                <li><strong>Pavimentos:</strong> cadastre por bloco (ex.: térreo, 1º, 2º).</li>
                <li><strong>Unidades:</strong> cadastre apartamento/sala com status ocupado ou vago.</li>
                <li><strong>Áreas:</strong> cadastre locais vistoriáveis (interna/externa).</li>
            </ol>

            <h3>4. Templates (checklist padrão)</h3>
            <ol class="help-list">
                <li>Abra <strong>Templates</strong>.</li>
                <li>Crie um template por categoria (Segurança, Extintores, Iluminação, etc.).</li>
                <li>Adicione itens com regra de foto obrigatória quando não estiver <strong>OK</strong>.</li>
                <li>Na criação da vistoria, aplique esse template para popular itens automaticamente.</li>
            </ol>

            <h3>5. Vistoria no celular (modo campo)</h3>
            <ol class="help-list">
                <li>Abra <strong>Vistorias</strong> e crie uma nova vistoria ou use o assistente.</li>
                <li>Selecione área/bloco/pavimento e o template desejado.</li>
                <li>Para cada item, escolha: <strong>OK</strong>, <strong>Danificado</strong>, <strong>Ausente</strong> ou <strong>Improvisado</strong>.</li>
                <li>Se status for diferente de OK, registre observação e foto de evidência.</li>
                <li>Use o botão <strong>Tirar Foto</strong> no item para abrir a câmera do celular.</li>
            </ol>

            <div class="help-tip">
                <strong>Onde tirar foto?</strong> A foto é registrada no próprio item da vistoria.
                Se o item estiver não conforme (Danificado/Ausente/Improvisado), a evidência é obrigatória para finalizar.
            </div>

            <h3>6. Continuar vistoria depois</h3>
            <ol class="help-list">
                <li>Abra <strong>Vistorias</strong> no mesmo condomínio.</li>
                <li>Encontre a vistoria em rascunho e clique em <strong>Editar</strong> ou <strong>Ver</strong>.</li>
                <li>Continue preenchendo itens e anexos até concluir.</li>
            </ol>

            <h3>7. Finalização e risco</h3>
            <ol class="help-list">
                <li>Revise pendências antes de finalizar.</li>
                <li>Classifique o risco geral em: <strong>Neutro</strong>, <strong>Baixo</strong>, <strong>Médio</strong> ou <strong>Alto</strong>.</li>
                <li>Clique em <strong>Finalizar vistoria</strong>.</li>
            </ol>

            <h3>8. PDF e envio por e-mail</h3>
            <ol class="help-list">
                <li>Vá em <strong>Relatórios</strong> e gere o PDF da vistoria.</li>
                <li>Baixe para conferência ou compartilhe por link assinado.</li>
                <li>Cadastre destinatários em <strong>E-mails</strong>.</li>
                <li>Use <strong>Enviar relatório</strong> para disparar por e-mail.</li>
            </ol>

            <h3>9. Conflitos e ocorrências</h3>
            <ol class="help-list">
                <li><strong>Conflitos:</strong> registre ocorrência entre moradores, unidade e status de mediação.</li>
                <li><strong>Ocorrências:</strong> registre fatos de funcionários, medida aplicada e andamento.</li>
                <li>Em ambos os módulos, anexe evidências quando necessário.</li>
            </ol>
        </section>

        <section id="faq" class="help-block" data-help-search-item>
            <h2>FAQ rápido (clique para expandir)</h2>
            <div class="help-faq">
                <details class="faq-item" open>
                    <summary>1) Onde tiro foto da vistoria?</summary>
                    <p>Dentro do item da vistoria. Ao marcar Danificado, Ausente ou Improvisado, use o botão Tirar Foto no próprio item.</p>
                </details>
                <details class="faq-item">
                    <summary>2) Posso continuar uma vistoria depois?</summary>
                    <p>Sim. A vistoria fica em rascunho. Volte em Vistorias e abra a mesma vistoria para continuar do ponto onde parou.</p>
                </details>
                <details class="faq-item">
                    <summary>3) Por que não consigo finalizar?</summary>
                    <p>Há pendência: item não OK sem evidência (foto e/ou observação). Complete as pendências e tente finalizar novamente.</p>
                </details>
                <details class="faq-item">
                    <summary>4) Como gerar o PDF oficial?</summary>
                    <p>Abra Relatórios no condomínio e clique em gerar para a vistoria desejada. O arquivo fica salvo e pode ser baixado depois.</p>
                </details>
                <details class="faq-item">
                    <summary>5) Como enviar para síndico/conselho?</summary>
                    <p>Cadastre destinatários em Configuração de E-mails e use a ação enviar relatório.</p>
                </details>
                <details class="faq-item">
                    <summary>6) Posso usar no celular em campo?</summary>
                    <p>Sim. O sistema é mobile-first. Dê permissão de câmera e use os botões grandes de status para agilizar o preenchimento.</p>
                </details>
                <details class="faq-item">
                    <summary>7) Como organizar o cadastro corretamente?</summary>
                    <p>Siga a ordem: Condomínio > Blocos > Pavimentos > Unidades > Áreas > Templates > Vistorias.</p>
                </details>
                <details class="faq-item">
                    <summary>8) O que é template?</summary>
                    <p>É um checklist padrão reutilizável. Você cria uma vez e aplica em várias vistorias para economizar tempo.</p>
                </details>
            </div>
        </section>

        <section class="help-block" data-help-search-item>
            <h2>Ações rápidas</h2>
            <div class="help-grid">
                @if ($ctx)
                    <a class="link-btn link-strong" href="{{ route('condominios.context.dashboard', $ctx) }}">Ir para painel do condomínio</a>
                    <a class="link-btn" href="{{ route('condominios.context.blocos.index', $ctx) }}">Cadastrar blocos</a>
                    <a class="link-btn" href="{{ route('condominios.context.pavimentos.index', $ctx) }}">Cadastrar pavimentos</a>
                    <a class="link-btn" href="{{ route('condominios.context.unidades.index', $ctx) }}">Cadastrar unidades</a>
                    <a class="link-btn" href="{{ route('condominios.context.areas.index', $ctx) }}">Cadastrar áreas</a>
                    <a class="link-btn" href="{{ route('condominios.context.templates.index', $ctx) }}">Gerenciar templates</a>
                    <a class="link-btn" href="{{ route('condominios.context.vistorias.wizard', $ctx) }}">Nova vistoria (assistente)</a>
                    <a class="link-btn" href="{{ route('condominios.context.relatorios.index', $ctx) }}">Relatórios</a>
                    <a class="link-btn" href="{{ route('condominios.context.emails.index', $ctx) }}">Configurar e-mails</a>
                @else
                    <a class="link-btn link-strong" href="{{ route('condominios.index') }}">Ir para condomínios</a>
                    <a class="link-btn" href="{{ route('dashboard') }}">Voltar para início</a>
                @endif
            </div>
        </section>

        <section class="help-block" data-help-search-item>
            <h2>Dicas para uso no celular</h2>
            <ol class="help-list">
                <li>Use o celular em modo retrato ou paisagem; o app adapta os cards.</li>
                <li>Dê permissão de câmera no navegador para usar o botão <strong>Tirar Foto</strong>.</li>
                <li>Prefira preencher vistoria por área para não perder sequência.</li>
                <li>Antes de sair, confira se os itens não OK têm evidência completa.</li>
                <li>Use o botão <span class="help-kbd">Ajuda</span> no topo sempre que houver dúvida operacional.</li>
            </ol>
        </section>
    </div>
    <script>
        (function () {
            const input = document.getElementById('help-search-input');
            const counter = document.getElementById('help-search-count');
            const sections = Array.from(document.querySelectorAll('[data-help-search-item]'));

            if (!input || !counter || sections.length === 0) {
                return;
            }

            const normalize = (text) => String(text || '')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .trim();

            const applyFilter = () => {
                const rawQuery = input.value || '';
                const query = normalize(rawQuery);
                let visibleCount = 0;

                sections.forEach((section) => {
                    const content = normalize(section.innerText);
                    const visible = query === '' || content.includes(query);
                    section.style.display = visible ? '' : 'none';
                    if (visible) {
                        visibleCount += 1;
                    }
                });

                if (query === '') {
                    counter.textContent = 'Mostrando todo o conteúdo da ajuda.';
                    return;
                }

                counter.textContent = `${visibleCount} seção(ões) encontrada(s) para "${rawQuery.trim()}".`;
            };

            input.addEventListener('input', applyFilter);
        })();
    </script>
@endsection
