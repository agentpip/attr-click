# Security & Privacy

## Product stance

attr.click exists to preserve useful attribution without becoming surveillance infrastructure. It keeps first-party, link-scoped event data and does not load third-party analytics, create cross-site identities, or fingerprint scanners.

## Data minimization

- Store a truncated/hashed network identifier only if abuse prevention requires it; do not retain raw IP addresses in reporting data.
- Persist referrer host, not full referrer path/query.
- Persist only standard UTM dimensions (`utm_source`, `utm_medium`, `utm_campaign`, `utm_content`, `utm_term`) in click attribution. Each retained value is capped at 120 characters; all other query data is forwarded but never stored.
- Publish retention defaults and make them configurable before public beta.

## Threats and controls

| Threat | Control |
|---|---|
| Open redirect abuse | One destination-admission policy handles both creation and editing: allow only absolute HTTP(S) URLs, reject credentials, local/single-label hosts, literal private/reserved IPs, and hostnames that do not resolve exclusively to public IPs. |
| Invitation brute force | Hash codes; rate limit by IP/email/code; use generic failure responses; expire/revoke/use-limit codes. |
| Magic-link replay | Store only a token hash; short expiry; atomic single-use consume; invalidate on use. |
| Account takeover | Signed/hashed login links, same-site secure sessions, email verification, session rotation, and no plaintext auth URL in flash/session state. Production refuses to issue passwordless credentials through the log mailer. |
| Query-string leakage | Separate forwarding from analytics persistence; scrub sensitive keys from click records and logs. |
| QR unreadability | Safe defaults, contrast/quiet-zone/logo-size validation, independent decode tests. |
| Upload attacks | MIME/content inspection, size/dimension limits, raster transform, private storage, signed retrieval. |
| Dashboard IDOR | Route model binding plus policies; tests for every owner-scoped action. |
| Browser injection/clickjacking | App middleware sets CSP, `frame-ancestors 'none'`, `X-Frame-Options: DENY`, `nosniff`, strict referrer policy, restrictive Permissions-Policy, and same-origin cross-origin policies. HSTS is emitted only for secure requests. The current Flux/Alpine runtime requires CSP `unsafe-inline` and `unsafe-eval` for appearance controls and other reactive interactions; retain the restrictive source directives and revisit this exception when the UI runtime offers a CSP-safe build. |

## Deployment checklist

- HTTPS-only canonical `APP_URL=https://attr.click`
- Secure cookies, HSTS, CSP, `X-Content-Type-Options`, frame policy
- MySQL credentials from secret manager, not source control
- Queue worker and scheduler running with observability/alerting
- Backups encrypted and restore-tested
- Separate access logs from application analytics; scrub query strings in logs where possible

## Security verification — 2026-07-23

Source remediation is covered by feature tests for private/localhost target rejection, bounded UTM-only analytics persistence, absence of plaintext login/verification URLs from session flash, safe refusal of production log-mailer delivery, unauthenticated dashboard redirect behavior, and browser-security headers. Local browser verification also confirms that Flux appearance selection works in light and dark modes with a single active icon. Production passwordless email already uses a real SMTP transport; keep its credentials out of source control. Before deployment, run the repository QA gates. After deployment, verify live HTTPS headers, an unauthenticated `/dashboard` redirect to `/login`, and the absence of `X-Powered-By`; do not create production accounts, links, or click events solely for a probe.
