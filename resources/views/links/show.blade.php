<x-layout>
    <section class="grid gap-10 lg:grid-cols-[1fr_320px]">
        <div><p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">Live link</p><h1 class="mt-2 break-all text-4xl font-black">{{ $link->canonicalUrl() }}</h1><p class="mt-5 break-all text-slate-400">Redirects to {{ $link->destination_url }}{{ $link->stored_query ? '?'.$link->stored_query : '' }}</p></div>
        <div class="rounded-2xl bg-white p-5"><img class="aspect-square w-full" alt="QR code for {{ $link->canonicalUrl() }}" src="data:image/svg+xml;base64,{{ base64_encode(app(\App\Services\QrCodeService::class)->svg($link)) }}"></div>
    </section>
</x-layout>
