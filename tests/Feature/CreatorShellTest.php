<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatorShellTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_creator_dashboard_uses_flux_navigation_and_actions(): void
    {
        $creator = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($creator)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('data-flux-sidebar', false)
            ->assertSee('data-flux-button', false)
            ->assertSee('Preferred color scheme')
            ->assertSee('Create link')
            ->assertSee('Log out');
    }
}
