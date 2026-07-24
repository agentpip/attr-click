<?php

namespace App\Http\Controllers;

use App\Models\ClickEvent;
use App\Models\Link;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    private const ATTRIBUTION_KEYS = [
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
    ];

    private const MAX_ATTRIBUTION_VALUE_LENGTH = 120;

    public function __invoke(Request $request, Link $link): RedirectResponse
    {
        abort_unless($link->is_active, 410);

        $query = $this->mergeQuery($link->stored_query, $request->getQueryString());

        ClickEvent::query()->create([
            'link_id' => $link->id,
            'referrer_host' => parse_url((string) $request->headers->get('referer'), PHP_URL_HOST),
            'attribution' => $this->safeAttribution($query),
        ]);

        return redirect()->away($link->destination_url.($query ? '?'.$query : ''), 302);
    }

    private function mergeQuery(?string $stored, ?string $incoming): string
    {
        $storedPairs = $this->pairs($stored);
        $incomingPairs = $this->pairs($incoming);
        $incomingByKey = [];

        foreach ($incomingPairs as $pair) {
            $incomingByKey[$pair[0]][] = $pair;
        }

        $effective = [];
        foreach ($storedPairs as $pair) {
            if (isset($incomingByKey[$pair[0]])) {
                $effective = [...$effective, ...$incomingByKey[$pair[0]]];
                unset($incomingByKey[$pair[0]]);

                continue;
            }

            $effective[] = $pair;
        }

        foreach ($incomingByKey as $pairs) {
            $effective = [...$effective, ...$pairs];
        }

        return implode('&', array_map(fn (array $pair) => $pair[2], $effective));
    }

    /** @return array<int, array{0: string, 1: string, 2: string}> */
    private function pairs(?string $query): array
    {
        if ($query === null || $query === '') {
            return [];
        }

        return array_map(function (string $part): array {
            [$key, $value] = array_pad(explode('=', $part, 2), 2, '');

            return [rawurldecode($key), rawurldecode($value), $part];
        }, explode('&', $query));
    }

    private function safeAttribution(?string $query): array
    {
        return collect($this->pairs($query))
            ->filter(fn (array $pair) => in_array($pair[0], self::ATTRIBUTION_KEYS, true))
            ->filter(fn (array $pair) => mb_strlen($pair[1]) <= self::MAX_ATTRIBUTION_VALUE_LENGTH)
            ->mapWithKeys(fn (array $pair) => [$pair[0] => $pair[1]])
            ->all();
    }
}
