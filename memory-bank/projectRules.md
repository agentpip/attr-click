# Project Rules

- Laravel conventions first; introduce an action/service when behavior has more than one caller or needs independent testing.
- New behavior begins with a focused failing test.
- Public URL, redirect, QR, and analytics contracts require feature/integration tests, not unit tests alone.
- Use Flux components where activated; preserve accessible labels, errors, focus, and keyboard navigation.
- Never report sensitive query values or raw IPs to creators.
