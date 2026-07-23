<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class QrExportController extends Controller
{
    public function png(Request $request, Link $link, QrCodeService $qrCode): Response
    {
        abort_unless($link->user_id === $request->user()->id, 403);

        return response($qrCode->png($link), 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="'.$link->slug.'.png"',
        ]);
    }
}
