# Project Rules

- Laravel conventions first; introduce an action/service when behavior has more than one caller or needs independent testing.
- New behavior begins with a focused failing test.
- Public URL, redirect, QR, and analytics contracts require feature/integration tests, not unit tests alone.
- Use Flux components where activated; preserve accessible labels, errors, focus, and keyboard navigation.
- Never report sensitive query values or raw IPs to creators.
- Destination admission is shared between create/update and fails closed for local, private, reserved, or unresolved hosts; it needs literal-IP and resolver/rebinding test coverage.
- Analytics persistence must use a small explicit attribution allowlist with value/count bounds. Do not rely solely on a sensitive-key denylist to prevent collection of PII.
- Authentication and verification URLs are credentials: send them only through the configured mail channel and never retain their plaintext form in session flash, logs, views, or analytics.
- Admin route protection and admin navigation visibility must share one Laravel ability so ordinary creators cannot discover or reach admin surfaces through UI drift.
- Before a public release, verify the live HTTPS response has HSTS, CSP, frame protection, `nosniff`, Referrer-Policy, Permissions-Policy, and no framework-version disclosure; include an unauthenticated auth-route canary.
