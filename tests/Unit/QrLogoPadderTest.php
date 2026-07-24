<?php

namespace Tests\Unit;

use App\Services\QrLogoPadder;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QrLogoPadderTest extends TestCase
{
    public function test_it_adds_a_background_matched_quiet_zone_around_a_center_logo(): void
    {
        Storage::fake('local');

        $source = imagecreatetruecolor(64, 64);
        imagefill($source, 0, 0, imagecolorallocate($source, 17, 24, 39));
        ob_start();
        imagepng($source);
        $png = ob_get_clean();
        imagedestroy($source);
        Storage::disk('local')->put('qr-logos/source.png', $png);

        $path = app(QrLogoPadder::class)->paddedPath('qr-logos/source.png', '#ffffff');
        $logo = imagecreatefrompng(Storage::disk('local')->path($path));

        $corner = imagecolorsforindex($logo, imagecolorat($logo, 0, 0));
        $center = imagecolorsforindex($logo, imagecolorat($logo, imagesx($logo) / 2, imagesy($logo) / 2));

        $this->assertGreaterThan(64, imagesx($logo));
        $this->assertSame(['red' => 255, 'green' => 255, 'blue' => 255, 'alpha' => 0], $corner);
        $this->assertSame(['red' => 17, 'green' => 24, 'blue' => 39, 'alpha' => 0], $center);

        imagedestroy($logo);
    }
}
