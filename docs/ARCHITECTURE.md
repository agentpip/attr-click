# attr.click Architecture

## Shape

Laravel is the application and public redirect service. Livewire + Flux provides the authenticated creator experience. Endroid QR Code produces server-authoritative SVG/PNG output, so preview/download behavior does not depend on a browser-only renderer.

```text
Creator browser → Livewire/Flux → Laravel actions → SQLite (local) / MySQL (production)
                                         ├── private logo storage
                                         ├── queued mail / aggregation jobs
Scanner → GET /{slug}?params → redirect resolver → click event → 302 destination?merged-params
```

## Core domain model

| Entity | Responsibility |
|---|---|
| `users` | passwordless verified creator identity and admin flag. |
| `invitations` | hashed invite code, expiry, use limit, creator/revocation metadata. |
| `login_links` | hashed, expiring, single-use email-authentication intent. |
| `links` | owner, slug, original URL, normalized destination base, stored ordered query data, state. |
| `qr_configurations` | immutable/snapshotted rendering settings for a link. |
| `qr_templates` | owner-scoped reusable configuration payload. |
| `click_events` | minimized first-party redirect event, with privacy-filtered attribution data. |

## URL/query contract

1. Parse only absolute `http(s)` destinations.
2. Normalize host casing and default ports without rewriting path/query semantics.
3. Store original query data as an ordered list rather than a map, preserving repeated keys.
4. Canonical short URL uses the stored query data.
5. On redirect, merge stored parameters with request parameters. Incoming values override stored values for the same key; repeated incoming values are retained in incoming order.
6. Filter sensitive parameter keys from analytics persistence, not redirect forwarding. Redirect correctness and analytics privacy are separate concerns.

This makes the printed QR URL inspectable and keeps campaign attribution stable even if someone copies only the short URL.

## Security boundaries

- Public redirects are read-only; they never trust user ownership input.
- Creator routes use authentication plus policy authorization on every resource.
- Magic links and invite validation are rate limited and responses are non-enumerating.
- Redirect targets are created by authenticated owners, but still receive scheme, host, and DNS/private-network defenses.
- Uploaded logos are treated as hostile input, transcoded to safe raster formats, and never served from an executable public path.

## Storage and deployment

SQLite is the supported local developer database. Production uses MySQL 8-compatible schema/indexes, object storage for logos/assets, a queue worker for email/aggregation, and Laravel scheduler for retention/rollups. Set `APP_URL=https://attr.click` before generating public QR assets.
