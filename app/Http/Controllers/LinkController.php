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

    public function create(): View
    {
        return view('links.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'destination_url' => ['required', 'url:http,https', 'max:2048'],
            'slug' => ['nullable', 'alpha_dash:ascii', 'min:4', 'max:80', Rule::unique('links', 'slug'), 'not_in:dashboard,invite,links,login,logout'],
        ]);

        $parts = parse_url($data['destination_url']);
        abort_unless(is_array($parts) && isset($parts['scheme'], $parts['host']), 422);

        $destinationUrl = strtolower($parts['scheme']).'://'.strtolower($parts['host'])
            .(isset($parts['port']) ? ':'.$parts['port'] : '')
            .($parts['path'] ?? '/');
        $storedQuery = $parts['query'] ?? null;

        $link = $request->user()->links()->create([
            'slug' => $data['slug'] ?: Str::lower(Str::random(7)),
            'destination_url' => $destinationUrl,
            'stored_query' => $storedQuery,
        ]);

        return redirect()->route('links.show', $link);
    }

    public function show(Request $request, Link $link): View
    {
        abort_unless($link->user_id === $request->user()->id, 403);

        return view('links.show', compact('link'));
    }
}
