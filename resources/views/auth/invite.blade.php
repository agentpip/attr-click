<x-layout>
    <section class="mx-auto max-w-md rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900/70 dark:shadow-2xl">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">Invitation only</p>
        <h1 class="mt-3 text-3xl font-black">Start with your invite.</h1>
        <p class="mt-3 text-sm leading-6 text-zinc-600 dark:text-zinc-400">Enter the email that should own your links. We will send one secure verification link.</p>
        <form method="POST" action="{{ route('invite.register') }}" class="mt-8 space-y-5">
            @csrf
            <label class="block text-sm font-semibold">Email
                <input name="email" value="{{ old('email') }}" type="email" required autofocus class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-3 py-3 text-zinc-950 outline-none ring-cyan-500 focus:ring-2 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                @error('email') <span class="mt-1 block text-sm text-rose-300">{{ $message }}</span> @enderror
            </label>
            <label class="block text-sm font-semibold">Invitation code
                <input name="code" type="text" required class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-3 py-3 font-mono uppercase text-zinc-950 outline-none ring-cyan-500 focus:ring-2 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                @error('code') <span class="mt-1 block text-sm text-rose-300">{{ $message }}</span> @enderror
            </label>
            <button class="w-full rounded-xl bg-cyan-400 px-5 py-3 font-bold text-slate-950 hover:bg-cyan-300">Send verification link</button>
        </form>
    </section>
</x-layout>
