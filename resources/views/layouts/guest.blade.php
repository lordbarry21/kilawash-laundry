<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#f7f9fa">

        <title>{{ config('app.name', 'KilaWash') }} — Autentikasi</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Host+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-[#f7f9fa] text-[#1f3038]">
        <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">
            <!-- Panel Kiri: Hero Fotografi Editorial Laundry -->
            <section class="relative overflow-hidden bg-[#16232a] text-white p-8 sm:p-12 lg:p-14 flex flex-col justify-between gap-8 order-2 lg:order-1">
                <img src="{{ asset('images/laundry-hero.jpg') }}" alt="" fetchpriority="high"
                     class="kw-hero-img absolute inset-0 w-full h-full object-cover object-center z-0">
                <div class="absolute inset-0 z-10"
                     style="background: linear-gradient(180deg, rgba(15,29,36,0.55) 0%, rgba(15,29,36,0.42) 34%, rgba(15,29,36,0.85) 70%, rgba(15,29,36,0.95) 100%);"></div>

                <a href="{{ route('customer.index') }}"
                   class="relative z-20 inline-flex items-center gap-3 w-fit px-3.5 py-2 rounded-xl bg-[#0f1d24]/60 border border-white/15 backdrop-blur-md">
                    <span class="w-9 h-9 rounded-lg bg-[#0d7480] text-white inline-flex items-center justify-center">
                        <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="13" r="6"/>
                            <path d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/>
                            <line x1="7" y1="6" x2="7.01" y2="6"/>
                            <line x1="11" y1="6" x2="11.01" y2="6"/>
                        </svg>
                    </span>
                    <span class="leading-tight">
                        <span class="block text-base font-semibold text-white">{{ config('app.name', 'KilaWash') }}</span>
                        <span class="block text-xs text-white/75">Sistem Informasi Manajemen Laundry</span>
                    </span>
                </a>

                <div class="relative z-20 space-y-5">
                    <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight leading-tight max-w-[18ch] text-white">
                        Kelola layanan, pesanan, dan laporan laundry dalam satu pintu.
                    </h1>
                    <p class="text-sm sm:text-base text-white/85 max-w-[44ch] leading-relaxed">
                        Dibangun dengan Laravel, Eloquent ORM, dan MySQL relasional — pencatatan kiloan &amp; satuan otomatis, kontrol hak akses Admin/Kasir, serta laporan omzet akurat.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                        <div class="rounded-xl bg-[#0f1d24]/65 border border-white/15 backdrop-blur-md p-4">
                            <p class="text-xs text-white/75">Reguler</p>
                            <p class="font-mono-num text-base font-semibold text-white mt-1">Rp 7.000/kg</p>
                            <p class="text-xs text-[#7dd3dc] mt-0.5">2–3 hari</p>
                        </div>
                        <div class="rounded-xl bg-[#0f1d24]/65 border border-white/15 backdrop-blur-md p-4">
                            <p class="text-xs text-white/75">Express</p>
                            <p class="font-mono-num text-base font-semibold text-white mt-1">Rp 12.000/kg</p>
                            <p class="text-xs text-[#7dd3dc] mt-0.5">4–6 jam</p>
                        </div>
                        <div class="rounded-xl bg-[#0f1d24]/65 border border-white/15 backdrop-blur-md p-4">
                            <p class="text-xs text-white/75">Superkilat</p>
                            <p class="font-mono-num text-base font-semibold text-white mt-1">Rp 25.000/kg</p>
                            <p class="text-xs text-[#7dd3dc] mt-0.5">5 jam selesai</p>
                        </div>
                    </div>
                </div>

                <div class="relative z-20 flex flex-wrap items-center justify-between gap-2 text-xs text-white/65">
                    <span>Jl. Melati Raya, Kecamatan Sukmajaya</span>
                    <a href="{{ route('erd.index') }}" class="underline hover:text-white">Lihat ERD &amp; Migration</a>
                </div>
            </section>

            <!-- Panel Kanan: Kartu Form Login / Register (di atas pada layar HP) -->
            <main class="flex items-center justify-center p-6 sm:p-10 order-1 lg:order-2 bg-[#f7f9fa]">
                <div class="w-full max-w-md bg-white border border-[#1f3038]/12 rounded-2xl p-7 sm:p-8 shadow-sm">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
