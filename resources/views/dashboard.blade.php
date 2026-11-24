<x-app-layout>
    @php
        $totalOrders = max(0, (int) $stats['total_orders']);
        $safeTotal   = max(1, $totalOrders);
        $pctAntrean  = $totalOrders > 0 ? (int) round(($stats['antrean_orders'] / $safeTotal) * 100) : 0;
        $pctDicuci   = $totalOrders > 0 ? (int) round(($stats['dicuci_orders'] / $safeTotal) * 100) : 0;
        $pctSelesai  = $totalOrders > 0 ? (int) round(($stats['completed_orders'] / $safeTotal) * 100) : 0;
        $avgOrder    = $totalOrders > 0 ? (int) round($stats['total_revenue'] / $safeTotal) : 0;

        $totalCatRev  = max(1, array_sum(array_column($categoryStats, 'revenue')));
        $maxStatusVal = max(1, $stats['total_orders'], $stats['antrean_orders'], $stats['dicuci_orders'], $stats['completed_orders']);

        $statusSeries = [
            ['label' => 'Antrean',       'val' => $stats['antrean_orders'],   'pct' => $pctAntrean, 'h' => $stats['antrean_orders'] > 0 ? max(16, (int) round(($stats['antrean_orders'] / $maxStatusVal) * 100)) : 4,   'from' => '#f59e0b', 'to' => '#fbbf24', 'dot' => '#d97706'],
            ['label' => 'Sedang Dicuci', 'val' => $stats['dicuci_orders'],    'pct' => $pctDicuci,  'h' => $stats['dicuci_orders'] > 0 ? max(16, (int) round(($stats['dicuci_orders'] / $maxStatusVal) * 100)) : 4,     'from' => '#2563eb', 'to' => '#60a5fa', 'dot' => '#2563eb'],
            ['label' => 'Siap Diambil',  'val' => $stats['completed_orders'], 'pct' => $pctSelesai, 'h' => $stats['completed_orders'] > 0 ? max(16, (int) round(($stats['completed_orders'] / $maxStatusVal) * 100)) : 4, 'from' => '#16a34a', 'to' => '#4ade80', 'dot' => '#16a34a'],
            ['label' => 'Total Pesanan', 'val' => $stats['total_orders'],     'pct' => 100,         'h' => $stats['total_orders'] > 0 ? 100 : 4,                                                                          'from' => '#141416', 'to' => '#3f3f46', 'dot' => '#141416'],
        ];

        $catPalette = [
            'Kiloan'  => ['from' => '#16a34a', 'to' => '#4ade80', 'bar' => '#16a34a'],
            'Express' => ['from' => '#2563eb', 'to' => '#60a5fa', 'bar' => '#2563eb'],
            'Satuan'  => ['from' => '#141416', 'to' => '#52525b', 'bar' => '#141416'],
        ];

        $ringCirc   = 2 * pi() * 20;
        $ringOffset = $ringCirc - (($stats['completion_rate'] / 100) * $ringCirc);
    @endphp

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @if(session('success'))
            <div role="status" class="kw-reveal rounded-2xl bg-[#dcfce7] border border-[#16a34a]/20 text-[#14532d] px-5 py-3.5 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <!-- Top Overview Bar -->
        <div class="kw-reveal flex flex-wrap items-end justify-between gap-4" style="--stagger: 0ms;">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl sm:text-[34px] font-semibold tracking-[-0.035em] text-[#141416] leading-none">Overview</h1>
                    <span class="hidden sm:inline-flex items-center px-3 py-1 rounded-full bg-white border border-black/[0.07] text-xs font-medium text-[#52525b]">
                        {{ Auth::user()->name }}
                    </span>
                </div>
                <p class="text-sm text-[#71717a] mt-1.5">Ringkasan operasional cucian, performa kategori, dan antrean aktif hari ini.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('services.index') }}" class="btn-soft">
                    {{ auth()->user()->can('manage-laundry') ? 'Kelola Layanan' : 'Daftar Layanan' }}
                </a>
                <a href="{{ route('customer.index') }}" class="btn-ink">
                    + Pesanan Baru
                </a>
            </div>
        </div>

        <!-- ROW 1: 4 Minimalist Executive KPI Cards (Staggered Reveal + Spotlight + CountUp) -->
        <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <article class="bento-card kw-reveal kw-spotlight p-5 flex flex-col justify-between gap-3" style="--stagger: 60ms;">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-medium text-[#71717a]">Total Pesanan</span>
                    <span class="w-8 h-8 rounded-lg bg-[#f4f4f6] text-[#141416] inline-flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    </span>
                </div>
                <div class="flex items-baseline justify-between gap-2">
                    <p class="text-3xl font-semibold tracking-[-0.03em] text-[#141416] tabular" data-countup="{{ $stats['total_orders'] }}" data-duration="650">{{ $stats['total_orders'] }}</p>
                    <span class="text-[11px] font-medium text-[#71717a] font-mono-num">{{ $services->count() }} layanan</span>
                </div>
            </article>

            <article class="bento-card kw-reveal kw-spotlight p-5 flex flex-col justify-between gap-3" style="--stagger: 110ms;">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-medium text-[#71717a]">Antrean Masuk</span>
                    <span class="w-8 h-8 rounded-lg bg-[#fef3c7]/70 text-[#b45309] inline-flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15 14"/></svg>
                    </span>
                </div>
                <div class="flex items-baseline justify-between gap-2">
                    <p class="text-3xl font-semibold tracking-[-0.03em] text-[#141416] tabular" data-countup="{{ $stats['antrean_orders'] }}" data-duration="650">{{ $stats['antrean_orders'] }}</p>
                    <span class="px-2 py-0.5 rounded-full bg-[#fef3c7] text-[#92400e] text-[11px] font-semibold font-mono-num" data-countup="{{ $pctAntrean }}" data-suffix="%" data-duration="700">{{ $pctAntrean }}%</span>
                </div>
            </article>

            <article class="bento-card kw-reveal kw-spotlight p-5 flex flex-col justify-between gap-3" style="--stagger: 160ms;">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-medium text-[#71717a]">Sedang Dicuci</span>
                    <span class="w-8 h-8 rounded-lg bg-[#dbeafe]/80 text-[#1d4ed8] inline-flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/><path d="M21 3v5h-5"/></svg>
                    </span>
                </div>
                <div class="flex items-baseline justify-between gap-2">
                    <p class="text-3xl font-semibold tracking-[-0.03em] text-[#141416] tabular" data-countup="{{ $stats['dicuci_orders'] }}" data-duration="650">{{ $stats['dicuci_orders'] }}</p>
                    <span class="px-2 py-0.5 rounded-full bg-[#dbeafe] text-[#1e40af] text-[11px] font-semibold font-mono-num" data-countup="{{ $pctDicuci }}" data-suffix="%" data-duration="700">{{ $pctDicuci }}%</span>
                </div>
            </article>

            <article class="bento-card kw-reveal kw-spotlight p-5 flex flex-col justify-between gap-3" style="--stagger: 210ms;">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-medium text-[#71717a]">Siap Diambil</span>
                    <span class="w-8 h-8 rounded-lg bg-[#dcfce7]/80 text-[#15803d] inline-flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </span>
                </div>
                <div class="flex items-baseline justify-between gap-2">
                    <p class="text-3xl font-semibold tracking-[-0.03em] text-[#141416] tabular" data-countup="{{ $stats['completed_orders'] }}" data-duration="650">{{ $stats['completed_orders'] }}</p>
                    <span class="px-2 py-0.5 rounded-full bg-[#dcfce7] text-[#166534] text-[11px] font-semibold font-mono-num" data-countup="{{ $pctSelesai }}" data-suffix="%" data-duration="700">{{ $pctSelesai }}%</span>
                </div>
            </article>
        </section>

        <!-- ROW 2: Crisp Executive Analytics Chart (8 cols) + Gross Volume & Animated Ring (4 cols) -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <!-- Left 8 Cols: Interactive Precision Analytics Card -->
            <article x-data="{ tab: 'status' }" class="lg:col-span-8 bento-card kw-reveal p-6 sm:p-7 flex flex-col justify-between gap-6" style="--stagger: 240ms;">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-[17px] font-semibold tracking-[-0.02em] text-[#141416]">Statistik Alur Pesanan</h2>
                        <p class="text-xs text-[#71717a] mt-0.5">Proporsi status cucian &amp; kontribusi omzet berdasarkan data terkini</p>
                    </div>

                    <!-- Segmented Tab Switcher -->
                    <div class="inline-flex items-center p-1 rounded-full bg-[#f4f4f6] border border-black/[0.05]">
                        <button type="button" @click="tab = 'status'"
                                :class="tab === 'status' ? 'bg-white text-[#141416] shadow-sm' : 'text-[#71717a] hover:text-[#141416]'"
                                class="px-3.5 py-1.5 rounded-full text-xs font-medium transition-all duration-200">
                            Status Cucian
                        </button>
                        <button type="button" @click="tab = 'kategori'"
                                :class="tab === 'kategori' ? 'bg-white text-[#141416] shadow-sm' : 'text-[#71717a] hover:text-[#141416]'"
                                class="px-3.5 py-1.5 rounded-full text-xs font-medium transition-all duration-200">
                            Omzet Kategori
                        </button>
                    </div>
                </div>

                <!-- TAB 1: Distribusi Status -->
                <div x-show="tab === 'status'"
                     x-transition:enter="transition ease-out duration-250"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="space-y-6">
                    <!-- Proportional Pipeline Bar -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-medium text-[#52525b]">Proporsi Tahapan Pengerjaan</span>
                            <span class="font-mono-num text-[#71717a]">{{ $stats['active_orders'] }} aktif · {{ $stats['completed_orders'] }} selesai</span>
                        </div>
                        <div class="h-2.5 w-full rounded-full bg-[#f4f4f6] overflow-hidden flex gap-0.5">
                            @if($pctAntrean > 0)
                                <div class="h-full bg-[#f59e0b] kw-bar-wipe first:rounded-l-full last:rounded-r-full" style="width: {{ $pctAntrean }}%; --bar-delay: 200ms;" title="Antrean: {{ $pctAntrean }}%"></div>
                            @endif
                            @if($pctDicuci > 0)
                                <div class="h-full bg-[#2563eb] kw-bar-wipe first:rounded-l-full last:rounded-r-full" style="width: {{ $pctDicuci }}%; --bar-delay: 280ms;" title="Sedang Dicuci: {{ $pctDicuci }}%"></div>
                            @endif
                            @if($pctSelesai > 0)
                                <div class="h-full bg-[#16a34a] kw-bar-wipe first:rounded-l-full last:rounded-r-full" style="width: {{ $pctSelesai }}%; --bar-delay: 360ms;" title="Selesai: {{ $pctSelesai }}%"></div>
                            @endif
                        </div>
                    </div>

                    <!-- Clean Gridline Bar Chart with Hover Floating Tooltip -->
                    <div class="relative pt-4">
                        <div class="absolute inset-x-0 top-4 bottom-8 flex flex-col justify-between pointer-events-none">
                            @foreach(['100%', '75%', '50%', '25%', '0%'] as $tick)
                                <div class="flex items-center gap-3">
                                    <span class="w-8 text-right font-mono-num text-[10px] text-[#a1a1aa] shrink-0">{{ $tick }}</span>
                                    <span class="flex-1 border-b border-dashed border-black/[0.07]"></span>
                                </div>
                            @endforeach
                        </div>

                        <div class="relative pl-11 pr-2 grid grid-cols-4 gap-4 sm:gap-8 items-end h-52">
                            @foreach($statusSeries as $idx => $bar)
                                <div class="h-full flex flex-col justify-end items-center group cursor-default">
                                    <div class="w-full flex-1 flex flex-col justify-end items-center pb-2 relative">
                                        <!-- Hover Tooltip Pill -->
                                        <span class="pointer-events-none mb-1 px-2.5 py-1 rounded-full bg-[#141416] text-white font-mono-num text-[10px] font-medium shadow-md opacity-0 translate-y-1 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-200 whitespace-nowrap">
                                            {{ $bar['val'] }} order · {{ $bar['pct'] }}%
                                        </span>
                                        <span class="font-mono-num text-xs font-semibold text-[#141416] mb-1.5 group-hover:opacity-0 transition-opacity duration-150">
                                            {{ $bar['val'] }} <span class="text-[10px] font-normal text-[#71717a]">({{ $bar['pct'] }}%)</span>
                                        </span>
                                        <div class="kw-bar-rise w-full max-w-[52px] rounded-t-xl transition-transform duration-200 ease-out group-hover:-translate-y-1"
                                             style="height: {{ $bar['h'] }}%; --bar-delay: {{ 220 + ($idx * 85) }}ms; background: linear-gradient(180deg, {{ $bar['from'] }} 0%, {{ $bar['to'] }} 100%); box-shadow: 0 8px 20px -6px {{ $bar['from'] }}66;">
                                        </div>
                                    </div>
                                    <div class="pt-2.5 flex items-center gap-1.5 text-[11px] sm:text-xs font-medium text-[#52525b] group-hover:text-[#141416] transition-colors truncate max-w-full">
                                        <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $bar['dot'] }}"></span>
                                        <span class="truncate">{{ $bar['label'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- TAB 2: Omzet per Kategori -->
                <div x-show="tab === 'kategori'"
                     x-transition:enter="transition ease-out duration-250"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     style="display:none" class="space-y-6">
                    <div class="relative pt-4">
                        <div class="absolute inset-x-0 top-4 bottom-8 flex flex-col justify-between pointer-events-none">
                            @foreach(['100%', '75%', '50%', '25%', '0%'] as $tick)
                                <div class="flex items-center gap-3">
                                    <span class="w-8 text-right font-mono-num text-[10px] text-[#a1a1aa] shrink-0">{{ $tick }}</span>
                                    <span class="flex-1 border-b border-dashed border-black/[0.07]"></span>
                                </div>
                            @endforeach
                        </div>

                        <div class="relative pl-11 pr-2 grid grid-cols-3 gap-6 sm:gap-10 items-end h-56">
                            @foreach($categoryStats as $catName => $catData)
                                @php
                                    $pal = $catPalette[$catName] ?? ['from' => '#2563eb', 'to' => '#60a5fa', 'bar' => '#2563eb'];
                                    $sharePct = (int) round(($catData['revenue'] / $totalCatRev) * 100);
                                    $colH = $catData['revenue'] > 0 ? max(18, $catData['percent']) : 4;
                                @endphp
                                <div class="h-full flex flex-col justify-end items-center group cursor-default">
                                    <div class="w-full flex-1 flex flex-col justify-end items-center pb-2">
                                        <span class="font-mono-num text-xs font-semibold text-[#141416] mb-1.5">
                                            Rp {{ number_format($catData['revenue'], 0, ',', '.') }}
                                        </span>
                                        <div class="kw-bar-rise w-full max-w-[64px] rounded-t-xl transition-transform duration-200 ease-out group-hover:-translate-y-1"
                                             style="height: {{ $colH }}%; --bar-delay: {{ 120 + ($loop->index * 90) }}ms; background: linear-gradient(180deg, {{ $pal['from'] }} 0%, {{ $pal['to'] }} 100%); box-shadow: 0 8px 20px -6px {{ $pal['from'] }}55;">
                                        </div>
                                    </div>
                                    <div class="pt-2.5 text-center">
                                        <span class="text-xs font-medium text-[#141416]">{{ $catName }}</span>
                                        <span class="font-mono-num text-[11px] text-[#71717a] ml-1">{{ $sharePct }}%</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Clean Footer Metrics Strip -->
                <div class="pt-4 border-t border-black/[0.06] grid grid-cols-3 gap-4 text-xs">
                    <div>
                        <p class="text-[#71717a]">Rata-rata Order</p>
                        <p class="font-mono-num font-semibold text-[#141416] mt-0.5" data-countup="{{ $avgOrder }}" data-prefix="Rp " data-duration="800">Rp {{ number_format($avgOrder, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-[#71717a]">Cucian Berjalan</p>
                        <p class="font-mono-num font-semibold text-[#141416] mt-0.5">{{ $stats['active_orders'] }} antrean</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[#71717a]">Katalog Aktif</p>
                        <p class="font-mono-num font-semibold text-[#141416] mt-0.5">{{ $services->count() }} layanan</p>
                    </div>
                </div>
            </article>

            <!-- Right 4 Cols: Gross Volume + Precision Bars + Animated Completion Ring -->
            <article class="lg:col-span-4 bento-card kw-reveal kw-spotlight p-6 sm:p-7 flex flex-col justify-between gap-6" style="--stagger: 300ms;">
                <div>
                    <div class="flex items-center justify-between">
                        <h2 class="text-[17px] font-semibold tracking-[-0.02em] text-[#141416]">Gross Volume</h2>
                        <a href="{{ route('admin.report') }}" class="text-xs font-medium text-[#2563eb] hover:underline">Laporan →</a>
                    </div>
                    <p class="text-4xl sm:text-[40px] font-semibold tracking-[-0.04em] text-[#141416] tabular mt-3 leading-none"
                       data-countup="{{ $stats['total_revenue'] }}" data-prefix="Rp " data-duration="850">
                        Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-[#71717a] mt-2">
                        Pendapatan selesai: <strong class="font-mono-num text-[#16a34a]" data-countup="{{ $stats['completed_revenue'] }}" data-prefix="Rp " data-duration="900">Rp {{ number_format($stats['completed_revenue'], 0, ',', '.') }}</strong>
                    </p>
                </div>

                <!-- Sleek Precision Progress Bars -->
                <div class="space-y-4">
                    @foreach($categoryStats as $catName => $catData)
                        @php
                            $pal = $catPalette[$catName] ?? ['bar' => '#2563eb'];
                            $sharePct = (int) round(($catData['revenue'] / $totalCatRev) * 100);
                        @endphp
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-[#52525b]">Layanan {{ $catName }} <span class="font-mono-num text-[#a1a1aa] ml-1">{{ $sharePct }}%</span></span>
                                <span class="font-mono-num font-semibold text-[#141416]">Rp {{ number_format($catData['revenue'], 0, ',', '.') }}</span>
                            </div>
                            <div class="h-1.5 rounded-full bg-[#f4f4f6] overflow-hidden">
                                <div class="h-full rounded-full kw-bar-wipe" style="width: {{ $sharePct }}%; --bar-delay: {{ 280 + ($loop->index * 110) }}ms; background-color: {{ $pal['bar'] }};"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Animated Circular Completion Rate Widget -->
                <div class="pt-4 border-t border-black/[0.06] flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="relative w-12 h-12 shrink-0">
                            <svg class="w-12 h-12 -rotate-90" viewBox="0 0 48 48" aria-hidden="true">
                                <circle cx="24" cy="24" r="20" fill="none" stroke="#f4f4f6" stroke-width="4.5" />
                                <circle cx="24" cy="24" r="20" fill="none" stroke="#16a34a" stroke-width="4.5"
                                        stroke-linecap="round"
                                        class="kw-ring-progress"
                                        stroke-dasharray="{{ number_format($ringCirc, 2, '.', '') }}"
                                        stroke-dashoffset="{{ number_format($ringCirc, 2, '.', '') }}"
                                        data-ring-target="{{ number_format($ringOffset, 2, '.', '') }}" />
                            </svg>
                            <span class="absolute inset-0 flex items-center justify-center font-mono-num text-[11px] font-semibold text-[#141416]"
                                  data-countup="{{ $stats['completion_rate'] }}" data-suffix="%" data-duration="900">
                                {{ $stats['completion_rate'] }}%
                            </span>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-[#141416]">Tingkat Penyelesaian</p>
                            <p class="text-[11px] text-[#71717a] mt-0.5">{{ $stats['completed_orders'] }} dari {{ $stats['total_orders'] }} pesanan selesai</p>
                        </div>
                    </div>
                </div>
            </article>
        </section>

        <!-- ROW 3: Daftar Pesanan Masuk Table Card -->
        <section class="bento-card kw-reveal overflow-hidden" style="--stagger: 350ms;">
            <div class="px-6 sm:px-7 py-5 border-b border-black/[0.06] flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2.5">
                        <h2 class="text-[17px] font-semibold tracking-[-0.02em] text-[#141416]">Daftar Pesanan Masuk</h2>
                        <span class="px-2.5 py-0.5 rounded-full bg-[#f4f4f6] font-mono-num text-[11px] font-medium text-[#52525b]">
                            {{ $orders->count() }} data
                        </span>
                    </div>
                    <p class="text-xs text-[#71717a] mt-0.5">Pantau antrean dan ubah status cucian secara langsung dari Antrean → Dicuci → Selesai</p>
                </div>

                <!-- Integrated Table Filter Toolbar -->
                <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap sm:flex-nowrap items-center gap-2">
                    <div class="flex items-center gap-2 bg-[#f4f4f6] rounded-xl px-3.5 border border-black/[0.06] focus-within:bg-white focus-within:border-[#2563eb] transition min-w-[210px] flex-1 sm:flex-initial">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 text-[#71717a] shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="search" name="q" value="{{ $search }}" placeholder="Cari pelanggan / WA..."
                               class="w-full py-2 text-xs bg-transparent border-0 focus:ring-0 text-[#141416] placeholder-[#a1a1aa]">
                    </div>

                    <div class="relative">
                        <select name="status" aria-label="Filter status"
                                class="appearance-none min-h-[36px] rounded-xl border border-black/[0.09] bg-white pl-3.5 pr-8 py-1.5 text-xs font-medium text-[#141416] focus:border-[#2563eb] focus:outline-none">
                            <option value="">Semua Status</option>
                            @foreach(['Antrean', 'Dicuci', 'Selesai'] as $st)
                                <option value="{{ $st }}" @selected($statusFilter === $st)>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="min-h-[36px] px-4 rounded-xl bg-[#141416] hover:bg-[#27272a] text-white text-xs font-medium transition">
                        Filter
                    </button>

                    @if($search !== '' || $statusFilter !== '')
                        <a href="{{ route('dashboard') }}" class="px-2 text-xs font-medium text-[#71717a] hover:text-[#141416]">Reset</a>
                    @endif
                </form>
            </div>

            <div class="kw-table-wrap" tabindex="0" role="region" aria-label="Daftar pesanan masuk">
                <table class="kw-table-responsive w-full text-left border-collapse text-sm">
                    <thead class="bg-[#fafafa] text-[#71717a] text-xs font-medium">
                        <tr>
                            <th class="px-6 py-3.5 border-b border-black/[0.06]">Order</th>
                            <th class="px-6 py-3.5 border-b border-black/[0.06]">Pelanggan</th>
                            <th class="px-6 py-3.5 border-b border-black/[0.06]">WhatsApp</th>
                            <th class="px-6 py-3.5 border-b border-black/[0.06]">Rincian Layanan</th>
                            <th class="px-6 py-3.5 border-b border-black/[0.06]">Total</th>
                            <th class="px-6 py-3.5 border-b border-black/[0.06]">Status</th>
                            <th class="px-6 py-3.5 border-b border-black/[0.06] text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/[0.05]">
                        @forelse($orders as $order)
                            <tr class="kw-row-in hover:bg-[#fafafa] transition-colors" style="--row-delay: {{ min(8, $loop->index) * 45 + 320 }}ms;">
                                <th scope="row" class="px-6 py-4 font-mono-num font-semibold text-[#141416] whitespace-nowrap">
                                    #{{ str_pad((string) $order->id, 3, '0', STR_PAD_LEFT) }}
                                </th>
                                <td data-label="Pelanggan" class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-[#141416]">{{ $order->customer_name }}</div>
                                    <div class="text-xs text-[#71717a]">{{ $order->created_at?->format('d M Y, H:i') }}</div>
                                </td>
                                <td data-label="WhatsApp" class="px-6 py-4 font-mono-num text-xs text-[#52525b] whitespace-nowrap">
                                    {{ $order->whatsapp_number }}
                                </td>
                                <td data-label="Rincian" class="px-6 py-4">
                                    <ul class="space-y-1 text-xs text-[#52525b]">
                                        @foreach($order->orderDetails as $detail)
                                            <li>
                                                <strong class="text-[#141416] font-medium">{{ $detail->service->service_name ?? 'Layanan' }}</strong>
                                                · {{ $detail->qty_or_weight }} {{ $detail->service->unit_type ?? '' }}
                                                <span class="font-mono-num text-[#71717a] whitespace-nowrap">(Rp {{ number_format($detail->subtotal, 0, ',', '.') }})</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td data-label="Total" class="px-6 py-4 font-mono-num font-semibold text-[#141416] whitespace-nowrap">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </td>
                                <td data-label="Status" class="px-6 py-4">
                                    @if($order->status === 'Antrean')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-[#fef3c7] text-[#92400e]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#d97706]"></span>Antrean
                                        </span>
                                    @elseif($order->status === 'Selesai')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-[#dcfce7] text-[#166534]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#16a34a]"></span>Selesai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-[#dbeafe] text-[#1e40af]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#2563eb]"></span>{{ $order->status }}
                                        </span>
                                    @endif
                                </td>
                                <td data-label="Aksi" class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2 justify-end">
                                        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" aria-label="Status pesanan #{{ $order->id }}" onchange="this.form.submit()"
                                                    class="appearance-none text-xs border border-black/15 rounded-full pl-3.5 pr-8 py-1.5 bg-white font-medium text-[#141416] focus:border-[#2563eb] focus:outline-none">
                                                @foreach(['Antrean', 'Dicuci', 'Selesai'] as $st)
                                                    <option value="{{ $st }}" @selected($order->status === $st)>{{ $st }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                        <a href="{{ route('admin.orders.receipt', $order->id) }}"
                                           class="px-3.5 py-1.5 rounded-full border border-black/12 bg-white hover:bg-[#f4f4f6] text-xs font-medium text-[#141416] transition">
                                            Struk
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-[#71717a]">Belum ada pesanan masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
