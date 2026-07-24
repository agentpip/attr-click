<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Services\QrRegenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QrRegenerationController extends Controller
{
    public function store(Request $request, Link $link, QrRegenerator $regenerator): RedirectResponse
    {
        abort_unless($link->user_id === $request->user()->id, 403);

        $regenerator->regenerate($link);

        return redirect()->route('links.show', $link)->with('status', 'A fresh QR code is ready to download. It resolves to the same short URL.');
    }
}
