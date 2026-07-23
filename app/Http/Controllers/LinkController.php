<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Services\DestinationUrlPolicy;
use App\Services\UtmAnalyticsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LinkController extends Controller
{
    public function index(Request $request, UtmAnalyticsService $utmAnalytics): View
    {
        $user = $request->user();

        return view('links.index', [
            'links' => $user->links()->latest()->get(),
            'utmReport' => $utmAnalytics->for($user),
        ]);
    }

    public function create(Request $request): View
    {
        return view('links.create', ['templates' => $request->user()->qrTemplates()->orderBy('name')->get()]);
    }

    public function store(Request $request, DestinationUrlPolicy $destinationUrls): RedirectResponse
    {
        $data = $request->validate([
            'destination_url' => $this->destinationRules($destinationUrls),
            'slug' => ['nullable', 'alpha_dash:ascii', 'min:4', 'max:80', Rule::unique('links', 'slug'), 'not_in:dashboard,invite,links,login,logout'],
            'qr_foreground_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'qr_background_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'qr_template_id' => ['nullable', 'integer'],
            'qr_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048', 'dimensions:min_width=64,min_height=64,max_width=2048,max_height=2048'],
        ]);

        [$destinationUrl, $storedQuery] = $destinationUrls->normalize($data['destination_url']);
        $template = isset($data['qr_template_id'])
            ? $request->user()->qrTemplates()->findOrFail($data['qr_template_id'])
            : null;

        $logoPath = $request->file('qr_logo')?->store('qr-logos', 'local');

        $link = $request->user()->links()->create([
            'slug' => $data['slug'] ?: Str::lower(Str::random(7)),
            'destination_url' => $destinationUrl,
            'stored_query' => $storedQuery,
            'qr_template_id' => $template?->id,
            'qr_foreground_color' => $template?->foreground_color ?? strtolower($data['qr_foreground_color'] ?? '#111827'),
            'qr_background_color' => $template?->background_color ?? strtolower($data['qr_background_color'] ?? '#ffffff'),
            'qr_logo_path' => $logoPath,
        ]);

        return redirect()->route('links.show', $link);
    }

    public function show(Request $request, Link $link): View
    {
        abort_unless($link->user_id === $request->user()->id, 403);

        return view('links.show', compact('link'));
    }

    public function update(Request $request, Link $link, DestinationUrlPolicy $destinationUrls): RedirectResponse
    {
        abort_unless($link->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'destination_url' => $this->destinationRules($destinationUrls),
        ]);

        [$destinationUrl, $storedQuery] = $destinationUrls->normalize($data['destination_url']);

        $link->update([
            'destination_url' => $destinationUrl,
            'stored_query' => $storedQuery,
        ]);

        return redirect()->route('links.show', $link)->with('status', 'Destination updated. Your short URL and QR payload are unchanged.');
    }

    /** @return array<int, mixed> */
    private function destinationRules(DestinationUrlPolicy $destinationUrls): array
    {
        return [
            'required',
            'string',
            'max:2048',
            function (string $attribute, mixed $value, \Closure $fail) use ($destinationUrls): void {
                if (! is_string($value) || ! $destinationUrls->allows($value)) {
                    $fail('The destination URL must use a publicly routable HTTP or HTTPS host.');
                }
            },
        ];
    }
}
