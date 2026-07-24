<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class MagicLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_user_can_sign_in_with_a_single_use_email_link(): void
    {
        $user = User::factory()->create([
            'email' => 'creator@example.com',
            'email_verified_at' => now(),
        ]);

        $response = $this->from(route('login'))->post(route('login.store'), ['email' => $user->email]);

        $response->assertRedirect(route('login'));
        $response->assertSessionMissing('login_url');

        $token = 'known-login-token';
        $loginLink = $user->loginLinks()->create([
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addMinutes(30),
        ]);
        $loginUrl = URL::temporarySignedRoute('login.verify', $loginLink->expires_at, [
            'loginLink' => $loginLink->id,
            'token' => $token,
        ]);

        $this->get($loginUrl)->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->get($loginUrl)->assertStatus(403);
    }

    public function test_guest_dashboard_request_redirects_to_the_sign_in_form(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_log_out(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_production_does_not_issue_a_magic_link_through_the_log_mailer(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->app->detectEnvironment(fn (): string => 'production');
        config(['mail.default' => 'log']);
        $this->withoutMiddleware(PreventRequestForgery::class);

        $this->post(route('login.store'), ['email' => $user->email])
            ->assertServiceUnavailable();

        $this->assertDatabaseCount('login_links', 0);
    }
}
