<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    public function test_public_response_sets_browser_security_headers(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertHeader('content-security-policy', "default-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'; object-src 'none'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self' data:; connect-src 'self'")
            ->assertHeader('permissions-policy', 'camera=(), geolocation=(), microphone=()')
            ->assertHeader('referrer-policy', 'strict-origin-when-cross-origin')
            ->assertHeader('x-content-type-options', 'nosniff')
            ->assertHeader('x-frame-options', 'DENY')
            ->assertHeader('cross-origin-opener-policy', 'same-origin')
            ->assertHeader('cross-origin-resource-policy', 'same-origin');
    }

    public function test_secure_public_response_sets_hsts_and_hides_framework_version(): void
    {
        $this->call('GET', '/', [], [], [], [
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'REMOTE_ADDR' => '127.0.0.1',
        ])
            ->assertOk()
            ->assertHeader('strict-transport-security', 'max-age=31536000; includeSubDomains')
            ->assertHeaderMissing('x-powered-by');
    }
}
