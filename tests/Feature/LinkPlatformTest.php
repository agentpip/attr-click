<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Link;
use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinkPlatformTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_invited_email_is_verified_and_authenticated_once(): void
    {
        $invitation = Invitation::factory()->create(['code' => 'WELCOME-ATTR', 'max_uses' => 1]);

        $response = $this->post(route('invite.register'), [
            'email' => 'creator@example.com',
            'code' => 'WELCOME-ATTR',
        ]);

        $response->assertSessionHas('verification_url');
        $verificationUrl = $this->app['session.store']->get('verification_url');
        $this->assertNotNull($verificationUrl);

        $this->get($verificationUrl)
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'creator@example.com']);
        $this->assertDatabaseHas('invitations', ['id' => $invitation->id, 'uses' => 1]);

        $this->get($verificationUrl)->assertStatus(403);
    }

    public function test_an_existing_verified_invitee_can_start_a_session(): void
    {
        Invitation::factory()->create(['code' => 'RETURNING-ATTR']);
        User::factory()->create(['email' => 'returning@example.com', 'email_verified_at' => now()]);

        $this->post(route('invite.register'), ['email' => 'returning@example.com', 'code' => 'RETURNING-ATTR'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_creator_can_create_a_short_link_that_preserves_source_parameters(): void
    {
        $creator = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($creator)->post(route('links.store'), [
            'destination_url' => 'https://example.com/launch?utm_source=poster&utm_campaign=summer&tag=one&tag=two',
            'slug' => 'summer-launch',
        ]);

        $response->assertRedirect(route('links.show', 'summer-launch'));

        $link = Link::query()->where('slug', 'summer-launch')->firstOrFail();

        $this->assertSame('https://example.com/launch', $link->destination_url);
        $this->assertSame('utm_source=poster&utm_campaign=summer&tag=one&tag=two', $link->stored_query);
        $this->assertSame(url('/summer-launch').'?utm_source=poster&utm_campaign=summer&tag=one&tag=two', $link->canonicalUrl());
    }

    public function test_redirect_forwards_stored_parameters_and_allows_incoming_overrides(): void
    {
        $link = Link::factory()->create([
            'slug' => 'summer-launch',
            'destination_url' => 'https://example.com/launch',
            'stored_query' => 'utm_source=poster&utm_campaign=summer&tag=one&tag=two',
        ]);

        $this->get('/summer-launch?utm_source=scan&ref=qr')
            ->assertRedirect('https://example.com/launch?utm_source=scan&utm_campaign=summer&tag=one&tag=two&ref=qr');

        $this->assertDatabaseHas('click_events', ['link_id' => $link->id]);
    }

    public function test_qr_service_creates_decodable_svg_for_canonical_link(): void
    {
        $link = Link::factory()->create([
            'slug' => 'summer-launch',
            'stored_query' => 'utm_source=poster',
        ]);

        $svg = app(QrCodeService::class)->svg($link);

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('viewBox', $svg);
    }
}
