<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class QrLogoPadder
{
    public function paddedPath(string $sourcePath, string $backgroundColor): string
    {
        $disk = Storage::disk('local');
        $sourceFile = $disk->path($sourcePath);
        $imageInfo = getimagesize($sourceFile);

        if ($imageInfo === false) {
            throw new InvalidArgumentException('The QR logo must be a valid image.');
        }

        $source = match ($imageInfo[2]) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($sourceFile),
            IMAGETYPE_PNG => imagecreatefrompng($sourceFile),
            IMAGETYPE_WEBP => imagecreatefromwebp($sourceFile),
            default => throw new InvalidArgumentException('The QR logo image type is not supported.'),
        };

        $padding = max(16, (int) ceil(max(imagesx($source), imagesy($source)) * 0.2));
        $canvas = imagecreatetruecolor(imagesx($source) + (2 * $padding), imagesy($source) + (2 * $padding));
        [$red, $green, $blue] = $this->rgb($backgroundColor);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, $red, $green, $blue));
        imagecopy($canvas, $source, $padding, $padding, 0, 0, imagesx($source), imagesy($source));

        $path = 'qr-logo-renders/'.hash('sha256', $disk->get($sourcePath).$backgroundColor).'.png';
        $output = $disk->path($path);
        $directory = dirname($output);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        imagepng($canvas, $output);
        imagedestroy($source);
        imagedestroy($canvas);

        return $path;
    }

    /** @return array{int, int, int} */
    private function rgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }
}
