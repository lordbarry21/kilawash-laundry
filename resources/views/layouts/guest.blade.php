<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#ffffff">

        <title>{{ config('app.name', 'KilaWash') }} — Autentikasi</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-white text-[#141416]">
        <!-- Clean Full-Bleed Split-Screen Auth Layout: Form bersih di KIRI, Foto full edge-to-edge di KANAN -->
        <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12">
            <!-- Sisi Kiri: Brand Header + Form Autentikasi -->
            <main class="lg:col-span-5 xl:col-span-5 bg-white border-r border-black/[0.06] flex flex-col justify-between p-6 sm:p-10 lg:p-12">
                <div class="flex items-center justify-between">
                    <a href="{{ route('login') }}" class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-[9px] bg-[#141416] text-white inline-flex items-center justify-center">
                            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="13" r="5.5"/>
                                <rect x="4" y="3" width="16" height="18" rx="2.5"/>
                                <line x1="8" y1="6.5" x2="8.01" y2="6.5"/>
                                <line x1="11.5" y1="6.5" x2="11.51" y2="6.5"/>
                            </svg>
                        </span>
                        <span class="text-[17px] font-semibold tracking-[-0.03em] text-[#141416]">kilawash</span>
                    </a>

                    @if(request()->routeIs('register'))
                        <a href="{{ route('login') }}" class="px-3.5 py-1.5 rounded-full bg-[#f4f4f6] hover:bg-[#e4e4e7] text-xs font-medium text-[#141416] transition">
                            Masuk
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="px-3.5 py-1.5 rounded-full bg-[#f4f4f6] hover:bg-[#e4e4e7] text-xs font-medium text-[#141416] transition">
                            Daftar Akun
                        </a>
                    @endif
                </div>

                <div class="w-full max-w-[380px] mx-auto py-8">
                    {{ $slot }}
                </div>

                <p class="text-xs text-[#a1a1aa]">© {{ date('Y') }} KilaWash • Jl. Melati Raya, Kecamatan Sukmajaya</p>
            </main>

            <!-- Sisi Kanan: Full-Bleed Edge-to-Edge Editorial Laundry Photo (Zero Outer Padding, Zero Rounded Frame) -->
            <section class="hidden lg:flex lg:col-span-7 xl:col-span-7 relative overflow-hidden bg-[#141416] text-white p-10 xl:p-14 flex-col justify-between min-h-screen">
                <img src="{{ asset('images/laundry-hero.jpg') }}" alt="" fetchpriority="high"
                     class="absolute inset-0 w-full h-full object-cover object-center z-0">
                <div class="absolute inset-0 z-10"
                     style="background: linear-gradient(180deg, rgba(16,18,22,0.34) 0%, rgba(16,18,22,0.22) 40%, rgba(16,18,22,0.82) 78%, rgba(16,18,22,0.94) 100%);"></div>

                <div class="relative z-20 flex items-center justify-between">
                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-black/45 border border-white/15 backdrop-blur-md text-xs font-medium text-white/95">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#22c55e]"></span>
                        Sistem Manajemen Laundry
                    </span>
                </div>

                <div class="relative z-20 space-y-5 max-w-xl">
                    <h1 class="text-3xl xl:text-[42px] font-medium tracking-[-0.035em] leading-[1.1] max-w-[16ch] text-white">
                        Operasional laundry modern dalam satu kendali.
                    </h1>
                    <p class="text-sm text-white/80 max-w-[46ch] leading-relaxed">
                        Pencatatan order kiloan &amp; satuan otomatis, pantau status pengerjaan Antrean hingga Selesai, dan rekap omzet per kategori secara real-time.
                    </p>

                    <div class="grid grid-cols-3 gap-3 pt-2 max-w-lg">
                        <div class="rounded-xl bg-black/45 border border-white/15 backdrop-blur-md p-4">
                            <p class="text-[11px] text-white/70">Reguler</p>
                            <p class="font-mono-num text-base font-medium text-white mt-1">Rp 7.000</p>
                            <p class="text-[11px] text-white/60 mt-0.5">per Kg</p>
                        </div>
                        <div class="rounded-xl bg-black/45 border border-white/15 backdrop-blur-md p-4">
                            <p class="text-[11px] text-white/70">Express</p>
                            <p class="font-mono-num text-base font-medium text-white mt-1">Rp 12.000</p>
                            <p class="text-[11px] text-white/60 mt-0.5">4–6 jam</p>
                        </div>
                        <div class="rounded-xl bg-black/45 border border-white/15 backdrop-blur-md p-4">
                            <p class="text-[11px] text-white/70">Superkilat</p>
                            <p class="font-mono-num text-base font-medium text-white mt-1">Rp 25.000</p>
                            <p class="text-[11px] text-white/60 mt-0.5">5 jam</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </body>
</html>
