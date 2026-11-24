<x-app-layout>
    @php
        $maxStage = max(1, $stats['total_orders'], $stats['antrean_orders'], $stats['dicuci_orders'], $stats['completed_orders']);
        $bars = [
            ['label' => 'Total Pesanan', 'val' => $stats['total_orders'],     'h' => max(26, (int) round(($stats['total_orders'] / $maxStage) * 100)),     'cls' => 'hatch-cobalt'],
            ['label' => 'Antrean Masuk', 'val' => $stats['antrean_orders'],   'h' => max(18, (int) round(($stats['antrean_orders'] / $maxStage) * 100)),   'cls' => 'hatch-cobalt-soft'],
            ['label' => 'Sedang Dicuci', 'val' => $stats['dicuci_orders'],    'h' => max(18, (int) round(($stats['dicuci_orders'] / $maxStage) * 100)),    'cls' => 'hatch-cobalt'],
            ['label' => 'Siap Diambil',  'val' => $stats['completed_orders'], 'h' => max(18, (int) round(($stats['completed_orders'] / $maxStage) * 100)), 'cls' => 'hatch-emerald'],
        ];
    @endphp

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @if(session('success'))
            <div role="status" class="rounded-2xl bg-[#dcfce7] border border-[#16a34a]/20 text-[#14532d] px-5 py-3.5 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <!-- Top Overview Bar (Zentra style) -->
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div class="flex items-center gap-3">
                <h1 class="text-3xl sm:text-[38px] font-medium tracking-[-0.04em] text-[#141416] leading-none">Overview</h1>
                <span class="hidden sm:inline-flex items-center px-3 py-1 rounded-full bg-white border border-black/[0.07] text-xs font-medium text-[#52525b]">
                    {{ Auth::user()->name }}
                </span>
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

        <!-- ROW 1: Zentra Bento Split (8 cols Order Flow Chart + 4 cols Gross Volume) -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <!-- Left Bento Card: Order Stage Hatched Chart + Command Filter -->
            <article class="lg:col-span-8 bento-card p-6 sm:p-7 flex flex-col justify-between gap-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-[17px] font-semibold tracking-[-0.02em] text-[#141416]">Alur Pesanan</h2>
                        <p class="text-xs text-[#71717a] mt-0.5">Distribusi langsung status cucian dari tabel <code>orders</code></p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#f4f4f6] text-xs font-medium text-[#52525b]">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#2563eb]"></span>
                        Live MySQL
                    </span>
                </div>

                <!-- 4 Vertical Hatched Columns -->
                <div class="grid grid-cols-4 gap-3 sm:gap-5 items-end pt-2">
                    @foreach($bars as $bar)
                        <div class="flex flex-col gap-3">
                            <div>
                                <p class="text-[11px] sm:text-xs font-medium text-[#71717a] truncate">{{ $bar['label'] }}</p>
                                <p class="text-2xl sm:text-[30px] font-medium tracking-[-0.03em] text-[#141416] tabular mt-0.5">{{ $bar['val'] }}</p>
                            </div>
                            <div class="h-36 sm:h-44 rounded-2xl bg-[#f4f4f6]/80 p-1.5 flex items-end overflow-hidden">
                                <div class="w-full rounded-xl {{ $bar['cls'] }} relative transition-all duration-300" style="height: {{ $bar['h'] }}%;">
                                    <span class="absolute top-2 left-1/2 -translate-x-1/2 w-8 h-1 rounded-full bg-white/75"></span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Floating Search & Filter Command Bar (Zentra style) -->
                <form method="GET" action="{{ route('dashboard') }}"
                      class="rounded-2xl bg-[#f4f4f6]/90 border border-black/[0.06] p-2 flex flex-wrap sm:flex-nowrap items-center gap-2">
                    <div class="flex items-center gap-2.5 flex-1 min-w-[180px] bg-white rounded-xl px-3.5 border border-black/[0.06]">
                        <svg viewBox="0 0 24 24" class="w-4 h-4 text-[#71717a] shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="search" name="q" value="{{ $search }}" placeholder="Cari nama pelanggan atau nomor WhatsApp..."
                               class="w-full py-2.5 text-sm bg-transparent border-0 focus:ring-0 text-[#141416] placeholder-[#a1a1aa]">
                    </div>
                    <select name="status" aria-label="Filter status"
                            class="min-h-[40px] rounded-xl border border-black/[0.08] bg-white pl-3 pr-8 py-2 text-xs font-medium text-[#141416] focus:border-[#2563eb] focus:outline-none">
                        <option value="">Semua Status</option>
                        @foreach(['Antrean', 'Dicuci', 'Selesai'] as $st)
                            <option value="{{ $st }}" @selected($statusFilter === $st)>{{ $st }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="min-h-[40px] px-4 rounded-xl bg-[#141416] hover:bg-[#27272a] text-white text-xs font-medium transition">
                        Filter
                    </button>
                    @if($search !== '' || $statusFilter !== '')
                        <a href="{{ route('dashboard') }}" class="px-2.5 text-xs font-medium text-[#71717a] hover:text-[#141416]">Reset</a>
                    @endif
                </form>
            </article>

            <!-- Right Bento Card: Gross Volume + Category Hatched Progress -->
            <article class="lg:col-span-4 bento-card p-6 sm:p-7 flex flex-col justify-between gap-6">
                <div>
                    <div class="flex items-center justify-between">
                        <h2 class="text-[17px] font-semibold tracking-[-0.02em] text-[#141416]">Gross Volume</h2>
                        <a href="{{ route('admin.report') }}" class="text-xs font-medium text-[#2563eb] hover:underline">Laporan →</a>
                    </div>
                    <p class="text-4xl sm:text-[42px] font-medium tracking-[-0.04em] text-[#141416] tabular mt-3 leading-none">
                        Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-[#71717a] mt-2">
                        Pendapatan selesai: <strong class="font-mono-num text-[#16a34a]">Rp {{ number_format($stats['completed_revenue'], 0, ',', '.') }}</strong>
                    </p>
                </div>

                <!-- 3 Horizontal Hatched Category Bars -->
                <div class="space-y-4">
                    @php
                        $catStyles = [
                            'Kiloan'  => 'hatch-emerald',
                            'Express' => 'hatch-cobalt',
                            'Satuan'  => 'hatch-rose',
                        ];
                    @endphp
                    @foreach($categoryStats as $catName => $catData)
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-[#52525b]">Layanan {{ $catName }}</span>
                                <span class="font-mono-num font-medium text-[#141416]">Rp {{ number_format($catData['revenue'], 0, ',', '.') }}</span>
                            </div>
                            <div class="h-3 rounded-full bg-[#f4f4f6] overflow-hidden">
                                <div class="h-full rounded-full {{ $catStyles[$catName] ?? 'hatch-cobalt' }}" style="width: {{ $catData['percent'] }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Bottom Completion Pill -->
                <div class="pt-4 border-t border-black/[0.06] flex items-center justify-between text-xs">
                    <span class="text-[#71717a]">Tingkat penyelesaian</span>
                    <span class="font-mono-num font-semibold text-[#141416]">{{ $stats['completion_rate'] }}% selesai</span>
                </div>
            </article>
        </section>

        <!-- ROW 2: 3 Secondary Bento Insight Cards -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <article class="bento-card p-6 flex flex-col justify-between gap-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-[#141416]">Efisiensi Pengerjaan</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-[#f4f4f6] text-[11px] font-medium text-[#52525b]">Hari ini</span>
                </div>
                <div class="flex items-end justify-between gap-3">
                    <p class="text-4xl font-medium tracking-[-0.04em] text-[#141416] tabular">{{ $stats['completion_rate'] }}%</p>
                    <p class="text-xs text-[#16a34a] font-medium">{{ $stats['completed_orders'] }} dari {{ $stats['total_orders'] }} selesai</p>
                </div>
            </article>

            <article class="bento-card p-6 flex flex-col justify-between gap-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-[#141416]">Katalog Layanan</span>
                    <a href="{{ route('services.index') }}" class="text-xs font-medium text-[#71717a] hover:text-[#141416]">Lihat semua</a>
                </div>
                <div class="flex items-end justify-between gap-3">
                    <p class="text-4xl font-medium tracking-[-0.04em] text-[#141416] tabular">{{ $services->count() }}</p>
                    <p class="text-xs text-[#71717a]">Kiloan, Express &amp; Satuan aktif</p>
                </div>
            </article>

            <article class="bento-card overflow-hidden relative min-h-[132px] flex items-end p-5 text-white">
                <img src="{{ asset('images/laundry-banner.jpg') }}" alt="" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-[#141416]/85 via-[#141416]/40 to-transparent"></div>
                <div class="relative z-10 w-full flex items-end justify-between gap-3">
                    <div>
                        <p class="text-[11px] text-white/75 font-medium">Antrean Aktif</p>
                        <p class="text-2xl font-medium tracking-[-0.03em] leading-tight mt-0.5">{{ $stats['active_orders'] }} cucian berjalan</p>
                    </div>
                    <a href="{{ route('customer.index') }}" class="px-3.5 py-1.5 rounded-full bg-white text-[#141416] text-xs font-semibold hover:bg-white/90 transition shrink-0">
                        Input Order
                    </a>
                </div>
            </article>
        </section>

        <!-- ROW 3: Daftar Pesanan Masuk Table Card -->
        <section class="bento-card overflow-hidden">
            <div class="px-6 sm:px-7 py-5 border-b border-black/[0.06] flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-[17px] font-semibold tracking-[-0.02em] text-[#141416]">Daftar Pesanan Masuk</h2>
                    <p class="text-xs text-[#71717a] mt-0.5">Ubah status pengerjaan secara langsung dari Antrean → Dicuci → Selesai</p>
                </div>
                <span class="font-mono-num text-xs text-[#71717a]">{{ $orders->count() }} pesanan ditampilkan</span>
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
                            <tr class="hover:bg-[#fafafa] transition-colors">
                                <th scope="row" class="px-6 py-4 font-mono-num font-semibold text-[#141416]">
                                    #{{ str_pad((string) $order->id, 3, '0', STR_PAD_LEFT) }}
                                </th>
                                <td data-label="Pelanggan" class="px-6 py-4">
                                    <div class="font-medium text-[#141416]">{{ $order->customer_name }}</div>
                                    <div class="text-xs text-[#71717a]">{{ $order->created_at?->format('d M Y, H:i') }}</div>
                                </td>
                                <td data-label="WhatsApp" class="px-6 py-4 font-mono-num text-xs text-[#52525b]">
                                    {{ $order->whatsapp_number }}
                                </td>
                                <td data-label="Rincian" class="px-6 py-4">
                                    <ul class="space-y-1 text-xs text-[#52525b]">
                                        @foreach($order->orderDetails as $detail)
                                            <li>
                                                <strong class="text-[#141416] font-medium">{{ $detail->service->service_name ?? 'Layanan' }}</strong>
                                                · {{ $detail->qty_or_weight }} {{ $detail->service->unit_type ?? '' }}
                                                <span class="font-mono-num text-[#71717a]">(Rp {{ number_format($detail->subtotal, 0, ',', '.') }})</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td data-label="Total" class="px-6 py-4 font-mono-num font-semibold text-[#141416]">
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
                                                    class="text-xs border border-black/15 rounded-full pl-3 pr-8 py-1.5 bg-white font-medium text-[#141416] focus:border-[#2563eb] focus:outline-none">
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
