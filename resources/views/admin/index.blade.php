<x-layout>
    <div class="flex flex-wrap items-end justify-between gap-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">Administration</p>
            <h1 class="mt-2 text-4xl font-black">Global activity</h1>
            <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-400">A first-party view across every creator, link, and scan.</p>
        </div>
    </div>

    <section class="mt-10 grid gap-4 sm:grid-cols-2 xl:grid-cols-5" data-admin-stats data-endpoint="{{ route('admin.stats') }}">
        @foreach ([
            'users_total' => 'Creators',
            'verified_users' => 'Verified creators',
            'links_total' => 'Links',
            'active_links' => 'Active links',
            'scans_total' => 'Scans',
        ] as $key => $label)
            <article class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900/50">
                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ $label }}</p>
                <p class="mt-3 text-3xl font-black" data-stat="{{ $key }}">{{ number_format($stats[$key]) }}</p>
            </article>
        @endforeach
    </section>

    <div class="mt-10 flex flex-wrap gap-3">
        <a href="{{ route('admin.users.index') }}" class="rounded-xl border border-zinc-300 px-4 py-3 font-semibold hover:border-cyan-400 dark:border-zinc-700">Manage creators</a>
        <a href="{{ route('admin.invitations.index') }}" class="rounded-xl border border-zinc-300 px-4 py-3 font-semibold hover:border-cyan-400 dark:border-zinc-700">Manage invitation codes</a>
    </div>
</x-layout>
