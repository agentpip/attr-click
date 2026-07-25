<x-layout
    title="attr.click help — short links, QR codes, and first-party attribution"
    description="A practical guide to invitations, short links, QR codes, templates, and privacy at attr.click."
    :canonical="route('help')"
    :social-image="asset('images/attr-click-share.png')"
>
    <article class="mx-auto max-w-3xl pb-16">
        <header class="border-b border-zinc-200 pb-12 dark:border-zinc-800">
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-cyan-700 dark:text-cyan-300">Public user guide</p>
            <h1 class="mt-4 text-4xl font-black tracking-tight text-zinc-950 dark:text-zinc-100 sm:text-5xl">How attr.click works</h1>
            <p class="mt-5 max-w-2xl text-lg leading-8 text-zinc-600 dark:text-zinc-300">Create durable short links, produce QR codes that stay useful after print, and keep the attribution you need without adding a third-party tracking stack.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <flux:button :href="route('invite.create')" variant="primary">Use an invitation</flux:button>
                <flux:button :href="route('login')" variant="outline">Sign in</flux:button>
            </div>
        </header>

        <div class="mt-12 space-y-12 text-zinc-700 dark:text-zinc-300">
            <section id="access" class="scroll-mt-8">
                <h2 class="text-2xl font-bold text-zinc-950 dark:text-zinc-100">Get access</h2>
                <p class="mt-3 leading-7">attr.click is invitation-only. Enter a valid invitation code and your email address, then follow the single-use verification link we send you. Once verified, future sign-ins use a passwordless magic link—there is no password to create or remember.</p>
            </section>

            <section id="create" class="scroll-mt-8">
                <h2 class="text-2xl font-bold text-zinc-950 dark:text-zinc-100">Create a short link</h2>
                <ol class="mt-4 list-decimal space-y-2 pl-5 leading-7 marker:font-semibold marker:text-cyan-700 dark:marker:text-cyan-300">
                    <li>Choose <strong class="font-semibold text-zinc-950 dark:text-zinc-100">Create link</strong> from your dashboard.</li>
                    <li>Paste a public <code class="rounded bg-zinc-100 px-1.5 py-0.5 text-sm text-zinc-900 dark:bg-zinc-800 dark:text-zinc-100">https://</code> or <code class="rounded bg-zinc-100 px-1.5 py-0.5 text-sm text-zinc-900 dark:bg-zinc-800 dark:text-zinc-100">http://</code> destination. Private, local, and unsafe destinations are rejected.</li>
                    <li>Review the generated short URL and its QR code, then save.</li>
                </ol>
                <p class="mt-4 leading-7">Destination query parameters are preserved. When someone opens your short URL, attr.click forwards them to the destination and lets any incoming values override matching stored parameters.</p>
            </section>

            <section id="qr" class="scroll-mt-8">
                <h2 class="text-2xl font-bold text-zinc-950 dark:text-zinc-100">QR codes and templates</h2>
                <p class="mt-3 leading-7">Every saved link has a server-generated QR code. Choose colors and reuse saved QR templates to keep a campaign or brand consistent. Download SVG when you need a vector asset; choose PNG when you need a raster export.</p>
                <p class="mt-3 leading-7">A template is a starting point, not a live dependency. Applying one copies its settings to the new link, so changing a template later never changes an existing printed or downloaded QR code.</p>
            </section>

            <section id="manage" class="scroll-mt-8">
                <h2 class="text-2xl font-bold text-zinc-950 dark:text-zinc-100">Edit, reissue, or delete</h2>
                <p class="mt-3 leading-7">You can change a link’s destination without changing its short URL. Reissue a QR when you need a fresh download—the QR continues to encode the same canonical short URL, so existing printed codes keep working.</p>
                <p class="mt-3 leading-7">Deleting a link is permanent. You must type the exact slug to confirm it, and the link, QR resolution, and associated analytics are removed. Scans of a deleted short URL return a not-found response.</p>
            </section>

            <section id="analytics" class="scroll-mt-8">
                <h2 class="text-2xl font-bold text-zinc-950 dark:text-zinc-100">First-party analytics</h2>
                <p class="mt-3 leading-7">Your dashboard shows link-scoped clicks, recent activity, referrer hosts, and standard UTM dimensions such as source, medium, and campaign. It is designed to answer practical questions about your links—not to build a cross-site audience profile.</p>
            </section>

            <section id="privacy" class="rounded-2xl border border-cyan-200 bg-cyan-50 p-6 dark:border-cyan-900/70 dark:bg-cyan-950/30">
                <h2 class="text-2xl font-bold text-zinc-950 dark:text-zinc-100">Privacy by default</h2>
                <ul class="mt-4 list-disc space-y-2 pl-5 leading-7 marker:text-cyan-700 dark:marker:text-cyan-300">
                    <li>No third-party analytics SDKs, fingerprinting, or outbound telemetry.</li>
                    <li>Only standard UTM values are retained for reporting; other query parameters are forwarded but not stored as analytics data.</li>
                    <li>Reports show referrer hosts, not full referrer URLs, and never expose raw IP addresses.</li>
                    <li>Uploaded QR logos are transformed server-side and kept in private storage.</li>
                </ul>
                <p class="mt-4 leading-7">For implementation details, read the <a href="https://github.com/msitarzewski/attr-click/blob/main/docs/SECURITY_AND_PRIVACY.md" class="font-semibold text-cyan-800 underline decoration-cyan-500/50 underline-offset-4 hover:text-cyan-900 dark:text-cyan-200 dark:hover:text-cyan-100">security and privacy documentation</a>.</p>
            </section>

            <section id="support" class="scroll-mt-8">
                <h2 class="text-2xl font-bold text-zinc-950 dark:text-zinc-100">Need more help?</h2>
                <p class="mt-3 leading-7">attr.click is open source under the <a href="https://github.com/msitarzewski/attr-click/blob/main/LICENSE" class="font-semibold text-cyan-800 underline decoration-cyan-500/50 underline-offset-4 hover:text-cyan-900 dark:text-cyan-200 dark:hover:text-cyan-100">MIT License</a>. Browse the <a href="https://github.com/msitarzewski/attr-click" class="font-semibold text-cyan-800 underline decoration-cyan-500/50 underline-offset-4 hover:text-cyan-900 dark:text-cyan-200 dark:hover:text-cyan-100">source on GitHub</a> for deployment notes, architecture, and contribution guidance.</p>
            </section>
        </div>
    </article>
</x-layout>
