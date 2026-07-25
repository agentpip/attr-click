@props([
    'title' => 'attr.click',
    'description' => null,
    'canonical' => null,
    'socialImage' => null,
])

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @if ($description)
        <meta name="description" content="{{ $description }}">
    @endif
    @if ($canonical)
        <link rel="canonical" href="{{ $canonical }}">
        <meta property="og:locale" content="en_US">
        <meta property="og:site_name" content="attr.click">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ $canonical }}">
        <meta property="og:title" content="{{ $title }}">
        <meta property="og:description" content="{{ $description }}">
        <meta property="og:image" content="{{ $socialImage }}">
        <meta property="og:image:secure_url" content="{{ $socialImage }}">
        <meta property="og:image:type" content="image/png">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="attr.click — Open-source short links and QR codes">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $title }}">
        <meta name="twitter:description" content="{{ $description }}">
        <meta name="twitter:image" content="{{ $socialImage }}">
        <meta name="twitter:image:alt" content="attr.click — Open-source short links and QR codes">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="min-h-screen bg-white text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
    @auth
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950">
            <flux:sidebar.brand name="attr.click" :href="route('dashboard')">
                <span class="grid size-6 place-items-center rounded bg-cyan-400 font-black text-zinc-950">a</span>
            </flux:sidebar.brand>

            <flux:navlist variant="outline">
                <flux:navlist.item :href="route('dashboard')" icon="home" :current="request()->routeIs('dashboard')">Dashboard</flux:navlist.item>
                <flux:navlist.item :href="route('links.create')" icon="plus" :current="request()->routeIs('links.create')">Create link</flux:navlist.item>
                <flux:navlist.item :href="route('templates.index')" icon="swatch" :current="request()->routeIs('templates.*')">QR templates</flux:navlist.item>
                @can('access-admin')
                    <flux:navlist.item :href="route('admin.dashboard')" icon="lock-closed" :current="request()->routeIs('admin.*')">Admin</flux:navlist.item>
                @endcan
            </flux:navlist>

            <form method="POST" action="{{ route('logout') }}" class="mt-auto">
                @csrf
                <flux:button type="submit" variant="ghost" class="w-full justify-start">Log out</flux:button>
            </form>
        </flux:sidebar>

        <flux:main class="bg-white dark:bg-zinc-950">
            <div class="mx-auto w-full max-w-6xl">
                <div class="mb-8 flex items-center justify-between lg:hidden">
                    <flux:sidebar.toggle icon="bars-3" />
                    <span class="text-sm font-semibold">attr.click</span>
                    <x-appearance-toggle />
                </div>
                @if (session('status'))
                    <flux:callout class="mb-6" variant="success" icon="check-circle">{{ session('status') }}</flux:callout>
                @endif
                {{ $slot }}
            </div>
        </flux:main>
    @else
        <main class="mx-auto max-w-5xl px-6 py-12">
            <nav class="mb-16 flex items-center justify-between">
                <a href="{{ route('home') }}" class="text-xl font-black tracking-tight">attr<span class="text-cyan-400">.</span>click</a>
                <div class="flex items-center gap-1">
                    <flux:button :href="route('help')" variant="ghost">Help</flux:button>
                    <x-appearance-toggle />
                    <flux:button :href="route('login')" variant="ghost">Sign in</flux:button>
                    <flux:button :href="route('invite.create')" variant="outline">Use an invitation</flux:button>
                </div>
            </nav>
            @if (session('status'))
                <flux:callout class="mb-6" variant="success" icon="check-circle">{{ session('status') }}</flux:callout>
            @endif
            {{ $slot }}
        </main>
    @endauth

    @fluxScripts
</body>
</html>
