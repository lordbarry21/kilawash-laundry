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
        <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12 p-3 sm:p-4 gap-4">
            <!-- Panel Kiri: Rounded Editorial Hero Frame -->
            <section class="lg:col-span-6 relative overflow-hidden rounded-[28px] bg-[#141416] text-white p-8 sm:p-10 lg:p-12 flex flex-col justify-between gap-10 order-2 lg:order-1 min-h-[340px]">
                <img src="{{ asset('images/laundry-hero.jpg') }}" alt="" fetchpriority="high"
                     class="kw-hero-img absolute inset-0 w-full h-full object-cover object-center z-0">
                <div class="absolute inset-0 z-10"
                     style="background: linear-gradient(180deg, rgba(16,18,22,0.48) 0%, rgba(16,18,22,0.34) 38%, rgba(16,18,22,0.86) 76%, rgba(16,18,22,0.94) 100%);"></div>

                <a href="{{ route('customer.index') }}"
                   class="relative z-20 inline-flex items-center gap-2.5 w-fit px-3.5 py-2 rounded-full bg-black/45 border border-white/15 backdrop-blur-md">
                    <span class="w-7 h-7 rounded-full bg-white text-[#141416] inline-flex items-center justify-center">
                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="13" r="5.5"/>
                            <rect x="4" y="3" width="16" height="18" rx="2.5"/>
                        </svg>
                    </span>
                    <span class="text-sm font-semibold tracking-[-0.02em] text-white">kilawash</span>
                </a>

                <div class="relative z-20 space-y-5">
                    <h1 class="text-3xl sm:text-[40px] font-medium tracking-[-0.035em] leading-[1.12] max-w-[16ch] text-white">
                        Operasional laundry modern dalam satu kendali.
                    </h1>
                    <p class="text-sm text-white/80 max-w-[44ch] leading-relaxed">
                        Pencatatan order kiloan &amp; satuan otomatis, kontrol status pengerjaan real-time, dan rekap omzet per kategori layanan.
                    </p>

                    <div class="grid grid-cols-3 gap-2.5 pt-2">
                        <div class="rounded-2xl bg-black/45 border border-white/15 backdrop-blur-md p-3.5">
                            <p class="text-[11px] text-white/70">Reguler</p>
                            <p class="font-mono-num text-sm sm:text-base font-medium text-white mt-1">Rp 7.000</p>
                            <p class="text-[11px] text-white/60 mt-0.5">per Kg</p>
                        </div>
                        <div class="rounded-2xl bg-black/45 border border-white/15 backdrop-blur-md p-3.5">
                            <p class="text-[11px] text-white/70">Express</p>
                            <p class="font-mono-num text-sm sm:text-base font-medium text-white mt-1">Rp 12.000</p>
                            <p class="text-[11px] text-white/60 mt-0.5">4–6 jam</p>
                        </div>
                        <div class="rounded-2xl bg-black/45 border border-white/15 backdrop-blur-md p-3.5">
                            <p class="text-[11px] text-white/70">Superkilat</p>
                            <p class="font-mono-num text-sm sm:text-base font-medium text-white mt-1">Rp 25.000</p>
                            <p class="text-[11px] text-white/60 mt-0.5">5 jam</p>
                        </div>
                    </div>
                </div>

                <p class="relative z-20 text-xs text-white/55">Jl. Melati Raya, Kecamatan Sukmajaya</p>
            </section>

            <!-- Panel Kanan: Pure White Studio Form -->
            <main class="lg:col-span-6 flex items-center justify-center p-4 sm:p-8 order-1 lg:order-2">
                <div class="w-full max-w-[420px] bento-card p-7 sm:p-9">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
