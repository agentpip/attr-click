# Architectural Decisions

## 2026-07-22: Laravel monolith with server-authoritative QR rendering

**Status:** Accepted

**Decision:** Use Laravel/Livewire/Flux for the creator application and Endroid QR Code for generated SVG/PNG output.

**Why:** The product needs reproducible assets, safe logo handling, and a low-ops foundation. Browser-only canvas generation cannot guarantee that downloaded output matches persisted configuration.

**Trade-off:** Advanced artistic QR module styles may need a renderer extension later. v1 prioritizes scannability and stable exports.

## 2026-07-22: Store captured query data separately from destination base URL

**Status:** Accepted

**Decision:** Persist the normalized destination base and ordered original query data, and encode the equivalent canonical query on the short URL.

**Why:** It permits attribution reporting and deterministic redirect composition while preserving the inspectable link/QR payload.

**Trade-off:** Query semantics need explicit tests for duplicates/collisions and careful sensitive-key filtering.
