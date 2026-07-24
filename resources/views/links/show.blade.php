<x-layout>
    <section class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_320px]">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-700 dark:text-cyan-300">Live link</p>
            <h1 class="mt-2 break-all text-3xl font-black leading-tight">{{ $link->canonicalUrl() }}</h1>
            <p class="mt-5 break-all text-zinc-600 dark:text-zinc-400">Redirects to {{ $link->destination_url }}{{ $link->stored_query ? '?'.$link->stored_query : '' }}</p>
        </div>
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900/50">
            <img class="aspect-square w-full" alt="QR code for {{ $link->canonicalUrl() }}" src="data:image/svg+xml;base64,{{ base64_encode(app(\App\Services\QrCodeService::class)->svg($link)) }}">
            <flux:button class="mt-4 w-full" :href="route('links.qr-png', $link)" variant="primary" icon="arrow-down-tray">Download PNG</flux:button>
            <form method="POST" action="{{ route('links.qr-regenerate', $link) }}" class="mt-3">
                @csrf
                <flux:button class="w-full" type="submit" variant="ghost" icon="arrow-path">Regenerate QR</flux:button>
            </form>
            <p class="mt-3 text-xs leading-5 text-zinc-500 dark:text-zinc-400">Creates a fresh QR download for this same short URL. Existing scans keep working.</p>
        </div>
    </section>

    <section class="mt-12 grid gap-6 lg:grid-cols-[1fr_280px]">
        <form method="POST" action="{{ route('links.update', $link) }}" class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900/50">
            @csrf
            @method('PUT')

            <h2 class="text-xl font-black">Update destination</h2>
            <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-400">Change where scans go without changing the public short URL or QR payload.</p>

            <label class="mt-6 block text-sm font-semibold">Destination URL
                <input name="destination_url" type="url" required value="{{ old('destination_url', $link->destination_url.($link->stored_query ? '?'.$link->stored_query : '')) }}" class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-3 py-3 text-zinc-950 outline-none ring-cyan-500 focus:ring-2 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                @error('destination_url') <span class="mt-1 block text-sm text-rose-600 dark:text-rose-300">{{ $message }}</span> @enderror
            </label>

            <flux:button class="mt-5" type="submit" variant="primary">Save destination</flux:button>
        </form>

        <aside class="rounded-2xl border border-cyan-300/40 bg-cyan-50 p-6 dark:border-cyan-400/20 dark:bg-cyan-400/5">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-700 dark:text-cyan-300">QR promise</p>
            <p class="mt-3 text-sm leading-6 text-zinc-700 dark:text-zinc-300">Every regenerated QR resolves to this stable short URL. Destination parameters stay at the destination, not in the QR payload.</p>
        </aside>
    </section>

    <section class="mt-12 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900/50" data-link-analytics data-endpoint="{{ route('links.analytics', $link) }}">
        <div class="flex items-baseline justify-between gap-4">
            <div><p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-700 dark:text-cyan-300">First-party analytics</p><h2 class="mt-2 text-2xl font-bold">What this link is doing</h2></div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">No pixels. No fingerprinting.</p>
        </div>
        <div class="mt-6 grid gap-6 md:grid-cols-[160px_1fr]">
            <div><p class="text-4xl font-black" data-total-clicks>—</p><p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">total scans</p></div>
            <div class="min-h-48"><canvas data-referrer-chart aria-label="Top referrer hosts" role="img"></canvas></div>
        </div>
    </section>
</x-layout>
