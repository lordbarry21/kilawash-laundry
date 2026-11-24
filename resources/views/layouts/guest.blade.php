<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#f4f4f6">

        <title>{{ config('app.name', 'KilaWash') }} — Autentikasi</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-[#f4f4f6] text-[#141416]">
        <!-- Split-Left Studio Auth Layout: Form bersih di KIRI, Editorial Photography di KANAN -->
        <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12">
            <!-- Sisi Kiri: Brand Header + Form Autentikasi Bersih -->
            <main class="lg:col-span-5 xl:col-span-5 bg-white border-r border-black/[0.06] flex flex-col justify-between p-6 sm:p-10 lg:p-12">
                <div class="flex items-center justify-between">
                    <a href="{{ route('customer.index') }}" class="flex items-center gap-2.5">
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

                    <a href="{{ route('customer.index') }}" class="text-xs font-medium text-[#71717a] hover:text-[#141416] transition">
                        ← Katalog
                    </a>
                </div>

                <div class="w-full max-w-[390px] mx-auto py-8">
                    {{ $slot }}
                </div>

                <p class="text-xs text-[#a1a1aa]">© {{ date('Y') }} KilaWash • Jl. Melati Raya, Kecamatan Sukmajaya</p>
            </main>

            <!-- Sisi Kanan: Full-Height Editorial Laundry Showcase -->
            <section class="lg:col-span-7 xl:col-span-7 p-3 sm:p-4 lg:p-5 bg-[#f4f4f6] flex">
                <div class="relative flex-1 overflow-hidden rounded-[26px] bg-[#141416] text-white p-8 sm:p-10 lg:p-12 flex flex-col justify-between gap-10 min-h-[340px]">
                    <img src="{{ asset('images/laundry-hero.jpg') }}" alt="" fetchpriority="high"
                         class="kw-hero-img absolute inset-0 w-full h-full object-cover object-center z-0">
                    <div class="absolute inset-0 z-10"
                         style="background: linear-gradient(180deg, rgba(16,18,22,0.36) 0%, rgba(16,18,22,0.28) 38%, rgba(16,18,22,0.84) 76%, rgba(16,18,22,0.94) 100%);"></div>

                    <div class="relative z-20 flex items-center justify-between">
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-black/45 border border-white/15 backdrop-blur-md text-xs font-medium text-white/90">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#22c55e]"></span>
                            Sistem Manajemen Laundry
                        </span>
                    </div>

                    <div class="relative z-20 space-y-5">
                        <h1 class="text-3xl sm:text-[42px] font-medium tracking-[-0.035em] leading-[1.1] max-w-[16ch] text-white">
                            Operasional laundry modern dalam satu kendali.
                        </h1>
                        <p class="text-sm text-white/80 max-w-[46ch] leading-relaxed">
                            Pencatatan order kiloan &amp; satuan otomatis, pantau status pengerjaan Antrean hingga Selesai, dan rekap omzet per kategori secara real-time.
                        </p>

                        <div class="grid grid-cols-3 gap-3 pt-2 max-w-lg">
                            <div class="rounded-2xl bg-black/45 border border-white/15 backdrop-blur-md p-4">
                                <p class="text-[11px] text-white/70">Reguler</p>
                                <p class="font-mono-num text-base font-medium text-white mt-1">Rp 7.000</p>
                                <p class="text-[11px] text-white/60 mt-0.5">per Kg</p>
                            </div>
                            <div class="rounded-2xl bg-black/45 border border-white/15 backdrop-blur-md p-4">
                                <p class="text-[11px] text-white/70">Express</p>
                                <p class="font-mono-num text-base font-medium text-white mt-1">Rp 12.000</p>
                                <p class="text-[11px] text-white/60 mt-0.5">4–6 jam</p>
                            </div>
                            <div class="rounded-2xl bg-black/45 border border-white/15 backdrop-blur-md p-4">
                                <p class="text-[11px] text-white/70">Superkilat</p>
                                <p class="font-mono-num text-base font-medium text-white mt-1">Rp 25.000</p>
                                <p class="text-[11px] text-white/60 mt-0.5">5 jam</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </body>
</html>
