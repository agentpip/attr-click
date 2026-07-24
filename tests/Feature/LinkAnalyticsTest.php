<?php

namespace Tests\Feature;

use App\Models\ClickEvent;
use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinkAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_link_owner_can_view_first_party_click_totals_and_safe_referrers(): void
    {
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $link = Link::factory()->for($owner)->create();
        ClickEvent::query()->create(['link_id' => $link->id, 'referrer_host' => 'instagram.com', 'attribution' => ['utm_source' => 'poster']]);
        ClickEvent::query()->create(['link_id' => $link->id, 'referrer_host' => 'instagram.com', 'attribution' => ['utm_source' => 'poster', 'token' => 'never-show']]);

        $this->actingAs($owner)
            ->getJson(route('links.analytics', $link))
            ->assertOk()
            ->assertJsonPath('total_clicks', 2)
            ->assertJsonPath('top_referrers.0.host', 'instagram.com')
            ->assertJsonPath('top_referrers.0.clicks', 2)
            ->assertJsonMissing(['token' => 'never-show']);
    }

    public function test_link_detail_exposes_an_owner_authorized_chart_mount(): void
    {
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $link = Link::factory()->for($owner)->create(['slug' => 'analytics-link']);

        $this->actingAs($owner)
            ->get(route('links.show', $link))
            ->assertOk()
            ->assertSee('data-link-analytics', false)
            ->assertSee(route('links.analytics', $link), false)
            ->assertSee(route('links.qr-png', $link), false);
    }

    public function test_dashboard_aggregates_standard_utm_parameters_across_owned_links(): void
    {
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $firstLink = Link::factory()->for($owner)->create();
        $secondLink = Link::factory()->for($owner)->create();
        $otherLink = Link::factory()->for(User::factory()->create(['email_verified_at' => now()]))->create();

        ClickEvent::query()->create(['link_id' => $firstLink->id, 'attribution' => ['utm_source' => 'poster', 'utm_medium' => 'print', 'utm_campaign' => 'summer']]);
        ClickEvent::query()->create(['link_id' => $secondLink->id, 'attribution' => ['utm_source' => 'poster', 'utm_campaign' => 'summer']]);
        ClickEvent::query()->create(['link_id' => $secondLink->id, 'attribution' => ['utm_source' => 'newsletter', 'token' => 'never-show']]);
        ClickEvent::query()->create(['link_id' => $firstLink->id, 'attribution' => ['ref' => 'unreported']]);
        ClickEvent::query()->create(['link_id' => $otherLink->id, 'attribution' => ['utm_source' => 'other-owner']]);

        $response = $this->actingAs($owner)->get(route('dashboard'));

        $response->assertOk()->assertSee('Campaign performance');

        $report = $response->viewData('utmReport');

        $this->assertSame(3, $report['tagged_clicks']);
        $this->assertSame([
            ['value' => 'poster', 'clicks' => 2, 'links' => 2],
            ['value' => 'newsletter', 'clicks' => 1, 'links' => 1],
        ], $report['dimensions']['utm_source']['values']);
        $this->assertSame([
            ['value' => 'summer', 'clicks' => 2, 'links' => 2],
        ], $report['dimensions']['utm_campaign']['values']);
        $this->assertArrayNotHasKey('token', $report['dimensions']);
    }

    public function test_dashboard_reports_destination_utm_data_captured_by_a_canonical_redirect(): void
    {
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $link = Link::factory()->for($owner)->create([
            'slug' => 'campaign-poster',
            'stored_query' => 'utm_source=poster&utm_medium=print&utm_campaign=summer',
        ]);

        $this->get('/campaign-poster')
            ->assertRedirect();

        $report = $this->actingAs($owner)->get(route('dashboard'))->viewData('utmReport');

        $this->assertSame(1, $report['tagged_clicks']);
        $this->assertSame([
            ['value' => 'poster', 'clicks' => 1, 'links' => 1],
        ], $report['dimensions']['utm_source']['values']);
    }
}
