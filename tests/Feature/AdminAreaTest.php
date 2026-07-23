<?php

namespace Tests\Feature;

use App\Models\ClickEvent;
use App\Models\Invitation;
use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAreaTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_the_admin_dashboard(): void
    {
        $creator = User::factory()->create(['is_admin' => false]);

        $this->actingAs($creator)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_can_view_global_creator_link_and_scan_stats(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $creator = User::factory()->unverified()->create();
        $activeLink = Link::factory()->for($creator)->create();
        Link::factory()->for($creator)->create(['is_active' => false]);
        ClickEvent::query()->create(['link_id' => $activeLink->id]);
        ClickEvent::query()->create(['link_id' => $activeLink->id]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk()->assertSee('Global activity');
        $response->assertViewHas('stats', fn (array $stats): bool => $stats === [
            'users_total' => 2,
            'verified_users' => 1,
            'links_total' => 2,
            'active_links' => 1,
            'scans_total' => 2,
        ]);
    }

    public function test_admin_can_poll_global_stats_without_exposing_them_to_creators(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $creator = User::factory()->create(['is_admin' => false]);

        $this->actingAs($admin)
            ->getJson('/admin/stats')
            ->assertOk()
            ->assertExactJson([
                'users_total' => 2,
                'verified_users' => 2,
                'links_total' => 0,
                'active_links' => 0,
                'scans_total' => 0,
            ]);

        $this->actingAs($creator)->getJson('/admin/stats')->assertForbidden();
    }

    public function test_admin_navigation_is_visible_only_to_admins(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $creator = User::factory()->create(['is_admin' => false]);

        $this->actingAs($admin)->get(route('dashboard'))->assertSee('Admin');
        $this->actingAs($creator)->get(route('dashboard'))->assertDontSee('Admin');
    }

    public function test_admin_can_manage_other_user_admin_roles_but_not_remove_their_own_access(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $creator = User::factory()->create(['is_admin' => false]);

        $this->actingAs($admin)
            ->patch("/admin/users/{$creator->id}/role", ['is_admin' => true])
            ->assertRedirect('/admin/users');

        $this->assertTrue($creator->fresh()->is_admin);

        $this->actingAs($admin)
            ->patch("/admin/users/{$admin->id}/role", ['is_admin' => false])
            ->assertForbidden();

        $this->assertTrue($admin->fresh()->is_admin);
    }

    public function test_admin_can_issue_and_revoke_invitation_codes(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->post('/admin/invitations', [
                'code' => 'ADMIN-ACCESS-2026',
                'max_uses' => 2,
                'expires_at' => now()->addWeek()->toDateTimeString(),
            ])
            ->assertRedirect('/admin/invitations');

        $invitation = Invitation::query()->sole();
        $this->assertSame(Invitation::hashCode('ADMIN-ACCESS-2026'), $invitation->code_hash);
        $this->assertSame(2, $invitation->max_uses);

        $this->actingAs($admin)
            ->patch("/admin/invitations/{$invitation->id}/revoke")
            ->assertRedirect('/admin/invitations');

        $this->assertNotNull($invitation->fresh()->revoked_at);
    }
}
