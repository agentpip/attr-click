<x-layout>
    <div class="flex items-end justify-between gap-6">
        <div><p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">Your links</p><h1 class="mt-2 text-4xl font-black">Small URLs. Big signal.</h1><a href="{{ route('templates.index') }}" class="mt-3 inline-block text-sm font-semibold text-cyan-300 hover:text-cyan-200">Manage QR templates</a></div>
        <a href="{{ route('links.create') }}" class="rounded-xl bg-cyan-400 px-4 py-3 font-bold text-slate-950">Create link</a>
    </div>
    <div class="mt-10 overflow-hidden rounded-2xl border border-slate-800">
        @forelse ($links as $link)
            <a href="{{ route('links.show', $link) }}" class="block border-b border-slate-800 bg-slate-900/50 p-5 last:border-0 hover:bg-slate-900">
                <div class="font-bold text-cyan-300">{{ $link->canonicalUrl() }}</div>
                <div class="mt-1 truncate text-sm text-slate-400">{{ $link->destination_url }}{{ $link->stored_query ? '?'.$link->stored_query : '' }}</div>
            </a>
        @empty
            <div class="p-10 text-center text-slate-400">No links yet. Make your first QR-ready destination.</div>
        @endforelse
    </div>
</x-layout>
