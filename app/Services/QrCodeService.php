<?php

namespace App\Services;

use App\Models\Link;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Writer\WriterInterface;
use Illuminate\Support\Facades\Storage;

class QrCodeService
{
    public function __construct(private QrLogoPadder $logoPadder) {}

    public function svg(Link $link): string
    {
        return $this->builder($link, new SvgWriter)->build()->getString();
    }

    public function png(Link $link): string
    {
        return $this->builder($link, new PngWriter)->build()->getString();
    }

    private function builder(Link $link, WriterInterface $writer): Builder
    {
        $logoPath = $this->logoPath($link);

        return new Builder(
            writer: $writer,
            data: $link->canonicalUrl(),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 1024,
            margin: 24,
            foregroundColor: $this->color($link->qr_foreground_color),
            backgroundColor: $this->color($link->qr_background_color, '#ffffff'),
            logoPath: $logoPath ?? '',
            logoResizeToWidth: $logoPath ? 120 : null,
        );
    }

    private function logoPath(Link $link): ?string
    {
        if (! $link->qr_logo_path || ! Storage::disk('local')->exists($link->qr_logo_path)) {
            return null;
        }

        return Storage::disk('local')->path(
            $this->logoPadder->paddedPath($link->qr_logo_path, $link->qr_background_color ?? '#ffffff'),
        );
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
