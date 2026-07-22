# attr.click Release Plan

## Release principles

- Ship the redirect/data contract before the polished dashboard: printed QR codes are durable public infrastructure.
- Each phase is a mergeable vertical slice with migrations, automated tests, representative runtime verification, and rollback notes.
- Use semantic releases only when a deployable milestone exists; do not invent dates or release IDs.

## Phase 0 — Foundation (current)

**Outcome:** an open-source Laravel repository that any contributor can run locally.

- Laravel 13, Livewire 4, Tailwind, Flux dependency, Endroid QR package.
- SQLite local defaults; documented MySQL production path.
- Project constitution: PRD, architecture, privacy/security, contribution, memory bank, CI plan.
- CI gates: PHP tests, Pint, Vite production build, dependency audit.

**Exit:** fresh checkout completes install, migration, test, and build locally; public GitHub repository has license and contributing guidance.

## Phase 1 — Trusted access and link core

**Outcome:** invited verified users can create/manage reliable short links.

1. Invitation-code model, admin command/UI, rate-limited registration intent.
2. Signed email verification + passwordless magic-link session flow.
3. Link model, slug allocation, destination/query normalization service, ownership authorization.
4. Redirect controller, click event persistence, allowlisted query merge contract, branded disabled/missing pages.
5. Creator dashboard: create, list, edit, disable, copy canonical link.

**Representative validation:** mailer log shows a real signed verification URL; HTTP test follows it; browser validates creator flow; curl verifies 302 `Location` with query merging.

**Rollback:** deploy schema additively; public redirect routes remain backward compatible; disable a faulty link instead of deleting it.

## Phase 2 — QR studio and export

**Outcome:** every link has a production-ready, configurable QR asset.

1. QR configuration value object/schema and default configuration.
2. SVG/PNG generator with contrast/quiet-zone/error-correction guardrails.
3. Private logo upload pipeline and image validation/transformation.
4. Live preview/customizer and download endpoints.
5. Decoder-based test matrix across default/color/logo configurations.

**Representative validation:** scan each generated PNG/SVG with an independent decoder and verify exact canonical URL payload.

**Rollback:** retain the last validated asset/configuration; never overwrite a live asset until its replacement passes decode validation.

## Phase 3 — Templates and attribution reporting

**Outcome:** creators reuse brand looks and understand results.

1. Template CRUD, ownership checks, template application snapshot.
2. Click aggregation jobs/indexes; link detail reporting.
3. Query parameter summary with sensitive-key filtering and retention configuration.
4. Empty-state and privacy copy review.

**Representative validation:** generated requests with controlled referrer/query headers aggregate accurately; sensitive keys never appear in persistence/UI exports.

## Phase 4 — Operational hardening and public v1

**Outcome:** attr.click is safe to run on UMBP and useful to outside contributors.

- MySQL migration rehearsal against a fresh instance and production-like dataset.
- Caching for redirect lookup, queue worker/scheduler, structured logging, health endpoint, backup/restore runbook.
- Abuse/rate-limit review, security headers, URL validation, upload threat-model review.
- GitHub Actions, release notes, install guide, issue/PR templates.
- Invite-only beta, feedback triage, then public source release.

## Deferred backlog

Custom domains; workspace roles; shared templates; CSV/bulk creation; REST API/webhooks; branded interstitial pages; A/B destinations; multi-region redirect edge; billing.

## Release gates

1. All migrations are reversible or have a tested forward-only remediation.
2. Full tests, Pint, Vite build, and dependency audit pass in CI.
3. Redirect canary validates real `Location` handling for captured and incoming query parameters.
4. QR canary independently decodes each supported output style.
5. Auth and ownership test suite passes against SQLite and MySQL before a production schema release.
6. Security/privacy checklist is signed off; no sensitive parameters enter analytics data.
