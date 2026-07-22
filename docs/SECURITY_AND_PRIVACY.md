# Security & Privacy

## Product stance

attr.click exists to preserve useful attribution without becoming surveillance infrastructure. It keeps first-party, link-scoped event data and does not load third-party analytics, create cross-site identities, or fingerprint scanners.

## Data minimization

- Store a truncated/hashed network identifier only if abuse prevention requires it; do not retain raw IP addresses in reporting data.
- Persist referrer host, not full referrer path/query.
- Filter analytics storage for sensitive query keys: `token`, `code`, `password`, `secret`, `key`, `signature` and configured additions. Never display them in reports.
- Publish retention defaults and make them configurable before public beta.

## Threats and controls

| Threat | Control |
|---|---|
| Open redirect abuse | Parse and validate destination URL; allow only http/https; block internal/private targets; audit edits. |
| Invitation brute force | Hash codes; rate limit by IP/email/code; use generic failure responses; expire/revoke/use-limit codes. |
| Magic-link replay | Store only a token hash; short expiry; atomic single-use consume; invalidate on use. |
| Account takeover | Signed/hashed login links, same-site secure sessions, email verification, session rotation. |
| Query-string leakage | Separate forwarding from analytics persistence; scrub sensitive keys from click records and logs. |
| QR unreadability | Safe defaults, contrast/quiet-zone/logo-size validation, independent decode tests. |
| Upload attacks | MIME/content inspection, size/dimension limits, raster transform, private storage, signed retrieval. |
| Dashboard IDOR | Route model binding plus policies; tests for every owner-scoped action. |

## Deployment checklist

- HTTPS-only canonical `APP_URL=https://attr.click`
- Secure cookies, HSTS, CSP, `X-Content-Type-Options`, frame policy
- MySQL credentials from secret manager, not source control
- Queue worker and scheduler running with observability/alerting
- Backups encrypted and restore-tested
- Separate access logs from application analytics; scrub query strings in logs where possible
