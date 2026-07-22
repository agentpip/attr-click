<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'attr.click' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <main class="mx-auto max-w-5xl px-6 py-12">
        <nav class="mb-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-xl font-black tracking-tight">attr<span class="text-cyan-400">.</span>click</a>
            @auth
                <a href="{{ route('dashboard') }}" class="text-sm font-medium text-slate-300 hover:text-white">Dashboard</a>
            @else
                <a href="{{ route('invite.create') }}" class="text-sm font-medium text-slate-300 hover:text-white">Use an invitation</a>
            @endauth
        </nav>
        @if (session('status'))
            <div class="mb-6 rounded-xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200">{{ session('status') }}</div>
        @endif
        {{ $slot }}
    </main>
</body>
</html>
