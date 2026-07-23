<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicThemeTest extends TestCase
{
    public function test_public_entry_pages_adapt_their_surfaces_for_light_and_dark_mode(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('dark:text-zinc-100', false);

        $this->get(route('invite.create'))
            ->assertOk()
            ->assertSee('dark:bg-zinc-900/70', false);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Preferred color scheme')
            ->assertSee('x-cloak', false);
    }
}
