# Progress

## Delivered through invite-only beta

- Laravel 13 / Livewire / Flux / Endroid QR foundation, local SQLite and documented production MySQL path.
- Invitation-only verification, passwordless login, owner-scoped link and QR management, stable query-forwarding redirect contract, first-party click/UTM reporting, QR palettes/templates/private logos, QR lifecycle reissue flow, and a Laravel-native admin slice for global stats, creator role management, and invitation operations.
- Creator-owned deletion: a detail-page danger zone requires the exact short slug before permanently deleting the short link, dynamic QR resolution, private QR logo, and cascade-owned scan analytics. Deletion is owner-only; former public slugs return `404`.
- GitHub transferred the canonical public repository to `msitarzewski/attr-click`; local `origin` now uses the canonical URL and public repository links are updated in the source. The production directory is an rsync artifact with no Git remote, so it is not a fork and will receive this public-link update through the next normal deployment from merged `main`.
- PRs #6 and #7 merged public-safe deployment templates and sanitized operator references into `main` as `4a7897d` and `82c378d`; they require local `ATTR_CLICK_DEPLOY_HOST`/`ATTR_CLICK_DEPLOY_PATH` configuration for future Envoy runs and intentionally do not alter the deployed application runtime.
- PR #5 merged the public user guide as `77897c6`; Envoy deployed it from `origin/main` only on 2026-07-25. `/help` is account-free, reachable from public navigation and the homepage footer, and covers access, links, QR templates, lifecycle, first-party analytics, and privacy. The README points readers to it. Public HTTPS `/help`, login/guest-auth redirect, and loopback health canaries passed; deployed routes/layouts/views match source hashes without creating production data.
- PR #3 merged homepage share metadata as `530bb33`; Envoy deployed it from `origin/main` only on 2026-07-24. The homepage now serves canonical, Open Graph, and Twitter/X large-image-card metadata plus a public 1200×630 PNG card. Facebook and Twitter crawler probes both received a complete HTTPS tag set; the image returns `200 image/png`. Public HTTPS, login/guest-auth redirect, and loopback health canaries passed without creating production data.
- PR #2 merged this deletion slice as `bf03ccf`; Envoy deployed it from `origin/main` only on 2026-07-24. The production route table contains `DELETE links/{link:slug}` and deployed controller/routes/view hashes match the released source. Public HTTPS, CSP/HSTS/Vite-asset, login/guest-auth redirect, and loopback health canaries passed without creating production data.
- Deployed `https://attr.click` behind Caddy with an Envoy asset-first deployment workflow and `/up` health probe. Release `ce6a944` was deployed from `origin/main` only on 2026-07-24; the build, asset/application sync, Composer production install, migrations, cache optimization, and loopback health completed successfully.
- Local QA on 2026-07-24: 42 tests / 179 assertions passed; Pint passed; Vite build passed; local invite verification completed through Mailpit before admin promotion. Production canaries confirmed HTTPS Vite assets, browser-security headers, `/login` 200, and guest redirects for `/dashboard` and `/admin` without creating production data.
- Local deletion-slice QA on 2026-07-24: 46 tests / 199 assertions passed; Pint and Vite build passed. A real local magic-login browser flow confirmed the rendered deletion controls, dashboard redirect after deletion, and `404` for the deleted public slug.

## Current release gate — completed application deploy; Apache cutover remains

- Source remediation is complete and deployed: centralized public-target admission, UTM-only bounded analytics, no auth-link flash storage, production log-mailer refusal, and browser-security middleware.
- Preserve the working production SMTP transport. The non-mutating live header and guest redirect canaries passed after deployment; do not run `links:regenerate-qr` until the Apache cutover is complete and healthy.

## Current infrastructure gate — replace the development server

- Production currently runs `php artisan serve` from the enabled `attr-click.service` user unit on loopback port 8092; Caddy proxies `attr.click` to that port.
- Prepared repository configuration in `deploy/apache/` adds a loopback-only Apache vhost with Laravel's `public/` document root and an explicit rewrite front controller. Candidate syntax validation passed against production Apache 2.4.64.
- Apache's root-owned system service is the replacement runtime. The user unit is retired only after a successful Apache reload and `/up` health check. Caddy/TLS configuration remains unchanged.
- Interactive sudo is required to install the Apache fragments, grant `www-data` write access to `storage/` and `bootstrap/cache/`, and reload Apache; noninteractive sudo is unavailable on the production host.

## Deferred public-v1 hardening

- MySQL rehearsal, backup/restore runbook, queue/scheduler observability, rate/abuse controls, CI, contributor release material, and invite-only beta feedback triage as defined in `docs/RELEASE_PLAN.md`.