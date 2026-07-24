<?php

namespace App\Services;

use App\Models\Link;

class QrRegenerator
{
    public function __construct(private QrCodeService $qrCode) {}

    public function regenerate(Link $link): void
    {
        $this->qrCode->svg($link);

        $link->forceFill(['qr_regenerated_at' => now()])->save();
    }
}
