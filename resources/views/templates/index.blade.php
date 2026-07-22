<x-layout>
    <div class="flex items-end justify-between gap-6">
        <div><p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">QR styles</p><h1 class="mt-2 text-4xl font-black">Reusable visual language.</h1></div>
        <a href="{{ route('links.create') }}" class="rounded-xl border border-slate-700 px-4 py-3 text-sm font-bold hover:border-cyan-300">New link</a>
    </div>
    <div class="mt-10 grid gap-8 lg:grid-cols-[1fr_360px]">
        <div class="grid gap-4 sm:grid-cols-2">@forelse ($templates as $template)
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5"><div class="h-16 rounded-xl" style="background: {{ $template->background_color }}; border: 12px solid {{ $template->foreground_color }}"></div><h2 class="mt-4 font-bold">{{ $template->name }}</h2><p class="mt-1 font-mono text-xs text-slate-400">{{ $template->foreground_color }} / {{ $template->background_color }}</p></div>
        @empty <p class="text-slate-400">No saved templates yet.</p>@endforelse</div>
        <form method="POST" action="{{ route('templates.store') }}" class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 space-y-5">@csrf
            <h2 class="text-xl font-black">Save a template</h2>
            <label class="block text-sm font-semibold">Name <input name="name" required class="mt-2 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-3 text-white outline-none ring-cyan-400 focus:ring-2"></label>
            <div class="grid grid-cols-2 gap-4"><label class="text-sm text-slate-400">Foreground <input name="foreground_color" type="color" value="#111827" class="mt-2 block h-12 w-full rounded-lg border border-slate-700 bg-slate-950 p-1"></label><label class="text-sm text-slate-400">Background <input name="background_color" type="color" value="#ffffff" class="mt-2 block h-12 w-full rounded-lg border border-slate-700 bg-slate-950 p-1"></label></div>
            <button class="w-full rounded-xl bg-cyan-400 px-5 py-3 font-bold text-slate-950 hover:bg-cyan-300">Save template</button>
        </form>
    </div>
</x-layout>
