@servers([
    'local' => '127.0.0.1',
    'production' => 'michael@umacbookpro',
])

@setup
    $repository = getcwd();
    $app = '/home/michael/Sites/attr.click';
    $host = 'michael@umacbookpro';
@endsetup

@task('build', ['on' => 'local'])
    set -eu
    cd '{{ $repository }}'
    npm ci --ignore-scripts
    npm run build
@endtask

@task('sync_assets', ['on' => 'local'])
    set -eu
    rsync -az --delete --itemize-changes '{{ $repository }}/public/build/' '{{ $host }}:{{ $app }}/public/build/'
@endtask

@task('sync_application', ['on' => 'local'])
    set -eu
    rsync -az --delete --itemize-changes \
        --exclude='.env' \
        --exclude='.env.*' \
        --exclude='.git/' \
        --exclude='.hermes/' \
        --exclude='auth.json' \
        --exclude='node_modules/' \
        --exclude='public/build/' \
        --exclude='storage/' \
        --exclude='vendor/' \
        --exclude='database/*.sqlite' \
        '{{ $repository }}/' '{{ $host }}:{{ $app }}/'
    ssh '{{ $host }}' "chmod -R go+rX '{{ $app }}/app' '{{ $app }}/config' '{{ $app }}/public' '{{ $app }}/resources' '{{ $app }}/routes'; chmod go+r '{{ $app }}/bootstrap/'*.php"
@endtask

@task('activate', ['on' => 'production'])
    set -eu
    cd '{{ $app }}'
    rm -f bootstrap/cache/*.php
    composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
    php artisan migrate --force
    php artisan optimize
@endtask

@task('regenerate_qr_codes', ['on' => 'production'])
    set -eu
    cd '{{ $app }}'
    php artisan links:regenerate-qr
@endtask

@task('health', ['on' => 'production'])
    set -eu
    for attempt in 1 2 3 4 5; do
        status="$(curl -fsS -o /dev/null -w '%{http_code}' -H 'Host: attr.click' http://127.0.0.1:8092/up || true)"
        if [ "$status" = '200' ]; then
            exit 0
        fi

        sleep 2
    done

    echo "attr.click health check failed" >&2
    exit 1
@endtask

@story('deploy')
    build
    sync_assets
    sync_application
    activate
    health
@endstory

@story('deploy_qr_lifecycle')
    build
    sync_assets
    sync_application
    activate
    regenerate_qr_codes
    health
@endstory
