<x-layout>
    <div class="flex flex-wrap items-end justify-between gap-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">Administration</p>
            <h1 class="mt-2 text-4xl font-black">Creators</h1>
            <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-400">Review account verification, link ownership, and admin access.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-cyan-300 hover:text-cyan-200">Global activity</a>
    </div>

    <div class="mt-10 overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900/50">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-zinc-200 text-zinc-600 dark:border-zinc-800 dark:text-zinc-400"><tr><th class="px-5 py-3 font-medium">Creator</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Links</th><th class="px-5 py-3 font-medium text-right">Access</th></tr></thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="border-b border-zinc-200 last:border-0 dark:border-zinc-800">
                        <td class="px-5 py-4"><p class="font-semibold">{{ $user->name }}</p><p class="text-zinc-600 dark:text-zinc-400">{{ $user->email }}</p></td>
                        <td class="px-5 py-4">{{ $user->email_verified_at ? 'Verified' : 'Pending verification' }}</td>
                        <td class="px-5 py-4">{{ number_format($user->links_count) }}</td>
                        <td class="px-5 py-4 text-right">
                            @if (auth()->id() === $user->id)
                                <span class="text-zinc-600 dark:text-zinc-400">Your account</span>
                            @else
                                <form method="POST" action="{{ route('admin.users.role', $user) }}">@csrf @method('PATCH')
                                    <input type="hidden" name="is_admin" value="{{ $user->is_admin ? 0 : 1 }}">
                                    <button class="rounded-lg border border-zinc-300 px-3 py-2 font-semibold hover:border-cyan-400 dark:border-zinc-700">{{ $user->is_admin ? 'Remove admin' : 'Make admin' }}</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
</x-layout>
