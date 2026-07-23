<?php

namespace App\Models;

use Database\Factories\LinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'qr_template_id', 'slug', 'destination_url', 'stored_query', 'qr_foreground_color', 'qr_background_color', 'qr_logo_path', 'qr_regenerated_at', 'is_active'])]
class Link extends Model
{
    /** @use HasFactory<LinkFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['qr_regenerated_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function clickEvents(): HasMany
    {
        return $this->hasMany(ClickEvent::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function canonicalUrl(): string
    {
        return url('/'.$this->slug);
    }
}
