<x-layout>
    <div class="flex items-end justify-between gap-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-700 dark:text-cyan-300">Create link</p>
            <h1 class="mt-2 text-4xl font-black">From destination to signal.</h1>
        </div>
        <a href="{{ route('templates.index') }}" class="rounded-xl border border-zinc-300 px-4 py-3 text-sm font-bold hover:border-cyan-400 dark:border-zinc-700 dark:hover:border-cyan-300">QR templates</a>
    </div>

    <section class="mt-10 max-w-3xl rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900/70">
        <h2 class="text-xl font-black">Link details</h2>
        <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-400">Paste a destination and turn it into a branded, attribution-ready short link and QR code.</p>

        <form method="POST" action="{{ route('links.store') }}" enctype="multipart/form-data" class="mt-8 space-y-5">
            @csrf

            <label class="block text-sm font-semibold">Destination URL
                <input name="destination_url" type="url" required placeholder="https://example.com/launch?utm_source=poster" class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-3 py-3 text-zinc-950 outline-none ring-cyan-500 focus:ring-2 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                @error('destination_url') <span class="mt-1 block text-sm text-rose-300">{{ $message }}</span> @enderror
            </label>

            <label class="block text-sm font-semibold">Custom slug <span class="font-normal text-zinc-500 dark:text-zinc-400">(optional)</span>
                <input name="slug" type="text" placeholder="summer-launch" class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-3 py-3 text-zinc-950 outline-none ring-cyan-500 focus:ring-2 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                @error('slug') <span class="mt-1 block text-sm text-rose-300">{{ $message }}</span> @enderror
            </label>

            @if ($templates->isNotEmpty())
                <label class="block text-sm font-semibold">Saved QR template
                    <select name="qr_template_id" class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-3 py-3 text-zinc-950 outline-none ring-cyan-500 focus:ring-2 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                        <option value="">Use palette below</option>
                        @foreach ($templates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                        @endforeach
                    </select>
                </label>
            @endif

            <fieldset>
                <legend class="text-sm font-semibold">QR palette</legend>
                <div class="mt-2 grid grid-cols-2 gap-4">
                    <label class="text-sm text-zinc-600 dark:text-zinc-400">Foreground <input name="qr_foreground_color" type="color" value="#111827" class="mt-2 block h-12 w-full rounded-lg border border-zinc-300 bg-white p-1 dark:border-zinc-700 dark:bg-zinc-950"></label>
                    <label class="text-sm text-zinc-600 dark:text-zinc-400">Background <input name="qr_background_color" type="color" value="#ffffff" class="mt-2 block h-12 w-full rounded-lg border border-zinc-300 bg-white p-1 dark:border-zinc-700 dark:bg-zinc-950"></label>
                </div>
            </fieldset>

            <flux:field>
                <flux:label>Center logo <span class="font-normal text-zinc-400">(optional)</span></flux:label>
                <flux:input name="qr_logo" type="file" accept="image/png,image/jpeg,image/webp" />
                <flux:description>PNG, JPEG, or WebP. Stored privately; rendered into the exported QR only.</flux:description>
                <flux:error name="qr_logo" />
            </flux:field>

            <button class="rounded-xl bg-cyan-400 px-5 py-3 font-bold text-slate-950 hover:bg-cyan-300">Create QR-ready link</button>
        </form>
    </section>
</x-layout>
