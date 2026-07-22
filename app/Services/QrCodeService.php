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
            foregroundColor: new Color(17, 24, 39),
            backgroundColor: new Color(255, 255, 255),
        ))->build()->getString();
    }
}
