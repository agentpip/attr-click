<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class QrTemplateController extends Controller
{
    public function index(Request $request): View
    {
        return view('templates.index', ['templates' => $request->user()->qrTemplates()->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('qr_templates', 'name')->where('user_id', $request->user()->id)],
            'foreground_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'background_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $request->user()->qrTemplates()->create([
            ...$data,
            'foreground_color' => strtolower($data['foreground_color']),
            'background_color' => strtolower($data['background_color']),
        ]);

        return redirect()->route('templates.index');
    }
}
