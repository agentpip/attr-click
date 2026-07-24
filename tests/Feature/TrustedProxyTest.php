<?php

namespace Tests\Feature;

use Tests\TestCase;

class TrustedProxyTest extends TestCase
{
    public function test_local_caddy_proxy_preserves_https_asset_urls(): void
    {
        config(['app.url' => 'https://attr.click']);

        $this->call('GET', '/', [], [], [], [
            'HTTP_HOST' => 'attr.click',
            'HTTP_X_FORWARDED_HOST' => 'attr.click',
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'REMOTE_ADDR' => '127.0.0.1',
        ])
            ->assertOk()
            ->assertSee('https://attr.click/build/assets/', false);
    }
}
