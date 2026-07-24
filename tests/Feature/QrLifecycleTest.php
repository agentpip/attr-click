<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QrLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_creator_can_reissue_a_qr_without_changing_its_canonical_url(): void
    {
        $creator = User::factory()->create(['email_verified_at' => now()]);
        $link = Link::factory()->for($creator)->create(['slug' => 'summer-poster']);

        $this->actingAs($creator)
            ->post(route('links.qr-regenerate', $link))
            ->assertRedirect(route('links.show', $link))
            ->assertSessionHas('status');

        $link->refresh();

        $this->assertNotNull($link->qr_regenerated_at);
        $this->assertSame(url('/summer-poster'), $link->canonicalUrl());
    }

    public function test_creator_can_permanently_delete_a_short_link_and_its_qr(): void
    {
        Storage::fake('local');
        $creator = User::factory()->create(['email_verified_at' => now()]);
        $link = Link::factory()->for($creator)->create([
            'slug' => 'expired-poster',
            'qr_logo_path' => 'qr-logos/expired-poster.png',
        ]);
        Storage::disk('local')->put($link->qr_logo_path, 'private logo');
        $click = $link->clickEvents()->create(['referrer_host' => 'example.com']);

        $this->actingAs($creator)
            ->delete(route('links.destroy', $link), ['confirm_slug' => $link->slug])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('links', ['id' => $link->id]);
        $this->assertDatabaseMissing('click_events', ['id' => $click->id]);
        Storage::disk('local')->assertMissing($link->qr_logo_path);
        $this->get('/expired-poster')->assertNotFound();
    }

    public function test_regeneration_command_reissues_every_qr_code(): void
    {
        $first = Link::factory()->create(['slug' => 'first-code']);
        $second = Link::factory()->create(['slug' => 'second-code']);

        $this->artisan('links:regenerate-qr')
            ->expectsOutput('Reissued 2 QR codes.')
            ->assertExitCode(0);

        $this->assertNotNull($first->refresh()->qr_regenerated_at);
        $this->assertNotNull($second->refresh()->qr_regenerated_at);
    }

    public function test_link_detail_offers_destination_editing_and_qr_reissue(): void
    {
        $creator = User::factory()->create(['email_verified_at' => now()]);
        $link = Link::factory()->for($creator)->create(['slug' => 'summer-poster']);

        $this->actingAs($creator)->get(route('links.show', $link))
            ->assertOk()
            ->assertSee('Update destination')
            ->assertSee(route('links.qr-regenerate', $link), false);
    }

    public function test_link_detail_requires_an_explicit_confirmation_before_deletion(): void
    {
        $creator = User::factory()->create(['email_verified_at' => now()]);
        $link = Link::factory()->for($creator)->create(['slug' => 'expired-poster']);

        $this->actingAs($creator)->get(route('links.show', $link))
            ->assertOk()
            ->assertSee('Delete this short link')
            ->assertSee('Type expired-poster to confirm')
            ->assertSee('Existing QR scans will no longer resolve.')
            ->assertSee(route('links.destroy', $link), false);
    }

    public function test_non_owner_cannot_delete_a_short_link_or_its_qr(): void
    {
        Storage::fake('local');
        $link = Link::factory()->create([
            'qr_logo_path' => 'qr-logos/owned-by-someone-else.png',
        ]);
        Storage::disk('local')->put($link->qr_logo_path, 'private logo');
        $otherCreator = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($otherCreator)
            ->delete(route('links.destroy', $link), ['confirm_slug' => $link->slug])
            ->assertForbidden();

        $this->assertDatabaseHas('links', ['id' => $link->id]);
        Storage::disk('local')->assertExists($link->qr_logo_path);
    }

    public function test_creator_must_type_the_exact_slug_before_deleting_a_short_link(): void
    {
        $creator = User::factory()->create(['email_verified_at' => now()]);
        $link = Link::factory()->for($creator)->create(['slug' => 'protected-poster']);

        $this->actingAs($creator)
            ->from(route('links.show', $link))
            ->delete(route('links.destroy', $link), ['confirm_slug' => 'wrong-slug'])
            ->assertRedirect(route('links.show', $link))
            ->assertSessionHasErrors('confirm_slug');

        $this->assertDatabaseHas('links', ['id' => $link->id]);
    }

    public function test_non_owner_cannot_change_a_qr_link_destination(): void
    {
        $link = Link::factory()->create(['destination_url' => 'https://example.com/original']);
        $otherCreator = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($otherCreator)->put(route('links.update', $link), [
            'destination_url' => 'https://example.org/changed',
        ])->assertForbidden();

        $this->assertSame('https://example.com/original', $link->refresh()->destination_url);
    }

    public function test_non_owner_cannot_reissue_a_qr_code(): void
    {
        $link = Link::factory()->create();
        $otherCreator = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($otherCreator)
            ->post(route('links.qr-regenerate', $link))
            ->assertForbidden();

        $this->assertNull($link->refresh()->qr_regenerated_at);
    }
}
