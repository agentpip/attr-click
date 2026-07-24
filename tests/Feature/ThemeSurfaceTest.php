<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeSurfaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_creator_workspaces_have_light_and_dark_surface_styles(): void
    {
        $creator = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($creator)
            ->get(route('links.create'))
            ->assertOk()
            ->assertSee('dark:bg-zinc-900/70', false)
            ->assertSee('focus:ring-2 dark:border-zinc-700 dark:bg-zinc-950', false)
            ->assertSee('flex items-end justify-between gap-6', false)
            ->assertSee('text-4xl font-black', false);

        $this->actingAs($creator)
            ->get(route('templates.index'))
            ->assertOk()
            ->assertSee('dark:bg-zinc-900/70', false);
        $link = Link::factory()->for($creator)->create();

        $this->actingAs($creator)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('dark:bg-zinc-900/50', false);

        $this->actingAs($creator)
            ->get(route('links.show', $link))
            ->assertOk()
            ->assertSee('dark:bg-zinc-900/50', false);
    }
}
