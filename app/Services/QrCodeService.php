<?php

namespace App\Services;

use App\Models\Link;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\SvgWriter;

class QrCodeService
{
    public function svg(Link $link): string
    {
        return (new Builder(
            writer: new SvgWriter,
            data: $link->canonicalUrl(),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 1024,
            margin: 24,
            foregroundColor: $this->color($link->qr_foreground_color),
            backgroundColor: $this->color($link->qr_background_color, '#ffffff'),
        ))->build()->getString();
    }

    private function color(?string $hex, string $fallback = '#111827'): Color
    {
        $hex ??= $fallback;

        return new Color(
            hexdec(substr($hex, 1, 2)),
            hexdec(substr($hex, 3, 2)),
            hexdec(substr($hex, 5, 2)),
        );
    }
}
