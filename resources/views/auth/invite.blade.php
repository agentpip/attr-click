<x-layout>
    <section class="mx-auto max-w-md rounded-2xl border border-slate-800 bg-slate-900/70 p-8 shadow-2xl">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">Invitation only</p>
        <h1 class="mt-3 text-3xl font-black">Start with your invite.</h1>
        <p class="mt-3 text-sm leading-6 text-slate-400">Enter the email that should own your links. We will send one secure verification link.</p>
        <form method="POST" action="{{ route('invite.register') }}" class="mt-8 space-y-5">
            @csrf
            <label class="block text-sm font-semibold">Email
                <input name="email" value="{{ old('email') }}" type="email" required autofocus class="mt-2 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-3 text-white outline-none ring-cyan-400 focus:ring-2">
                @error('email') <span class="mt-1 block text-sm text-rose-300">{{ $message }}</span> @enderror
            </label>
            <label class="block text-sm font-semibold">Invitation code
                <input name="code" type="text" required class="mt-2 w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-3 font-mono uppercase text-white outline-none ring-cyan-400 focus:ring-2">
                @error('code') <span class="mt-1 block text-sm text-rose-300">{{ $message }}</span> @enderror
            </label>
            <button class="w-full rounded-xl bg-cyan-400 px-5 py-3 font-bold text-slate-950 hover:bg-cyan-300">Send verification link</button>
        </form>
    </section>
</x-layout>
