<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['link_id', 'referrer_host', 'attribution'])]
class ClickEvent extends Model
{
    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return ['attribution' => 'array'];
    }

    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class);
    }
}
