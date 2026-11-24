<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#f4f4f6">
    <title>KilaWash — Pendaftaran Layanan Laundry</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f4f4f6] text-[#141416] antialiased">
    <div x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false" class="min-h-screen flex bg-[#f4f4f6]">

        <!-- Mobile Backdrop -->
        <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-black/40 backdrop-blur-[2px] lg:hidden" style="display:none"></div>

        <!-- Persistent Left Vertical Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed lg:sticky top-0 left-0 z-50 h-screen w-[260px] shrink-0 bg-white border-r border-black/[0.07] flex flex-col justify-between p-4 transition-transform duration-200 ease-out">

            <div class="space-y-6 overflow-y-auto">
                <!-- Brand -->
                <div class="flex items-center justify-between px-2 pt-1">
                    <a href="{{ route('customer.index') }}" class="flex items-center gap-2.5">
                        <span class="w-9 h-9 rounded-[11px] bg-[#141416] text-white inline-flex items-center justify-center shadow-sm">
                            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="13" r="5.5"/>
                                <rect x="4" y="3" width="16" height="18" rx="2.5"/>
                                <line x1="8" y1="6.5" x2="8.01" y2="6.5"/>
                                <line x1="11.5" y1="6.5" x2="11.51" y2="6.5"/>
                            </svg>
                        </span>
                        <span class="leading-tight">
                            <span class="block text-[16px] font-semibold tracking-[-0.03em] text-[#141416]">kilawash</span>
                            <span class="block text-[11px] text-[#71717a]">Express Laundry</span>
                        </span>
                    </a>
                    <button type="button" @click="sidebarOpen = false" aria-label="Tutup menu"
                            class="lg:hidden w-8 h-8 rounded-full hover:bg-[#f4f4f6] text-[#52525b] inline-flex items-center justify-center">&times;</button>
                </div>

                <!-- Sidebar Menu -->
                <nav aria-label="Menu katalog" class="space-y-5">
                    <div>
                        <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-[0.06em] text-[#a1a1aa]">Kategori Layanan</p>
                        <ul class="space-y-1">
                            <li>
                                <button type="button" onclick="filterCategory('all', this)"
                                        class="side-cat w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-[13.5px] font-medium transition bg-[#141416] text-white shadow-sm">
                                    <span>Semua Layanan</span>
                                    <span class="font-mono-num text-[11px] opacity-80">{{ $services->count() }}</span>
                                </button>
                            </li>
                            @foreach(['Kiloan', 'Express', 'Satuan'] as $cat)
                                <li>
                                    <button type="button" onclick="filterCategory('{{ $cat }}', this)"
                                            class="side-cat w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-[13.5px] font-medium transition text-[#52525b] hover:bg-[#f4f4f6] hover:text-[#141416]">
                                        <span>Layanan {{ $cat }}</span>
                                        <span class="font-mono-num text-[11px] text-[#a1a1aa]">{{ $services->where('category', $cat)->count() }}</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div>
                        <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-[0.06em] text-[#a1a1aa]">Portal Akun</p>
                        <ul class="space-y-1">
                            @guest
                                <li>
                                    <a href="{{ route('login') }}"
                                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-[13.5px] font-medium text-[#52525b] hover:bg-[#f4f4f6] hover:text-[#141416] transition">
                                        <svg class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                                        <span>Masuk Admin / Kasir</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('register') }}"
                                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-[13.5px] font-medium text-[#52525b] hover:bg-[#f4f4f6] hover:text-[#141416] transition">
                                        <svg class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                                        <span>Daftar Akun Baru</span>
                                    </a>
                                </li>
                            @else
                                @can('operate-laundry')
                                    <li>
                                        <a href="{{ route('dashboard') }}"
                                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-[13.5px] font-medium text-[#52525b] hover:bg-[#f4f4f6] hover:text-[#141416] transition">
                                            <svg class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>
                                            <span>Dashboard Overview</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('services.index') }}"
                                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-[13.5px] font-medium text-[#52525b] hover:bg-[#f4f4f6] hover:text-[#141416] transition">
                                            <svg class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41 13.42 20.58a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82Z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                                            <span>Data Layanan</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.report') }}"
                                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-[13.5px] font-medium text-[#52525b] hover:bg-[#f4f4f6] hover:text-[#141416] transition">
                                            <svg class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                                            <span>Laporan Keuangan</span>
                                        </a>
                                    </li>
                                @endcan
                                <li>
                                    <a href="{{ route('profile.edit') }}"
                                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-[13.5px] font-medium text-[#52525b] hover:bg-[#f4f4f6] hover:text-[#141416] transition">
                                        <svg class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
                                        <span>Pengaturan Akun</span>
                                    </a>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </nav>
            </div>

            <!-- Outlet Info Footer -->
            <div class="pt-4 border-t border-black/[0.06] space-y-2.5">
                <div class="rounded-xl bg-[#f4f4f6] p-3.5 space-y-1">
                    <p class="text-xs font-semibold text-[#141416] flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#16a34a]"></span>
                        Buka Hari Ini
                    </p>
                    <p class="text-[11px] text-[#52525b] font-mono-num">07:00 – 21:00 WIB</p>
                    <p class="text-[11px] text-[#71717a]">Jl. Melati Raya, Sukmajaya</p>
                </div>

                @guest
                    <a href="{{ route('login') }}" class="btn-ink w-full !min-h-[40px] !text-xs">
                        Masuk Petugas
                    </a>
                @else
                    <a href="{{ auth()->user()->can('operate-laundry') ? route('dashboard') : route('profile.edit') }}" class="btn-ink w-full !min-h-[40px] !text-xs">
                        {{ auth()->user()->can('operate-laundry') ? 'Buka Dashboard' : Auth::user()->name }}
                    </a>
                @endguest
            </div>
        </aside>

        <!-- Right Main Workspace -->
        <div class="flex-1 min-w-0 flex flex-col">
            <!-- Mobile Header (< lg) -->
            <header class="lg:hidden sticky top-0 z-30 h-16 px-4 bg-white/95 backdrop-blur-md border-b border-black/[0.06] flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <button type="button" @click="sidebarOpen = true" aria-label="Buka menu"
                            class="w-10 h-10 rounded-full bg-[#f4f4f6] text-[#141416] inline-flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                    </button>
                    <span class="text-base font-semibold tracking-[-0.03em] text-[#141416]">kilawash</span>
                </div>
                @guest
                    <a href="{{ route('login') }}" class="btn-ink !min-h-[36px] !px-4 !text-xs">Masuk</a>
                @else
                    <a href="{{ auth()->user()->can('operate-laundry') ? route('dashboard') : route('profile.edit') }}" class="btn-ink !min-h-[36px] !px-4 !text-xs">
                        {{ auth()->user()->can('operate-laundry') ? 'Dashboard' : 'Profil' }}
                    </a>
                @endguest
            </header>

            <main class="max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-7 sm:py-9 space-y-6">
                <!-- Executive Page Header -->
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <h1 class="text-3xl sm:text-[36px] font-medium tracking-[-0.04em] text-[#141416] leading-none">
                            Pemesanan Layanan
                        </h1>
                        <p class="text-sm text-[#71717a] mt-2">
                            Pilih kombinasi cucian kiloan, express, dan satuan dalam satu transaksi tanpa antre.
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-black/[0.08] text-xs font-medium text-[#52525b]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#2563eb]"></span>
                            Konfirmasi otomatis via WhatsApp
                        </span>
                    </div>
                </div>

                <!-- Sleek Compact Architectural Banner (150px tall — zero wasted space) -->
                <section class="relative overflow-hidden rounded-[22px] bg-[#141416] text-white min-h-[144px] flex items-end p-6 sm:px-8 shadow-sm">
                    <img src="{{ asset('images/laundry-banner.jpg') }}" alt="Handuk dan pakaian bersih terlipat rapi" fetchpriority="high"
                         class="kw-hero-img absolute inset-0 w-full h-full object-cover object-center">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#141416]/90 via-[#141416]/65 to-[#141416]/20"></div>
                    <div class="relative z-10 w-full flex flex-wrap items-end justify-between gap-4">
                        <div class="space-y-1">
                            <p class="text-xs font-medium text-white/75 uppercase tracking-wider">Standar Perawatan Pakaian</p>
                            <p class="text-xl sm:text-2xl font-medium tracking-[-0.03em]">Cuci higienis, setrika uap, selesai mulai 4 jam.</p>
                        </div>
                        <div class="flex flex-wrap gap-2 text-xs font-mono-num">
                            <span class="px-3 py-1.5 rounded-full bg-white/15 backdrop-blur-md border border-white/20">Reguler Rp 7.000/Kg</span>
                            <span class="px-3 py-1.5 rounded-full bg-white/15 backdrop-blur-md border border-white/20">Express Rp 12.000/Kg</span>
                            <span class="px-3 py-1.5 rounded-full bg-white/15 backdrop-blur-md border border-white/20">Superkilat Rp 25.000/Kg</span>
                        </div>
                    </div>
                </section>

                @if(session('success'))
                    <div role="status" class="rounded-2xl bg-[#dcfce7] border border-[#16a34a]/20 text-[#14532d] px-5 py-3.5 font-medium text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div role="alert" class="rounded-2xl bg-[#ffe4e6] border border-[#e11d48]/20 text-[#881337] px-5 py-3.5 font-medium text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div role="alert" class="rounded-2xl bg-[#ffe4e6] border border-[#e11d48]/20 text-[#881337] px-5 py-3.5 text-sm">
                        <ul class="list-disc list-inside space-y-1 font-medium">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- 2-Column POS Ordering Workspace -->
                <form id="orderForm" action="{{ route('customer.checkout') }}" method="POST" class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
                    @csrf

                    <!-- Left (8 cols): Clean Stripe-grade Service Rows List -->
                    <section class="xl:col-span-8 bento-card overflow-hidden">
                        <div class="px-6 py-4 border-b border-black/[0.06] flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h2 class="text-[16px] font-semibold tracking-[-0.02em] text-[#141416]">Katalog Tarif &amp; Pilihan Layanan</h2>
                                <p class="text-xs text-[#71717a] mt-0.5">Tekan <strong>+</strong> untuk menambahkan berat (Kg) atau jumlah (Pcs)</p>
                            </div>
                            <span id="activeCategoryBadge" class="px-3 py-1 rounded-full bg-[#f4f4f6] text-xs font-medium text-[#52525b]">Semua Kategori</span>
                        </div>

                        <div class="divide-y divide-black/[0.06]">
                            @forelse($services as $service)
                                <div class="service-card px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-[#fafafa] transition-colors"
                                     data-category="{{ $service->category }}">
                                    <div class="min-w-0 flex-1 space-y-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="text-[15px] font-semibold tracking-[-0.015em] text-[#141416]">{{ $service->service_name }}</h3>
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-medium {{ $service->category === 'Express' ? 'bg-[#dbeafe] text-[#1e40af]' : ($service->category === 'Satuan' ? 'bg-[#fef3c7] text-[#92400e]' : 'bg-[#f4f4f6] text-[#52525b]') }}">
                                                {{ $service->category }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-[#71717a] leading-relaxed">{{ $service->description }}</p>
                                    </div>

                                    <div class="flex items-center justify-between sm:justify-end gap-5 shrink-0">
                                        <div class="text-left sm:text-right">
                                            <p class="font-mono-num text-[15px] font-semibold text-[#141416]">
                                                Rp {{ number_format($service->price_per_unit, 0, ',', '.') }}
                                            </p>
                                            <p class="text-[11px] text-[#71717a]">per {{ $service->unit_type }}</p>
                                        </div>

                                        <div class="inline-flex items-center gap-1 bg-[#f4f4f6] p-1 rounded-full border border-black/[0.06]">
                                            <button type="button" onclick="stepQty('service-{{ $service->id }}', -1)" aria-label="Kurangi {{ $service->service_name }}"
                                                    class="w-8 h-8 rounded-full bg-white text-[#141416] hover:bg-[#141416] hover:text-white inline-flex items-center justify-center text-sm font-semibold transition shadow-xs">−</button>
                                            <input type="number" id="service-{{ $service->id }}" name="items[{{ $service->id }}]"
                                                   min="0" step="1" value="{{ old('items.'.$service->id, 0) }}"
                                                   data-unit="{{ $service->unit_type }}"
                                                   data-name="{{ $service->service_name }}"
                                                   data-price="{{ $service->price_per_unit }}"
                                                   oninput="updateLiveSummary()"
                                                   class="item-qty w-10 text-center bg-transparent border-0 font-mono-num text-sm font-semibold text-[#141416] focus:ring-0 p-0">
                                            <button type="button" onclick="stepQty('service-{{ $service->id }}', 1)" aria-label="Tambah {{ $service->service_name }}"
                                                    class="w-8 h-8 rounded-full bg-[#141416] text-white hover:bg-[#27272a] inline-flex items-center justify-center text-sm font-semibold transition">+</button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-10 text-center text-sm text-[#71717a]">Belum ada layanan tersedia.</div>
                            @endforelse
                        </div>

                        <p id="emptyCategoryMessage" class="hidden p-8 text-center text-sm text-[#71717a]">
                            Belum ada layanan pada kategori ini.
                        </p>
                    </section>

                    <!-- Right (4 cols): Sticky Checkout Summary Panel -->
                    <aside class="xl:col-span-4 xl:sticky xl:top-7 bento-card p-6 space-y-5">
                        <div>
                            <h2 class="text-[16px] font-semibold tracking-[-0.02em] text-[#141416]">Ringkasan Pesanan</h2>
                            <p class="text-xs text-[#71717a] mt-0.5">Lengkapi identitas pemesan untuk antrean cucian</p>
                        </div>

                        <div class="space-y-3.5">
                            <div>
                                <label for="customer_name" class="block text-xs font-medium text-[#52525b] mb-1.5">Nama Lengkap</label>
                                <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}"
                                       maxlength="255" required autocomplete="name" placeholder="Contoh: Dina Putri"
                                       class="w-full min-h-[42px] border border-black/15 rounded-xl px-3.5 py-2 text-sm text-[#141416] focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/15 focus:outline-none">
                            </div>

                            <div>
                                <label for="whatsapp_number" class="block text-xs font-medium text-[#52525b] mb-1.5">Nomor WhatsApp</label>
                                <input type="tel" id="whatsapp_number" name="whatsapp_number" required maxlength="16"
                                       pattern="\+?[0-9]{8,15}" inputmode="tel" autocomplete="tel" value="{{ old('whatsapp_number') }}"
                                       placeholder="081234567890"
                                       class="w-full min-h-[42px] font-mono-num border border-black/15 rounded-xl px-3.5 py-2 text-sm text-[#141416] focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/15 focus:outline-none">
                            </div>
                        </div>

                        <div class="pt-4 border-t border-black/[0.06] space-y-2.5">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[#71717a]">Rincian Dipilih</p>
                            <ul id="liveSummaryList" class="space-y-2 text-xs text-[#52525b]">
                                <li class="text-[#a1a1aa]">Belum ada layanan yang dipilih.</li>
                            </ul>
                        </div>

                        <div class="pt-4 border-t border-black/[0.06] flex items-baseline justify-between">
                            <span class="text-xs font-medium text-[#71717a]">Total Estimasi</span>
                            <span id="liveTotalText" class="font-mono-num text-2xl font-semibold tracking-[-0.03em] text-[#141416]">Rp 0</span>
                        </div>

                        <button type="button" id="review-order" onclick="showConfirmationModal()" class="btn-ink w-full !min-h-[46px]">
                            Pesan Sekarang
                        </button>
                    </aside>

                    <!-- Native <dialog> Modal Konfirmasi Pesanan -->
                    <dialog id="confirmModal" class="kw-modal" closedby="any" aria-labelledby="confirm-title">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-black/[0.06]">
                            <h1 id="confirm-title" class="text-lg font-semibold tracking-[-0.02em] text-[#141416]">Konfirmasi Pesanan</h1>
                            <button type="button" onclick="closeConfirmationModal()" aria-label="Tutup konfirmasi"
                                    class="w-8 h-8 rounded-full inline-flex items-center justify-center text-[#71717a] hover:bg-[#f4f4f6]">&times;</button>
                        </div>

                        <div class="p-6 space-y-4 overflow-y-auto">
                            <div class="space-y-1.5 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-[#71717a]">Nama Pemesan</span>
                                    <strong id="modalName" class="text-[#141416]"></strong>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-[#71717a]">WhatsApp</span>
                                    <strong id="modalWhatsapp" class="font-mono-num text-[#141416]"></strong>
                                </div>
                            </div>

                            <div class="border-t border-b border-black/[0.06] py-3.5 max-h-48 overflow-y-auto">
                                <p class="text-[11px] font-semibold text-[#71717a] uppercase tracking-wider mb-2">Rincian Item</p>
                                <ul id="modalItemList" class="space-y-2 text-sm text-[#141416]"></ul>
                            </div>

                            <div class="flex justify-between items-baseline pt-1">
                                <span class="font-medium text-[#71717a] text-sm">Total Pembayaran</span>
                                <span id="modalTotalPrice" class="font-mono-num text-2xl font-semibold text-[#141416]">Rp 0</span>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-[#fafafa] border-t border-black/[0.06] flex gap-3">
                            <button type="button" id="cancel-confirmation" onclick="closeConfirmationModal()" autofocus class="btn-soft w-1/2">
                                Batal
                            </button>
                            <button type="submit" class="btn-ink w-1/2">
                                Ya, Kirim Pesanan
                            </button>
                        </div>
                    </dialog>
                </form>
            </main>
        </div>
    </div>

    <script>
        function stepQty(inputId, delta) {
            const el = document.getElementById(inputId);
            if (!el) return;
            el.value = String(Math.max(0, (Number(el.value) || 0) + delta));
            updateLiveSummary();
        }

        function updateLiveSummary() {
            let total = 0;
            const list = document.getElementById('liveSummaryList');
            list.replaceChildren();

            document.querySelectorAll('.item-qty').forEach((input) => {
                const qty = Math.max(0, Number(input.value) || 0);
                if (qty <= 0) return;
                const sub = qty * Number(input.dataset.price || 0);
                total += sub;

                const li = document.createElement('li');
                li.className = 'flex items-center justify-between gap-2';
                const left = document.createElement('span');
                left.className = 'truncate';
                left.textContent = `${input.dataset.name} (${qty} ${input.dataset.unit})`;
                const right = document.createElement('span');
                right.className = 'font-mono-num font-medium text-[#141416] shrink-0';
                right.textContent = 'Rp ' + sub.toLocaleString('id-ID');
                li.append(left, right);
                list.appendChild(li);
            });

            if (!list.children.length) {
                const empty = document.createElement('li');
                empty.className = 'text-[#a1a1aa]';
                empty.textContent = 'Belum ada layanan yang dipilih.';
                list.appendChild(empty);
            }

            document.getElementById('liveTotalText').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        function filterCategory(category, element) {
            document.querySelectorAll('.side-cat').forEach((btn) => {
                btn.className = 'side-cat w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-[13.5px] font-medium transition text-[#52525b] hover:bg-[#f4f4f6] hover:text-[#141416]';
            });
            element.className = 'side-cat w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-[13.5px] font-medium transition bg-[#141416] text-white shadow-sm';

            const badge = document.getElementById('activeCategoryBadge');
            if (badge) badge.textContent = category === 'all' ? 'Semua Kategori' : 'Kategori: ' + category;

            const cards = document.querySelectorAll('.service-card');
            let visibleCount = 0;
            cards.forEach((card) => {
                const isVisible = category === 'all' || card.dataset.category === category;
                card.style.display = isVisible ? '' : 'none';
                if (isVisible) visibleCount++;
            });

            const emptyMsg = document.getElementById('emptyCategoryMessage');
            if (emptyMsg && cards.length > 0) {
                emptyMsg.classList.toggle('hidden', visibleCount > 0);
            }
        }

        function showConfirmationModal() {
            const form = document.getElementById('orderForm');
            if (!form.reportValidity()) return;

            const itemList = document.getElementById('modalItemList');
            itemList.replaceChildren();
            let grandTotal = 0;

            document.querySelectorAll('.item-qty').forEach((input) => {
                const quantity = Number(input.value);
                if (quantity <= 0) return;

                const subtotal = quantity * Number(input.dataset.price);
                grandTotal += subtotal;

                const li = document.createElement('li');
                li.className = 'flex justify-between gap-2';
                const left = document.createElement('span');
                left.textContent = `${input.dataset.name} (${quantity} ${input.dataset.unit})`;
                const right = document.createElement('strong');
                right.className = 'font-mono-num shrink-0';
                right.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
                li.append(left, right);
                itemList.appendChild(li);
            });

            if (!itemList.children.length) {
                alert('Pilih minimal satu layanan.');
                return;
            }

            document.getElementById('modalName').textContent = form.elements.customer_name.value.trim();
            document.getElementById('modalWhatsapp').textContent = form.elements.whatsapp_number.value.trim();
            document.getElementById('modalTotalPrice').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');

            document.getElementById('confirmModal').showModal();
        }

        function closeConfirmationModal() {
            document.getElementById('confirmModal').close();
        }

        const confirmDialog = document.getElementById('confirmModal');
        if (confirmDialog && !('closedBy' in HTMLDialogElement.prototype)) {
            let outsideDown = false;
            const isOutside = (e) => {
                const r = confirmDialog.getBoundingClientRect();
                return e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom;
            };
            confirmDialog.addEventListener('pointerdown', (e) => { outsideDown = isOutside(e); });
            confirmDialog.addEventListener('pointerup', (e) => { if (outsideDown && isOutside(e)) confirmDialog.close(); });
        }

        updateLiveSummary();
    </script>
</body>
</html>
