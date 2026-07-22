# attr.click Agent Guide

**Version:** 2.1-compatible project workflow

## Core rules

1. **Reuse before creation.** Before adding a file, search the app and tests for an extension point; record why extension is insufficient in the task plan.
2. **Sandbox first.** Work on a feature branch, never `main`.
3. **TDD.** Add a focused failing feature/unit test, observe the expected failure, implement the smallest behavior, then run the focused and full suites.
4. **No fake production data or stubbed completion.** Test fixtures are allowed; shipped behavior must use real data paths.
5. **Cite code by `path:line` and decisions by `memory-bank/file.md#heading` in plans and PRs.
6. **Approval gate.** For substantial scopes: PLAN → BUILD → DIFF → QA → APPROVAL → APPLY → DOCS. The user explicitly requested the initial foundation; subsequent feature slices still need a reviewable diff and passing QA before merge.

## Required discovery

For a feature or architecture change, first load:

- `memory-bank/projectbrief.md`
- `memory-bank/systemPatterns.md`
- `memory-bank/techContext.md`
- `memory-bank/activeContext.md`
- `memory-bank/projectRules.md`
- related records in `memory-bank/decisions.md`

Check `CONTRIBUTING.md` before any pull request. Search existing issues/PRs before opening a new one.

## Testing commands

```bash
php artisan test
./vendor/bin/pint --test
npm run build
```

For any public redirect or authentication change, add an HTTP feature test that exercises the real route, middleware, database persistence, and redirect response.

## Security invariants

- Treat destination URLs, query parameters, invite codes, uploaded logos, and headers as untrusted input.
- Preserve user consent and privacy: do not add outbound telemetry or third-party tracking.
- Store query parameters as first-party attribution data; never put secrets or raw IP addresses into user-facing reports.
- Validate redirect destinations; block unsafe schemes and private/internal address targets where applicable.
- Keep QR logo uploads on private storage and transform them server-side.

## Memory bank

Update `activeContext.md` at state transitions. Add ADRs for material architecture decisions and update `progress.md` when a release milestone is complete. Create task records after a reviewed feature is accepted.
