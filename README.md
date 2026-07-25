# attr.click

A self-hosted, invitation-only short-link and QR-code platform for teams that care about a clean brand surface, durable attribution, and owning their data.

## What it does

- Passwordless, invitation-only onboarding
- Short links on `attr.click`
- Lossless query-parameter capture and forwarding
- Automatic, customizable QR codes for each short link
- Reusable QR appearance templates
- First-party click events and attribution reporting

## Using attr.click

The public [user guide](https://attr.click/help) explains invitations, short-link creation, QR customization, templates, analytics, and the product's privacy boundaries. It is readable without an account.

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

## Production deployment

Production intentionally does not install frontend development dependencies. Envoy builds Vite assets locally on the machine invoking deployment, uploads those assets before the application source, refreshes Composer dependencies and Laravel caches, restarts the user service, and checks `/up`.

For a normal release, run one command:

```bash
vendor/bin/envoy run deploy
```

For the QR lifecycle release, use the dedicated one-command recipe. It performs the same deployment flow and then reissues every QR from its query-free canonical short URL:

```bash
vendor/bin/envoy run deploy_qr_lifecycle
```

List the available Envoy recipes with:

```bash
vendor/bin/envoy tasks
```

The target is `michael@umacbookpro:/home/michael/Sites/attr.click`. The workflow preserves remote `.env` and `storage/`; production secrets and user-generated files are never copied from the local checkout. Note that Envoy 2.12's `--pretend` mode intentionally prints one task then exits nonzero, so use it only for inspecting an individual task—not as a successful full-story dry run.

## Project docs

- [Public user guide](https://attr.click/help)
- [Product requirements](docs/PRD.md)
- [Release plan](docs/RELEASE_PLAN.md)
- [Architecture](docs/ARCHITECTURE.md)
- [Security & privacy](docs/SECURITY_AND_PRIVACY.md)
- [Contributing](CONTRIBUTING.md)
- [Agent workflow](AGENTS.md)

## License

MIT. See [LICENSE](LICENSE).
