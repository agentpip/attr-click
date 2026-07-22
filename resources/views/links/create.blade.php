<x-layout>
    <section class="mx-auto max-w-2xl rounded-2xl border border-slate-800 bg-slate-900/70 p-8">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">New link</p>
        <h1 class="mt-2 text-4xl font-black">Paste the destination.</h1>
        <p class="mt-3 text-slate-400">Campaign parameters are preserved in the canonical short URL and QR payload.</p>
        <form method="POST" action="{{ route('links.store') }}" class="mt-8 space-y-5">@csrf
            <label class="block text-sm font-semibold">Destination URL
                <input name="destination_url" type="url" required placeholder="https://example.com/launch?utm_source=poster" class="mt-2 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-3 text-white outline-none ring-cyan-400 focus:ring-2">
                @error('destination_url') <span class="mt-1 block text-sm text-rose-300">{{ $message }}</span> @enderror
            </label>
            <label class="block text-sm font-semibold">Custom slug <span class="font-normal text-slate-500">(optional)</span>
                <input name="slug" type="text" placeholder="summer-launch" class="mt-2 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-3 text-white outline-none ring-cyan-400 focus:ring-2">
                @error('slug') <span class="mt-1 block text-sm text-rose-300">{{ $message }}</span> @enderror
            </label>
            <button class="rounded-xl bg-cyan-400 px-5 py-3 font-bold text-slate-950 hover:bg-cyan-300">Create QR-ready link</button>
        </form>
    </section>
</x-layout>
