<x-layout>
    <div class="flex items-end justify-between gap-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">Your links</p>
            <h1 class="mt-2 text-4xl font-black">Small URLs. Big signal.</h1>
            <a href="{{ route('templates.index') }}" class="mt-3 inline-block text-sm font-semibold text-cyan-300 hover:text-cyan-200">Manage QR templates</a>
        </div>
        <a href="{{ route('links.create') }}" class="rounded-xl bg-cyan-400 px-4 py-3 font-bold text-slate-950">Create link</a>
    </div>

    <section class="mt-10 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900/50" data-utm-report>
        <div class="flex flex-wrap items-baseline justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">Campaign performance</p>
                <h2 class="mt-2 text-2xl font-bold">What is driving scans</h2>
            </div>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">Standard UTM tags, aggregated across your links.</p>
        </div>

        @if ($utmReport['tagged_clicks'] === 0)
            <p class="mt-6 text-sm leading-6 text-zinc-600 dark:text-zinc-400">No tagged scans yet. Add standard UTM parameters to the URLs you share, then return here to compare sources, campaigns, and channels.</p>
        @else
            <div class="mt-6 flex items-baseline gap-3">
                <p class="text-4xl font-black">{{ number_format($utmReport['tagged_clicks']) }}</p>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">tagged scans across {{ number_format($utmReport['tagged_links']) }} {{ \Illuminate\Support\Str::plural('link', $utmReport['tagged_links']) }}</p>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($utmReport['dimensions'] as $key => $dimension)
                    @continue ($dimension['values'] === [])

                    <article class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-800">
                        <div class="flex items-baseline justify-between gap-3">
                            <h3 class="font-bold">{{ $dimension['label'] }}</h3>
                            <code class="text-xs text-zinc-500 dark:text-zinc-400">{{ $key }}</code>
                        </div>
                        <ol class="mt-4 space-y-3">
                            @foreach ($dimension['values'] as $value)
                                <li class="flex items-start justify-between gap-4">
                                    <span class="min-w-0 break-words text-sm font-medium">{{ $value['value'] }}</span>
                                    <span class="shrink-0 text-right text-xs text-zinc-600 dark:text-zinc-400">{{ number_format($value['clicks']) }} scans<br>{{ number_format($value['links']) }} {{ \Illuminate\Support\Str::plural('link', $value['links']) }}</span>
                                </li>
                            @endforeach
                        </ol>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <div class="mt-10 overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900/50">
        @forelse ($links as $link)
            <a href="{{ route('links.show', $link) }}" class="block border-b border-zinc-200 bg-white p-5 last:border-0 hover:bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900/50 dark:hover:bg-zinc-900">
                <div class="font-bold text-cyan-300">{{ $link->canonicalUrl() }}</div>
                <div class="mt-1 truncate text-sm text-zinc-600 dark:text-zinc-400">{{ $link->destination_url }}{{ $link->stored_query ? '?'.$link->stored_query : '' }}</div>
            </a>
        @empty
            <div class="p-10 text-center text-zinc-600 dark:text-zinc-400">No links yet. Make your first QR-ready destination.</div>
        @endforelse
    </div>
</x-layout>
