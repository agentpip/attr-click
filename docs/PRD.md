# attr.click Product Requirements Document

**Status:** Foundation scope  
**Owner:** Michael Sitarzewski  
**Product thesis:** The best QR-link workflow is not a generic marketing suite. It is a fast, private, beautiful utility that turns a destination into a durable, attributable link and a production-ready QR asset in one flow.

## 1. Problem

Teams currently juggle a shortener, a QR generator, image export, UTM spreadsheets, and opaque analytics. That creates broken attribution, inconsistent visual assets, and links that cannot be changed safely after print.

## 2. Product outcomes

1. An invited collaborator can verify their email and create a live short link in under 60 seconds.
2. Pasting a destination preserves its query parameters as first-party attribution data and encodes the canonical attributed short URL into the QR code.
3. Every link immediately has a scannable QR code that can be branded, previewed, downloaded, and reused through templates.
4. Owners can understand clicks, captured parameters, and QR configuration without surveillance-adtech behavior.

## 3. Users

- **Owner/admin:** invites collaborators, controls access, manages platform guardrails.
- **Creator:** creates and manages links, QR assets, and templates.
- **Viewer/scanner:** opens a short URL or scans a QR code; no account required.

## 4. v1 functional requirements

### 4.1 Invitation-only passwordless access

- A user enters an email and a valid active invitation code.
- The system sends a single-use, expiring verification link to that email.
- Following it verifies ownership, creates/activates the account, consumes the invite if it has a use limit, and starts an authenticated session.
- Existing users sign in through an emailed magic link; no passwords exist.
- Invalid, expired, exhausted, or mismatched invitations return the same non-enumerating response.
- Admins can create, disable, expire, and limit invitation codes.

### 4.2 Short-link creation and redirect behavior

- Creator pastes an absolute `http` or `https` destination URL.
- System normalizes the URL, stores its base URL and an ordered map of its original query parameters, then allocates a unique, readable random slug.
- Creator may choose an unused custom slug subject to reserved-route and profanity/abuse rules.
- The canonical public URL is `https://attr.click/{slug}` plus the original query string. This exact URL is copied and encoded into the QR code.
- On a public request, the system records a first-party click event and 302-redirects to the stored destination with stored parameters plus permitted incoming parameters. Collision policy: incoming values override stored keys; repeated keys retain their original order.
- Redirect preserves fragments only when stored as part of destination metadata; browser requests never transmit URL fragments to the server.
- Link owners can disable or edit a destination. A disabled link returns a branded 410 page, not an open redirect.

### 4.3 QR generation and customization

- Automatically generate SVG and PNG QR assets for each canonical short URL.
- Defaults: high error correction, square dark modules, white quiet zone, sufficiently large quiet margin.
- Creator can change foreground/background color, module shape, corner treatment, margin, error-correction level (with safe defaults), logo upload/placement, and output dimensions.
- Preview updates before save; server validates contrast, logo footprint, quiet zone, and decodability constraints before publishing a configuration.
- SVG download remains vector; PNG is generated at chosen output dimensions.
- Uploaded logos are validated image files, stripped/transformed server-side, stored privately, and referenced by signed internal access.

### 4.4 QR templates

- Creator saves current QR settings as a named template.
- Templates are scoped to creator in v1; organization/shared templates are a future role/workspace feature.
- New links can apply a template before the QR is generated.
- Updating a template never silently rewrites existing QR assets.

### 4.5 Attribution and reporting

- Each redirect records timestamp, link, safely normalized request metadata, referrer host when available, and effective query parameter keys/values subject to privacy filters.
- Link detail shows total clicks, last click, daily trend, referrer hosts, and an attribution-parameter summary.
- Exclude known sensitive keys (`token`, `code`, `password`, `secret`, `key`, `signature`, case-insensitive) from event persistence and UI.
- No third-party analytics SDK, cross-site identity, fingerprinting, or outbound telemetry.

## 5. Non-functional requirements

| Area | Requirement |
|---|---|
| Redirect latency | p95 application redirect under 100 ms excluding network transit; cacheable link lookup once correctness is proven. |
| Availability | Redirect path is independently observable and deployable with migration-safe releases. |
| Privacy | Data minimization, documented retention settings, no full raw IP retention in v1. |
| Security | Authenticated ownership checks on every creator action; URL scheme validation; rate limits on registration, magic links, and redirects. |
| Accessibility | WCAG AA contrast, semantic forms, keyboard-operable customizers, text alternatives. |
| Portability | SQLite works locally; MySQL 8-compatible migrations/indexes in production. |

## 6. Explicit non-goals for v1

- Public self-service signup
- Custom domains and per-workspace domains
- Billing, plans, seats, or SSO
- Link-in-bio pages
- Bulk import/API/webhooks
- Third-party analytics integrations
- Dynamic QR destinations based on scanner geography/device

## 7. Acceptance criteria

- Invite submission with a valid code produces a signed email link; opening it authenticates a verified user exactly once.
- Creating `https://example.com/a?utm_source=print&utm_campaign=launch` produces a short URL whose QR payload includes both parameters and redirects to the expected destination.
- An owner can choose colors, upload an accepted logo, export a valid SVG and PNG, save settings as a template, and reuse them on a second link.
- Tests cover invalid URLs, reserved/custom slugs, invite expiration/use limits, query merging/collision behavior, unauthorized access, and QR generation with/without logos.
- App runs locally with SQLite and passes MySQL compatibility checks before first production deploy.
