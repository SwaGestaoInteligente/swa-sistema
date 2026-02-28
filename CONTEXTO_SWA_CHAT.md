# CONTEXTO SWA PARA NOVO CHAT

Leia este arquivo antes de qualquer alteracao no projeto.
Ele resume o estado atual do SWA e evita perder tempo reexplicando contexto.

## Projeto

- Nome: `swa-mobile`
- Pasta local: `c:\projetos\SGI_legado\swa-mobile`
- Stack: Laravel 12, Blade, PostgreSQL, Docker, Fly.io
- App Fly: `swa-mobile-aged-fog-2881`
- URL de producao: `https://swa-mobile-aged-fog-2881.fly.dev`

## Regra principal

Nao perder dados.

Nunca usar:

- `php artisan migrate:fresh`
- `php artisan db:wipe`
- reset de banco de producao

Toda mudanca estrutural deve ser aditiva.

## Backup obrigatorio antes de mudar estrutura

1. Entrar no condominio em producao
2. Abrir `Backups`
3. Clicar em `Gerar novo backup`
4. Clicar em `Baixar`
5. Guardar o `.zip`

Exemplo do condominio atual:

- `https://swa-mobile-aged-fog-2881.fly.dev/condominios/019c9aa5-a241-7135-885a-b19cb4a8ee45/backups`

## O que ja existe

O sistema ja esta funcional e em producao, com:

- condominios
- blocos
- pavimentos
- unidades
- areas
- templates
- vistorias
- conflitos
- ocorrencias
- relatorios
- e-mails
- ajuda
- backups

## Arquitetura usada

- Base inspirada no Multi-System, mas em repositorio isolado
- Padrao: modular monolith
- MVC para HTTP e views
- Service Layer para regras sensiveis
- Middleware e Policies para contexto e seguranca
- Multi-tenant logico por `condominio_id`
- A raiz do sistema e a lista de condominios
- Tudo dentro do produto deve operar a partir do contexto de um condominio

## Estado atual da UX

O backend ja esta em nivel utilizavel.
O foco atual e UX, principalmente mobile.

Ja foi feito:

- login em card unico
- telas de create/edit mais focadas
- sidebar no desktop
- drawer/hamburguer no mobile
- melhorias no fluxo de vistoria

## Dor atual do usuario

O usuario ainda acha o sistema:

- poluido
- repetitivo
- pouco confortavel no celular
- administrativo demais para uso em campo

## Direcao correta para as proximas iteracoes

Prioridade:

1. Simplificar a navegacao
2. Reduzir repeticao visual
3. Enxugar o dashboard do condominio
4. Dar foco ao fluxo de vistoria em campo
5. Evitar criar padroes visuais paralelos

## O que nao fazer

- Nao voltar para menu horizontal longo no topo
- Nao repetir contexto em varios lugares sem necessidade
- Nao lotar telas com cards e textos auxiliares
- Nao misturar tela de cadastro com navegacao pesada

## Comando padrao de deploy

```bash
fly deploy --app swa-mobile-aged-fog-2881
```

## Banco

- Usa secret `DATABASE_URL`
- Banco PostgreSQL via endpoint `flympg.net`
- O valor do secret nao aparece no painel web

## Licoes aprendidas

- Funcionar nao basta; no celular, a navegacao precisa ser pensada como produto de campo
- Runtime tambem e arquitetura: extensoes PHP, storage e deploy importam
- Backup precisa estar fechado antes de continuar evoluindo

## Arquivos de referencia

Se precisar de mais detalhe:

- `AGENTS.md`
- `docs/OPERACAO_SWA.md`
