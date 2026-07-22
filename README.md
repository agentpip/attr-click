# attr.click

A self-hosted, invitation-only short-link and QR-code platform for teams that care about a clean brand surface, durable attribution, and owning their data.

## What it does

- Passwordless, invitation-only onboarding
- Short links on `attr.click`
- Lossless query-parameter capture and forwarding
- Automatic, customizable QR codes for each short link
- Reusable QR appearance templates
- First-party click events and attribution reporting

## Local development

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run dev
php artisan serve
```

The generated `.env` uses SQLite (`database/database.sqlite`). For production, set `DB_CONNECTION=mysql` and the `DB_*` values for the UMBP MySQL instance, then run `php artisan migrate --force`.

## Flux UI

Flux is installed as a dependency. Activate it locally and in CI with the licensed Flux account:

```bash
php artisan flux:activate you@example.com YOUR_FLUX_LICENSE_KEY
```

The activation credential belongs in the deployment/CI secret store, never in git or `.env.example`.

## Project docs

- [Product requirements](docs/PRD.md)
- [Release plan](docs/RELEASE_PLAN.md)
- [Architecture](docs/ARCHITECTURE.md)
- [Security & privacy](docs/SECURITY_AND_PRIVACY.md)
- [Contributing](CONTRIBUTING.md)
- [Agent workflow](AGENTS.md)

## License

MIT. See [LICENSE](LICENSE).
