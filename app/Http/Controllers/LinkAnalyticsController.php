<?php

namespace App\Http\Controllers;

use App\Models\ClickEvent;
use App\Models\Link;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LinkAnalyticsController extends Controller
{
    public function show(Request $request, Link $link): JsonResponse
    {
        abort_unless($link->user_id === $request->user()->id, 403);

        $events = ClickEvent::query()->where('link_id', $link->id)->get(['referrer_host', 'created_at']);

        return response()->json([
            'total_clicks' => $events->count(),
            'last_click_at' => $events->max('created_at')?->toIso8601String(),
            'top_referrers' => $events
                ->filter(fn (ClickEvent $event) => $event->referrer_host !== null)
                ->countBy('referrer_host')
                ->sortDesc()
                ->map(fn (int $clicks, string $host) => ['host' => $host, 'clicks' => $clicks])
                ->values(),
        ]);
    }
}
