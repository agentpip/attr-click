<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicThemeTest extends TestCase
{
    public function test_help_is_a_publicly_available_user_guide(): void
    {
        $this->get(route('help'))
            ->assertOk()
            ->assertSee('How attr.click works')
            ->assertSee('Create a short link')
            ->assertSee('QR codes and templates')
            ->assertSee('Privacy by default')
            ->assertSee('Sign in');
    }

    public function test_help_links_to_the_canonical_public_repository(): void
    {
        $this->get(route('help'))
            ->assertOk()
            ->assertSee('https://github.com/msitarzewski/attr-click', false)
            ->assertDontSee('https://github.com/agentpip/attr-click', false);
    }

    public function test_public_open_source_surfaces_link_to_the_canonical_repository_and_license(): void
    {
        foreach ([route('home'), route('help')] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('https://github.com/msitarzewski/attr-click', false)
                ->assertSee('https://github.com/msitarzewski/attr-click/blob/main/LICENSE', false);
        }
    }

    public function test_homepage_exposes_complete_social_share_metadata_and_open_source_provenance(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<title>attr.click — Open-source short links and QR codes</title>', false)
            ->assertSee('<meta name="description" content="Create durable short links and branded QR codes with attribution data you own.">', false)
            ->assertSee('<link rel="canonical" href="'.url('/').'">', false)
            ->assertSee('<meta property="og:type" content="website">', false)
            ->assertSee('<meta property="og:url" content="'.url('/').'">', false)
            ->assertSee('<meta property="og:title" content="attr.click — Open-source short links and QR codes">', false)
            ->assertSee('<meta property="og:image" content="'.asset('images/attr-click-share.png').'">', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
            ->assertSee('MIT License')
            ->assertSee('Open source')
            ->assertSee('Built with Agency Agents');
    }

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
