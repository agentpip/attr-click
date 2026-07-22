<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LinkController extends Controller
{
    public function index(Request $request): View
    {
        return view('links.index', ['links' => $request->user()->links()->latest()->get()]);
    }

    public function create(Request $request): View
    {
        return view('links.create', ['templates' => $request->user()->qrTemplates()->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'destination_url' => ['required', 'url:http,https', 'max:2048'],
            'slug' => ['nullable', 'alpha_dash:ascii', 'min:4', 'max:80', Rule::unique('links', 'slug'), 'not_in:dashboard,invite,links,login,logout'],
            'qr_foreground_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'qr_background_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'qr_template_id' => ['nullable', 'integer'],
        ]);

        $parts = parse_url($data['destination_url']);
        abort_unless(is_array($parts) && isset($parts['scheme'], $parts['host']), 422);

        $destinationUrl = strtolower($parts['scheme']).'://'.strtolower($parts['host'])
            .(isset($parts['port']) ? ':'.$parts['port'] : '')
            .($parts['path'] ?? '/');
        $storedQuery = $parts['query'] ?? null;
        $template = isset($data['qr_template_id'])
            ? $request->user()->qrTemplates()->findOrFail($data['qr_template_id'])
            : null;

        $link = $request->user()->links()->create([
            'slug' => $data['slug'] ?: Str::lower(Str::random(7)),
            'destination_url' => $destinationUrl,
            'stored_query' => $storedQuery,
            'qr_template_id' => $template?->id,
            'qr_foreground_color' => $template?->foreground_color ?? strtolower($data['qr_foreground_color'] ?? '#111827'),
            'qr_background_color' => $template?->background_color ?? strtolower($data['qr_background_color'] ?? '#ffffff'),
        ]);

        return redirect()->route('links.show', $link);
    }

    public function show(Request $request, Link $link): View
    {
        abort_unless($link->user_id === $request->user()->id, 403);

        return view('links.show', compact('link'));
    }
}
