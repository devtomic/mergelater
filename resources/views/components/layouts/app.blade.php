<!DOCTYPE html>
<html lang="en" class="antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'MergeLater' }} - Schedule GitHub PR Merges</title>

    <link rel="icon" href="/favicon.ico" sizes="48x48">
    <link rel="icon" href="/favicon-32x32.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="preconnect" href="https://api.fontshare.com">
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=jetbrains-mono:400,500" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <x-google-analytics />
</head>
<body class="min-h-screen bg-void font-sans noise-overlay">
    {{-- Ambient background effects --}}
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-terminal/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-terminal/3 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 min-h-screen flex flex-col">
        @auth
        <nav class="border-b border-border-subtle">
            <div class="max-w-6xl mx-auto px-6 py-4">
                <div class="flex items-center justify-between">
                    <a href="/dashboard" class="flex items-center gap-3 group">
                        <div class="w-8 h-8 rounded-lg bg-terminal/10 border border-terminal/20 flex items-center justify-center group-hover:bg-terminal/20 transition-colors">
                            <svg class="w-4 h-4 text-terminal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-text">MergeLater</span>
                    </a>

                    <div class="flex items-center gap-6">
                        @if(auth()->user()->is_admin)
                        <a href="/admin" class="text-sm transition-colors {{ request()->is('admin*') ? 'text-text' : 'text-text-muted hover:text-text' }}">Admin</a>
                        @endif
                        <a href="/dashboard" class="text-sm transition-colors {{ request()->is('dashboard') ? 'text-text' : 'text-text-muted hover:text-text' }}">Dashboard</a>
                        <a href="/settings" class="text-sm transition-colors {{ request()->is('settings') ? 'text-text' : 'text-text-muted hover:text-text' }}">Settings</a>

                        <div class="flex items-center gap-3 pl-6 border-l border-border">
                            @if(auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-8 h-8 rounded-full ring-2 ring-border">
                            @endif
                            <form method="POST" action="/logout">
                                @csrf
                                <button type="submit" class="text-sm text-text-muted hover:text-text transition-colors">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        @endauth

        <main class="flex-1">
            {{ $slot }}
        </main>

        <footer class="border-t border-border-subtle py-6">
            <div class="max-w-6xl mx-auto px-6">
                <div class="flex items-center justify-between text-sm text-text-subtle">
                    <span class="font-mono">MergeLater {{ $appVersion }}</span>
                    <span>Schedule your merges. Ship on time.</span>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
