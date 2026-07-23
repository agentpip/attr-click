<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Link;
use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
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

        $response->assertSessionMissing('verification_url');

        $user = User::query()->where('email', 'creator@example.com')->sole();
        $nonce = 'known-verification-nonce';
        $user->forceFill(['verification_nonce' => hash('sha256', $nonce)])->save();
        $verificationUrl = URL::temporarySignedRoute('invite.verify', now()->addMinutes(30), [
            'user' => $user->id,
            'invitation' => $invitation->id,
            'nonce' => $nonce,
        ]);

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
        $this->assertSame(url('/summer-launch'), $link->canonicalUrl());
    }

    public function test_creator_cannot_create_a_short_link_to_a_private_network_target(): void
    {
        $creator = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($creator)->post(route('links.store'), [
            'destination_url' => 'http://127.0.0.1/private',
            'slug' => 'private-target',
        ])->assertSessionHasErrors('destination_url');

        $this->assertDatabaseMissing('links', ['slug' => 'private-target']);
    }

    public function test_creator_can_choose_qr_colors_when_creating_a_link(): void
    {
        $creator = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($creator)->post(route('links.store'), [
            'destination_url' => 'https://example.com/launch',
            'slug' => 'brand-launch',
            'qr_foreground_color' => '#112233',
            'qr_background_color' => '#fef3c7',
        ])->assertRedirect(route('links.show', 'brand-launch'));

        $link = Link::query()->where('slug', 'brand-launch')->firstOrFail();
        $this->assertSame('#112233', $link->qr_foreground_color);
        $this->assertSame('#fef3c7', $link->qr_background_color);
        $this->assertStringContainsString('#112233', app(QrCodeService::class)->svg($link));
    }

    public function test_creator_can_save_a_qr_template_and_apply_it_to_a_link(): void
    {
        $creator = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($creator)->post(route('templates.store'), [
            'name' => 'Field posters',
            'foreground_color' => '#0f766e',
            'background_color' => '#ecfeff',
        ])->assertRedirect(route('templates.index'));

        $this->actingAs($creator)->post(route('links.store'), [
            'destination_url' => 'https://example.com/poster',
            'slug' => 'field-poster',
            'qr_template_id' => $creator->qrTemplates()->sole()->id,
            'qr_foreground_color' => '#111827',
            'qr_background_color' => '#ffffff',
        ])->assertRedirect(route('links.show', 'field-poster'));

        $this->assertDatabaseHas('links', [
            'slug' => 'field-poster',
            'qr_foreground_color' => '#0f766e',
            'qr_background_color' => '#ecfeff',
        ]);
    }

    public function test_creator_can_upload_a_private_center_logo_for_a_qr_link(): void
    {
        Storage::fake('local');
        $creator = User::factory()->create(['email_verified_at' => now()]);
        $source = imagecreatetruecolor(128, 128);
        imagefill($source, 0, 0, imagecolorallocate($source, 17, 24, 39));
        ob_start();
        imagepng($source);
        $png = ob_get_clean();
        imagedestroy($source);

        $this->actingAs($creator)->post(route('links.store'), [
            'destination_url' => 'https://example.com/launch',
            'slug' => 'logo-link',
            'qr_foreground_color' => '#111827',
            'qr_background_color' => '#ffffff',
            'qr_logo' => UploadedFile::fake()->createWithContent('mark.png', $png),
        ])->assertRedirect(route('links.show', 'logo-link'));

        $link = Link::query()->where('slug', 'logo-link')->firstOrFail();
        $this->assertStringStartsWith('qr-logos/', $link->qr_logo_path);
        Storage::disk('local')->assertExists($link->qr_logo_path);

        preg_match('/href="data:image\/png;base64,([^"]+)"/', app(QrCodeService::class)->svg($link), $match);
        $renderedLogo = imagecreatefromstring(base64_decode($match[1]));
        $corner = imagecolorsforindex($renderedLogo, imagecolorat($renderedLogo, 0, 0));

        $this->assertSame(['red' => 255, 'green' => 255, 'blue' => 255, 'alpha' => 0], $corner);
        imagedestroy($renderedLogo);
    }

    public function test_creator_can_download_a_png_qr_export(): void
    {
        $creator = User::factory()->create(['email_verified_at' => now()]);
        $link = Link::factory()->for($creator)->create(['slug' => 'downloadable-qr']);

        $this->actingAs($creator)
            ->get(route('links.qr-png', $link))
            ->assertOk()
            ->assertHeader('content-type', 'image/png')
            ->assertSee("\x89PNG", false);
    }

    public function test_creator_can_change_a_qr_link_destination_without_changing_its_canonical_url(): void
    {
        $creator = User::factory()->create(['email_verified_at' => now()]);
        $link = Link::factory()->for($creator)->create([
            'slug' => 'summer-poster',
            'destination_url' => 'https://example.com/original',
            'stored_query' => 'utm_source=poster',
        ]);

        $this->actingAs($creator)->put(route('links.update', $link), [
            'destination_url' => 'https://example.org/new-launch?utm_source=print&utm_campaign=summer',
        ])->assertRedirect(route('links.show', $link));

        $link->refresh();

        $this->assertSame(url('/summer-poster'), $link->canonicalUrl());
        $this->assertSame('https://example.org/new-launch', $link->destination_url);
        $this->assertSame('utm_source=print&utm_campaign=summer', $link->stored_query);
    }

    public function test_creator_cannot_update_a_link_to_a_localhost_destination(): void
    {
        $creator = User::factory()->create(['email_verified_at' => now()]);
        $link = Link::factory()->for($creator)->create(['slug' => 'public-link']);

        $this->actingAs($creator)->put(route('links.update', $link), [
            'destination_url' => 'https://localhost/admin',
        ])->assertSessionHasErrors('destination_url');

        $this->assertNotSame('https://localhost/admin', $link->fresh()->destination_url);
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

    public function test_qr_payload_stays_canonical_while_destination_utm_parameters_are_tracked(): void
    {
        $link = Link::factory()->create([
            'slug' => 'summer-poster',
            'destination_url' => 'https://example.com/launch',
            'stored_query' => 'utm_source=poster&utm_medium=print&utm_campaign=summer',
        ]);

        $this->assertSame(url('/summer-poster'), $link->canonicalUrl());

        $this->get('/summer-poster')
            ->assertRedirect('https://example.com/launch?utm_source=poster&utm_medium=print&utm_campaign=summer');

        $this->assertSame([
            'utm_source' => 'poster',
            'utm_medium' => 'print',
            'utm_campaign' => 'summer',
        ], $link->clickEvents()->sole()->attribution);
    }

    public function test_redirect_persists_only_bounded_standard_utm_attribution(): void
    {
        $link = Link::factory()->create(['slug' => 'privacy-safe-link']);

        $this->get('/privacy-safe-link?utm_source=poster&email=creator%40example.com&token=do-not-store&utm_campaign='.str_repeat('x', 121))
            ->assertRedirect();

        $this->assertSame(['utm_source' => 'poster'], $link->clickEvents()->sole()->attribution);
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
