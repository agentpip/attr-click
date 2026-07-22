# System Patterns

## Vertical slices

Ship one observable workflow at a time: route → validation/action → persistence → authorization → feature test → UI. Do not build disconnected schema, service, and UI layers across separate milestones.

## Redirect contract

Treat the redirect endpoint as the product's most stable public API. Changes must preserve the URL/query merge semantics defined in `docs/ARCHITECTURE.md` and be exercised by an HTTP test.

## Render contract

QR configuration is snapshot-based. A template is an input for a new configuration; changing a template never mutates a published link's QR asset.
