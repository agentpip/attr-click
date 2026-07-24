# Progress

## Delivered through invite-only beta

- Laravel 13 / Livewire / Flux / Endroid QR foundation, local SQLite and documented production MySQL path.
- Invitation-only verification, passwordless login, owner-scoped link and QR management, stable query-forwarding redirect contract, first-party click/UTM reporting, QR palettes/templates/private logos, QR lifecycle reissue flow, and a Laravel-native admin slice for global stats, creator role management, and invitation operations.
- Deployed `https://attr.click` behind Caddy with an Envoy asset-first deployment workflow and `/up` health probe.
- Local QA on 2026-07-23: 41 tests / 173 assertions passed; Pint passed; Vite build passed; local invite verification completed through Mailpit before admin promotion; `composer audit --locked` and `npm audit --omit=dev --audit-level=high` reported no known advisories.

## Current release gate — deploy and canary security remediation

- Source remediation is complete and locally tested: centralized public-target admission, UTM-only bounded analytics, no auth-link flash storage, production log-mailer refusal, and browser-security middleware.
- Preserve the working production SMTP transport, deploy the reviewable source slice, and run non-mutating live header plus unauthenticated dashboard redirect canaries. Confirm the prior live HTTP 500 is absent before promotion.
- Re-run source, dependency, and live external-surface canaries after deployment; only then run `links:regenerate-qr` in production.

## Current infrastructure gate — replace the development server

- Production currently runs `php artisan serve` from the enabled `attr-click.service` user unit on loopback port 8092; Caddy proxies `attr.click` to that port.
- Prepared repository configuration in `deploy/apache/` adds a loopback-only Apache vhost with Laravel's `public/` document root and an explicit rewrite front controller. Candidate syntax validation passed against production Apache 2.4.64.
- Apache's root-owned system service is the replacement runtime. The user unit is retired only after a successful Apache reload and `/up` health check. Caddy/TLS configuration remains unchanged.
- Interactive sudo is required to install the Apache fragments, grant `www-data` write access to `storage/` and `bootstrap/cache/`, and reload Apache; noninteractive sudo is unavailable on UMacBookPro.

## Deferred public-v1 hardening

- MySQL rehearsal, backup/restore runbook, queue/scheduler observability, rate/abuse controls, CI, contributor release material, and invite-only beta feedback triage as defined in `docs/RELEASE_PLAN.md`.