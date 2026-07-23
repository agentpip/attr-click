<?php

namespace App\Services;

use App\Models\ClickEvent;
use App\Models\Link;
use App\Models\User;

class AdminStatsService
{
    /** @return array<string, int> */
    public function snapshot(): array
    {
        return [
            'users_total' => User::query()->count(),
            'verified_users' => User::query()->whereNotNull('email_verified_at')->count(),
            'links_total' => Link::query()->count(),
            'active_links' => Link::query()->where('is_active', true)->count(),
            'scans_total' => ClickEvent::query()->count(),
        ];
    }
}
