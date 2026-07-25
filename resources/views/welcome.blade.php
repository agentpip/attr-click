<x-layout
    title="attr.click — Open-source short links and QR codes"
    description="Create durable short links and branded QR codes with attribution data you own."
    :canonical="url('/')"
    :social-image="asset('images/attr-click-share.png')"
>
    <section class="max-w-3xl py-16">
        <p class="mb-5 text-sm font-semibold uppercase tracking-[0.24em] text-cyan-700 dark:text-cyan-300">Link intelligence, without the bloat</p>
        <h1 class="max-w-2xl text-5xl font-black tracking-tight text-zinc-950 dark:text-zinc-100 sm:text-7xl">Make every scan count.</h1>
        <p class="mt-7 max-w-xl text-lg leading-8 text-zinc-600 dark:text-zinc-300">attr.click turns a destination into a durable short link, a beautifully branded QR code, and attribution you actually own.</p>
        <a href="{{ route('invite.create') }}" class="mt-10 inline-flex rounded-xl bg-cyan-400 px-5 py-3 font-bold text-slate-950 transition hover:bg-cyan-300">Enter invitation code</a>
    </section>

    <footer class="border-t border-zinc-200 py-6 text-xs font-medium text-zinc-500 dark:border-zinc-800 dark:text-zinc-400">
        <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2">
            <p>MIT Licensed <span aria-hidden="true">•</span> Open Source</p>
            <div class="flex items-center gap-4 text-[11px]">
                <a href="{{ route('help') }}" class="hover:text-zinc-900 dark:hover:text-zinc-100">Help</a>
                <p>Built with Agency Agents</p>
            </div>
        </div>
    </footer>
</x-layout>
