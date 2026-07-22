# Contributing to attr.click

Thanks for improving attr.click. This project is intentionally small at its core: reliable redirects, honest attribution, and excellent QR output.

## Before you code

1. Read [AGENTS.md](AGENTS.md) and the relevant memory-bank files.
2. Search open issues and pull requests for duplicate work.
3. Open a focused issue for material product or architectural changes.
4. Create a branch from `main` using `type/short-description`.

## Development

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan test
./vendor/bin/pint --test
npm run build
```

Use SQLite locally. CI and production must be exercised against MySQL before a release that changes query behavior, migrations, indexes, or analytics aggregation.

## Pull requests

- Keep a PR to one behavior slice.
- Write tests before implementation and include expected failure/pass evidence in the PR description.
- Include a visual capture for UI changes.
- Explain all new files and migration effects.
- Never commit credentials, real invite codes, real production URLs with sensitive query strings, or user data.
- Update user-facing documentation when behavior changes.

## Style

Follow Laravel conventions, `./vendor/bin/pint`, Tailwind/Flux primitives, accessible semantic HTML, and route-level authorization. Prefer a service/action only once behavior is shared by multiple callers; do not turn every controller action into an abstraction.
