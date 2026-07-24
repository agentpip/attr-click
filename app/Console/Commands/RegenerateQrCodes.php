<?php

namespace App\Console\Commands;

use App\Models\Link;
use App\Services\QrRegenerator;
use Illuminate\Console\Command;

class RegenerateQrCodes extends Command
{
    protected $signature = 'links:regenerate-qr';

    protected $description = 'Reissue every QR code from its current canonical short URL';

    public function handle(QrRegenerator $regenerator): int
    {
        $count = 0;

        Link::query()->orderBy('id')->eachById(function (Link $link) use ($regenerator, &$count): void {
            $regenerator->regenerate($link);
            $count++;
        });

        $this->line("Reissued {$count} QR codes.");

        return self::SUCCESS;
    }
}
