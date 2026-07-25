# Deployment configuration

The repository's `Envoy.blade.php` is safe to publish: it contains deployment steps but no production hostname, account name, or application path. Operators provide those local values at run time.

Before running an Envoy story, export the target explicitly:

```sh
export ATTR_CLICK_DEPLOY_HOST='deploy-user@your-production-host'
export ATTR_CLICK_DEPLOY_PATH='/absolute/path/to/application'
./vendor/bin/envoy run deploy
```

For a QR lifecycle deployment:

```sh
./vendor/bin/envoy run deploy_qr_lifecycle
```

`deploy` builds assets locally, synchronizes only application and build artifacts, keeps `.env`, local agent state, storage, dependencies, and local databases out of sync, then runs Composer, migrations, Laravel optimization, and a loopback health check on the remote host.

## Safety boundary

- Keep credentials in your local shell, SSH agent, or secret manager—never in `Envoy.blade.php` or this repository.
- Review `Envoy.blade.php` before use: `rsync --delete` makes the local checkout the intended source of truth for synchronized application files.
- Deploy only a checked-out, up-to-date `main` branch after local QA and public/loopback preflight checks.
- The Envoy preflight exits before syncing if either required deployment variable is absent.
