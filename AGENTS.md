# SWA Agent Notes

## Mandatory First Step

Before making any change, read `docs/OPERACAO_SWA.md`.

That file is the source of truth for:
- production app and URL
- backup routine
- database handling
- architectural decisions
- current UX direction
- operational safety rules

## Non-Negotiable Rules

- Do not use `php artisan migrate:fresh`.
- Do not use `php artisan db:wipe`.
- Do not reset or recreate the production database.
- Treat all schema changes as additive unless the user explicitly approves otherwise.
- Generate and download an in-app backup before structural changes.
- Prefer updating existing patterns over introducing parallel UI patterns.

## Product Direction

- The app must stay usable on both desktop and mobile.
- Mobile comfort is a priority.
- Avoid visual repetition, noisy navigation, and oversized explanatory blocks.
- The current direction is:
  - desktop with sidebar navigation
  - mobile with drawer/hamburger navigation
  - focused create/edit screens with reduced chrome
