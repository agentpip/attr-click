# Technical Context

- PHP 8.5 / Laravel 13
- Livewire 4 + Flux 2 + Tailwind via Vite
- Endroid QR Code 6 for SVG/PNG output
- SQLite for local development; MySQL 8-compatible production deployment on UMBP
- Local invitation and magic-link verification can be exercised end-to-end with Mailpit (`127.0.0.1:8025` UI/API, SMTP on `127.0.0.1:1025`).
- Laravel feature tests/PHPUnit and Pint
- Chart.js is bundled with Vite for owner-only link analytics.
- Production is served by Caddy at `https://attr.click`; repository-local Envoy builds Vite assets locally, syncs assets before source, migrates, optimizes, restarts the user service, and probes `/up`.
- Production has secure, HTTP-only, `SameSite=Lax` session cookies and a working real SMTP transport for client magic-link delivery. Keep its credentials outside version control.

Flux requires license activation outside version control. See `README.md#flux-ui`.
