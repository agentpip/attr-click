# System Patterns

## Vertical slices

Ship one observable workflow at a time: route → validation/action → persistence → authorization → feature test → UI. Do not build disconnected schema, service, and UI layers across separate milestones.

## Redirect contract

Treat the redirect endpoint as the product's most stable public API. Changes must preserve the URL/query merge semantics defined in `docs/ARCHITECTURE.md` and be exercised by an HTTP test.

## Render contract

QR configuration is snapshot-based. A template is an input for a new configuration; changing a template never mutates a published link's QR asset.

## External-security contract

Treat destination admission, public redirects, passwordless links, uploaded logos, and analytics capture as untrusted-input boundaries. Enforce one destination-admission policy for creation and editing; it must reject non-HTTP(S), local/private/reserved network targets, and unsafe hostname resolution before persistence. Redirect forwarding remains lossless, but analytics persistence is allowlisted and bounded rather than a copy of arbitrary query data. Public responses carry deployment-owned browser security headers; release-gate probes verify them from `https://attr.click`, not only in local tests.

## Admin auth coherence

Admin visibility and admin access share the same server-owned ability. If a route is protected by `can:access-admin`, the creator shell should use that exact ability to decide whether to render admin navigation.

## Public repository operations

Versioned deployment files are portable templates, not operator configuration. They contain no production host, account, or absolute application path; operators provide deployment target and application path as local environment variables, and every deployment story validates both before any synchronization starts.
