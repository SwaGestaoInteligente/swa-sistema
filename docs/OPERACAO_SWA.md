# Operacao SWA

## Leitura obrigatoria para qualquer novo chat ou agente

Este arquivo deve ser lido antes de qualquer alteracao.
Ele concentra o estado real do projeto, as decisoes de arquitetura, a rotina de backup e as regras para nao perder dados.

## Estado atual do projeto

- O sistema esta funcional e em producao.
- O foco atual nao e reconstruir backend; e refinar UX, principalmente para celular.
- O produto ja cobre:
  - condominio
  - estrutura
  - templates
  - vistorias
  - conflitos
  - ocorrencias
  - relatorios
  - e-mails
  - ajuda
  - backups
- A principal dor atual do usuario e UX:
  - excesso visual
  - repeticao
  - navegacao desconfortavel
  - tela administrativa demais para uso em campo

## Arquitetura adotada

- Base tecnica inspirada no Multi-System, mas em repositorio isolado
- Laravel + Blade + PostgreSQL + Docker + Fly.io
- Padrao: modular monolith
- Estrutura principal:
  - MVC para HTTP e views
  - Service Layer para regras sensiveis
  - middleware/policies para contexto e seguranca
- Multi-tenant logico por `condominio_id`
- A raiz do sistema e a lista de condominios
- Tudo dentro do produto deve operar a partir do contexto de um condominio

## Direcao de UX que deve ser mantida

- Desktop:
  - sidebar agrupada
  - consulta, cadastro e detalhe com shells visuais distintos
- Mobile:
  - menu drawer/hamburguer
  - menos navegacao exposta ao mesmo tempo
  - prioridade para a acao principal
- Formularios:
  - telas focadas
  - sem menu grande
  - menos texto auxiliar
- Nao voltar para:
  - faixa longa de botoes no topo
  - repeticao de contexto
  - cards e blocos desnecessarios

## Producao

- App Fly: `swa-mobile-aged-fog-2881`
- URL publica: `https://swa-mobile-aged-fog-2881.fly.dev`
- Regiao principal: `gru`
- Runtime: `php-fpm + nginx`
- Release command: `php artisan migrate --force --seed`

## Banco em uso

- Origem: secret `DATABASE_URL`
- Tipo: PostgreSQL gerenciado pela Fly via endpoint `flympg.net`
- Observacao: o valor do secret nao aparece no painel; consultar apenas via `fly ssh console` quando estritamente necessario

## Rotina obrigatoria antes de alterar estrutura

1. Entrar no condominio em producao.
2. Abrir a tela `Backups`.
3. Clicar em `Gerar novo backup`.
4. Clicar em `Baixar`.
5. Guardar o `.zip` fora do navegador.

Link de exemplo do condominio atual:

- `https://swa-mobile-aged-fog-2881.fly.dev/condominios/019c9aa5-a241-7135-885a-b19cb4a8ee45/backups`

## O que o backup interno cobre

- Condominio
- Vinculos de usuarios e perfis
- Blocos
- Pavimentos
- Unidades
- Areas
- Templates
- Vistorias e itens
- Conflitos
- Ocorrencias
- Anexos
- Relatorios
- E-mails

## Robustez do backup

- O arquivo `.zip` continua sendo salvo em disco local.
- O mesmo conteudo tambem e persistido no banco em `backups.payload_base64`.
- Se outra maquina do Fly atender o download e o arquivo local nao existir nela, o sistema entrega o backup pelo conteudo salvo no banco.

## Deploy padrao

```bash
fly deploy --app swa-mobile-aged-fog-2881
```

## Regras de seguranca operacional

- Nao usar `php artisan migrate:fresh`
- Nao usar `php artisan db:wipe`
- Nao resetar banco de producao
- Toda migration deve ser aditiva
- Sempre gerar backup antes de mudanca estrutural

## Observacoes

- Se o browser mostrar erro ao baixar backup em ambiente multi-maquina, validar se a migration `payload_base64` foi aplicada e se o backup foi gerado apos essa versao.
- Dumps brutos de PostgreSQL ficam como opcao secundaria; o caminho primario e o backup interno do app.

## Licoes aprendidas com os erros do projeto

- Nao adaptar interface administrativa crua para celular.
- Nao usar navegacao longa e horizontal para muitos modulos.
- Nao repetir contexto e cabecalhos em excesso.
- Nao misturar tela de cadastro com navegacao pesada.
- Nao continuar evoluindo sem antes fechar rotina de backup e operacao.
- Nao pensar so no codigo; runtime, storage, extensoes e deploy tambem fazem parte da arquitetura.

## Norte pratico para as proximas iteracoes

1. Preservar dados e backup antes de mudanca estrutural.
2. Simplificar o dashboard do condominio.
3. Reduzir ruudo visual em mobile.
4. Priorizar fluxo de vistoria em campo sobre elementos administrativos.
5. Manter uma unica linguagem visual, sem criar padroes paralelos.
