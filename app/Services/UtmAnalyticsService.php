<?php

namespace App\Services;

use App\Models\ClickEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UtmAnalyticsService
{
    private const DIMENSIONS = [
        'utm_source' => 'Source',
        'utm_medium' => 'Medium',
        'utm_campaign' => 'Campaign',
        'utm_content' => 'Content',
        'utm_term' => 'Term',
    ];

    /**
     * @return array{
     *     tagged_clicks: int,
     *     tagged_links: int,
     *     dimensions: array<string, array{label: string, values: array<int, array{value: string, clicks: int, links: int}>}>
     * }
     */
    public function for(User $user): array
    {
        $counts = [];
        $links = [];
        $taggedLinks = [];
        $taggedClicks = 0;

        foreach (self::DIMENSIONS as $key => $label) {
            $counts[$key] = [];
            $links[$key] = [];
        }

        $events = ClickEvent::query()
            ->select(['id', 'link_id', 'attribution'])
            ->whereHas('link', fn (Builder $query) => $query->where('user_id', $user->id))
            ->cursor();

        foreach ($events as $event) {
            $tagged = false;

            foreach (self::DIMENSIONS as $key => $label) {
                $value = $event->attribution[$key] ?? null;

                if (! is_string($value) || $value === '' || strlen($value) > 120) {
                    continue;
                }

                $tagged = true;
                $counts[$key][$value] = ($counts[$key][$value] ?? 0) + 1;
                $links[$key][$value][$event->link_id] = true;
            }

            if ($tagged) {
                $taggedClicks++;
                $taggedLinks[$event->link_id] = true;
            }
        }

        $dimensions = [];
        foreach (self::DIMENSIONS as $key => $label) {
            $values = collect($counts[$key])
                ->map(fn (int $clicks, string $value) => [
                    'value' => $value,
                    'clicks' => $clicks,
                    'links' => count($links[$key][$value]),
                ])
                ->sort(fn (array $left, array $right) => $right['clicks'] <=> $left['clicks'] ?: strcasecmp($left['value'], $right['value']))
                ->take(5)
                ->values()
                ->all();

            $dimensions[$key] = ['label' => $label, 'values' => $values];
        }

        return [
            'tagged_clicks' => $taggedClicks,
            'tagged_links' => count($taggedLinks),
            'dimensions' => $dimensions,
        ];
    }
}
