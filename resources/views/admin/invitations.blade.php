<x-layout>
    <div class="flex flex-wrap items-end justify-between gap-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">Administration</p>
            <h1 class="mt-2 text-4xl font-black">Invitation codes</h1>
            <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-400">Issue bounded access and revoke codes that should no longer work.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-cyan-300 hover:text-cyan-200">Global activity</a>
    </div>

    <div class="mt-10 grid gap-8 lg:grid-cols-[minmax(0,1fr)_360px]">
        <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900/50">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-zinc-200 text-zinc-600 dark:border-zinc-800 dark:text-zinc-400"><tr><th class="px-5 py-3 font-medium">Issued</th><th class="px-5 py-3 font-medium">Use</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3"></th></tr></thead>
                <tbody>
                    @forelse ($invitations as $invitation)
                        <tr class="border-b border-zinc-200 last:border-0 dark:border-zinc-800">
                            <td class="px-5 py-4">{{ $invitation->created_at->format('M j, Y') }}</td>
                            <td class="px-5 py-4">{{ number_format($invitation->uses) }} / {{ $invitation->max_uses ? number_format($invitation->max_uses) : 'Unlimited' }}</td>
                            <td class="px-5 py-4">{{ $invitation->revoked_at ? 'Revoked' : ($invitation->canBeUsed() ? 'Active' : 'Unavailable') }}</td>
                            <td class="px-5 py-4 text-right">
                                @if (! $invitation->revoked_at)
                                    <form method="POST" action="{{ route('admin.invitations.revoke', $invitation) }}">@csrf @method('PATCH')<button class="font-semibold text-rose-600 hover:text-rose-500 dark:text-rose-400">Revoke</button></form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-10 text-center text-zinc-600 dark:text-zinc-400">No invitation codes issued yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <form method="POST" action="{{ route('admin.invitations.store') }}" class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900/50">@csrf
            <h2 class="text-xl font-black">Issue a code</h2>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Codes are stored as hashes and cannot be revealed later.</p>
            <label class="mt-6 block text-sm font-semibold">Code<input name="code" required maxlength="80" value="{{ old('code') }}" class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-3 py-3 dark:border-zinc-700 dark:bg-zinc-950"></label>
            <label class="mt-4 block text-sm font-semibold">Maximum uses <span class="font-normal text-zinc-600 dark:text-zinc-400">(optional)</span><input name="max_uses" type="number" min="1" value="{{ old('max_uses') }}" class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-3 py-3 dark:border-zinc-700 dark:bg-zinc-950"></label>
            <label class="mt-4 block text-sm font-semibold">Expires at <span class="font-normal text-zinc-600 dark:text-zinc-400">(optional)</span><input name="expires_at" type="datetime-local" value="{{ old('expires_at') }}" class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-3 py-3 dark:border-zinc-700 dark:bg-zinc-950"></label>
            <button class="mt-6 w-full rounded-xl bg-cyan-400 px-5 py-3 font-bold text-slate-950 hover:bg-cyan-300">Issue invitation</button>
        </form>
    </div>

    <div class="mt-6">{{ $invitations->links() }}</div>
</x-layout>
