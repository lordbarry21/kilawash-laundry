<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#f4f4f6">

        <title>{{ config('app.name', 'KilaWash') }} — Manajemen Laundry</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-[#f4f4f6] text-[#141416]">
        <div x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false" class="min-h-screen flex bg-[#f4f4f6]">
            @include('layouts.navigation')

            <!-- Main Content Area -->
            <div class="flex-1 min-w-0 flex flex-col">
                <!-- Mobile / Tablet Top Header (< lg) -->
                <header class="lg:hidden sticky top-0 z-30 h-16 px-4 bg-white/95 backdrop-blur-md border-b border-black/[0.06] flex items-center justify-between gap-3 no-print">
                    <div class="flex items-center gap-3">
                        <button type="button" @click="sidebarOpen = true" aria-label="Buka menu navigasi"
                                class="w-10 h-10 rounded-full bg-[#f4f4f6] hover:bg-[#e4e4e7] text-[#141416] inline-flex items-center justify-center transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16"/>
                            </svg>
                        </button>
                        <a href="{{ route(auth()->user()->can('operate-laundry') ? 'dashboard' : 'customer.index') }}" class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-[#141416] text-white inline-flex items-center justify-center">
                                <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="5.5"/><rect x="4" y="3" width="16" height="18" rx="2.5"/></svg>
                            </span>
                            <span class="text-base font-semibold tracking-[-0.03em] text-[#141416]">kilawash</span>
                        </a>
                    </div>

                    <a href="{{ route('profile.edit') }}" class="w-9 h-9 rounded-full bg-[#141416] text-white text-xs font-semibold inline-flex items-center justify-center overflow-hidden">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" alt="" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        @endif
                    </a>
                </header>

                <main class="flex-1 min-w-0">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
