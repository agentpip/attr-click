# Architectural Decisions

## 2026-07-22: Laravel monolith with server-authoritative QR rendering

**Status:** Accepted

**Decision:** Use Laravel/Livewire/Flux for the creator application and Endroid QR Code for generated SVG/PNG output.

**Why:** The product needs reproducible assets, safe logo handling, and a low-ops foundation. Browser-only canvas generation cannot guarantee that downloaded output matches persisted configuration.

**Trade-off:** Advanced artistic QR module styles may need a renderer extension later. v1 prioritizes scannability and stable exports.

## 2026-07-22: Store captured query data separately from destination base URL

**Status:** Accepted

**Decision:** Persist the normalized destination base and ordered original query data. QR codes encode only the stable, query-free short URL; redirects deterministically apply the stored query, allow incoming keys to override it, and record the resulting safe attribution data.

**Why:** A QR code remains durable and shareable without baking mutable campaign parameters into the payload, while the destination and first-party dashboard still receive the same effective UTM values.

**Trade-off:** Query semantics need explicit tests for duplicates/collisions, incoming overrides, and careful sensitive-key filtering.

## 2026-07-22: QR reissue is a stable-link lifecycle event

**Status:** Accepted

**Decision:** Destination editing and QR reissue never rotate a link slug or alter its canonical payload. Reissue renders a fresh canonical QR and records `qr_regenerated_at`; the `links:regenerate-qr` command applies that lifecycle event to every link.

**Why:** Owners can correct destinations and replace QR downloads without invalidating scans from printed or shared QR codes. The reissue timestamp creates an auditable management event despite dynamic SVG/PNG rendering.

**Trade-off:** A QR image already printed or saved outside attr.click cannot be changed remotely. It must be replaced with a newly downloaded QR asset.

## 2026-07-23: Security remediation gates public-v1 promotion

**Status:** Accepted

**Decision:** Keep attr.click in deployed invite-only beta until the external-surface review findings are fixed and independently verified in production. The required gates are: centralized destination admission that blocks internal/private/reserved targets; no plaintext passwordless credentials in session flash/logs; bounded, allowlisted analytics persistence; a live browser-security-header canary; and a passing unauthenticated dashboard/auth-route canary.

**Why:** The app's public redirect is durable infrastructure. The existing production behavior and source review diverged from the documented privacy/security contract, so a feature-complete QR lifecycle slice is not equivalent to a public-v1-safe release.

**Trade-off:** This delays wider invitations and source promotion, but preserves the core ownership/privacy promise and avoids baking weak redirect/auth/data-retention behavior into public use.

## 2026-07-23: Admin authorization uses one shared Laravel ability

**Status:** Accepted

**Decision:** Protect the admin slice with a single Laravel Gate ability, `access-admin`, backed by `users.is_admin`. Use that same ability for server-side route middleware and creator-shell navigation visibility. Admin operations remain narrow: global aggregate stats, creator role management, and invitation issuance/revocation.

**Why:** One shared ability prevents route protection and UI visibility from drifting apart, keeps the implementation native to Laravel, and avoids introducing a heavier RBAC system before there is evidence of more complex policy needs.

**Trade-off:** `is_admin` is intentionally coarse. If attr.click later needs delegated scopes, team workspaces, or per-resource administration, the single-flag model will need to evolve into richer policies or roles.
