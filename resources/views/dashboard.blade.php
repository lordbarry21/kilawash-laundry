<x-app-layout>
    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @if(session('success'))
            <div role="status" class="bg-[#dcfce7] border border-[#15803d]/25 text-[#14532d] px-4 py-3 rounded-xl text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <!-- 1. Hero Editorial Dashboard -->
        <section class="grid grid-cols-1 lg:grid-cols-12 bg-white border border-[#1f3038]/12 rounded-2xl overflow-hidden shadow-sm">
            <div class="lg:col-span-7 p-6 sm:p-8 flex flex-col justify-between gap-5">
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#e3f2f4] text-[#07474f] text-xs font-medium">
                        {{ $stats['active_orders'] }} antrean &amp; cucian aktif hari ini
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-[#1f3038] mt-3">
                        Halo, {{ Auth::user()->name }}
                    </h1>
                    <p class="text-sm text-[#51656f] mt-1.5 max-w-[48ch]">
                        Kelola pesanan masuk, perbarui status cucian dari Antrean hingga Selesai, dan pantau omzet layanan laundry secara real-time.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2.5">
                    <a href="{{ route('customer.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-[#0d7480] hover:bg-[#0a5d67] text-white text-sm font-medium transition">
                        + Catat Pesanan Baru
                    </a>
                    <a href="{{ route('services.index') }}"
                       class="inline-flex items-center px-4 py-2.5 rounded-lg border border-[#1f3038]/20 bg-white hover:bg-[#ecf0f2] text-[#1f3038] text-sm font-medium transition">
                        {{ auth()->user()->can('manage-laundry') ? 'Kelola Layanan' : 'Lihat Daftar Layanan' }}
                    </a>
                    <a href="{{ route('admin.report') }}"
                       class="inline-flex items-center px-4 py-2.5 rounded-lg border border-[#1f3038]/20 bg-white hover:bg-[#ecf0f2] text-[#1f3038] text-sm font-medium transition">
                        Laporan Keuangan
                    </a>
                </div>
            </div>

            <div class="lg:col-span-5 relative min-h-[200px] bg-[#ecf0f2] overflow-hidden order-first lg:order-last">
                <img src="{{ asset('images/laundry-banner.jpg') }}" alt="Tumpukan pakaian bersih terlipat rapi" fetchpriority="high"
                     class="w-full h-full object-cover">
            </div>
        </section>

        <!-- 2. Grid 4 Kartu KPI -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" aria-label="Ringkasan statistik pesanan">
            <article class="bg-white border border-[#1f3038]/12 rounded-xl p-5">
                <p class="text-xs font-medium text-[#51656f]">Total Pesanan</p>
                <p class="font-mono-num text-2xl font-semibold text-[#1f3038] mt-1.5">{{ $stats['total_orders'] }}</p>
                <p class="text-xs text-[#51656f] mt-1">Seluruh pesanan tercatat</p>
            </article>

            <article class="bg-white border border-[#1f3038]/12 rounded-xl p-5">
                <p class="text-xs font-medium text-[#51656f]">Antrean &amp; Dicuci</p>
                <p class="font-mono-num text-2xl font-semibold text-[#92400e] mt-1.5">{{ $stats['active_orders'] }}</p>
                <p class="text-xs text-[#51656f] mt-1">Sedang dalam pengerjaan</p>
            </article>

            <article class="bg-white border border-[#1f3038]/12 rounded-xl p-5">
                <p class="text-xs font-medium text-[#51656f]">Cucian Selesai</p>
                <p class="font-mono-num text-2xl font-semibold text-[#166534] mt-1.5">{{ $stats['completed_orders'] }}</p>
                <p class="text-xs text-[#51656f] mt-1">Siap diambil pelanggan</p>
            </article>

            <article class="bg-white border border-[#1f3038]/12 rounded-xl p-5">
                <p class="text-xs font-medium text-[#51656f]">Total Nilai Order</p>
                <p class="font-mono-num text-2xl font-semibold text-[#0d7480] mt-1.5">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                <p class="text-xs text-[#51656f] mt-1 font-mono-num">Selesai: Rp {{ number_format($stats['completed_revenue'], 0, ',', '.') }}</p>
            </article>
        </section>

        <!-- 3. Tabel Daftar Pesanan Masuk (Rekap Transaksi) -->
        <section class="bg-white border border-[#1f3038]/12 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-[#1f3038]/10 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-[#1f3038]">Daftar Pesanan Masuk</h2>
                    <p class="text-xs text-[#51656f] mt-0.5">Relasi Eloquent <code>Order::with('orderDetails.service')</code> — ubah dropdown untuk update status langsung</p>
                </div>

                <!-- Filter Pencarian & Status -->
                <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-center gap-2">
                    <input type="search" name="q" value="{{ $search }}" placeholder="Cari nama / WhatsApp..."
                           class="min-h-[38px] px-3 py-1.5 rounded-lg border border-[#1f3038]/20 text-sm text-[#1f3038] focus:border-[#0d7480] focus:outline-none">
                    <select name="status" class="min-h-[38px] px-3 py-1.5 rounded-lg border border-[#1f3038]/20 text-sm text-[#1f3038] focus:border-[#0d7480] focus:outline-none">
                        <option value="">Semua Status</option>
                        @foreach(['Antrean', 'Dicuci', 'Selesai'] as $st)
                            <option value="{{ $st }}" @selected($statusFilter === $st)>{{ $st }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="min-h-[38px] px-3.5 rounded-lg bg-[#ecf0f2] hover:bg-[#e2e8ec] text-[#1f3038] text-sm font-medium">Filter</button>
                    @if($search !== '' || $statusFilter !== '')
                        <a href="{{ route('dashboard') }}" class="text-xs text-[#51656f] hover:underline px-2">Reset</a>
                    @endif
                </form>
            </div>

            <div class="kw-table-wrap" tabindex="0" role="region" aria-label="Daftar pesanan masuk">
                <table class="kw-table-responsive w-full text-left border-collapse text-sm">
                    <thead class="bg-[#ecf0f2] text-[#51656f] text-xs font-medium">
                        <tr>
                            <th class="px-5 py-3.5 border-b border-[#1f3038]/10"># ID</th>
                            <th class="px-5 py-3.5 border-b border-[#1f3038]/10">Pelanggan</th>
                            <th class="px-5 py-3.5 border-b border-[#1f3038]/10">WhatsApp</th>
                            <th class="px-5 py-3.5 border-b border-[#1f3038]/10">Rincian Pesanan</th>
                            <th class="px-5 py-3.5 border-b border-[#1f3038]/10">Total Harga</th>
                            <th class="px-5 py-3.5 border-b border-[#1f3038]/10">Status</th>
                            <th class="px-5 py-3.5 border-b border-[#1f3038]/10 text-right">Aksi Status &amp; Struk</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#1f3038]/10">
                        @forelse($orders as $order)
                            <tr class="hover:bg-[#f9fbfc]">
                                <th scope="row" class="px-5 py-4 font-mono-num font-semibold text-[#1f3038]">
                                    #{{ $order->id }}
                                </th>
                                <td data-label="Pelanggan" class="px-5 py-4">
                                    <div class="font-medium text-[#1f3038]">{{ $order->customer_name }}</div>
                                    <div class="text-xs text-[#51656f]">{{ $order->created_at?->format('d M Y, H:i') }}</div>
                                </td>
                                <td data-label="WhatsApp" class="px-5 py-4">
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-[#e0f2fe] text-[#0369a1] font-mono-num text-xs font-medium">
                                        {{ $order->whatsapp_number }}
                                    </span>
                                </td>
                                <td data-label="Rincian Pesanan" class="px-5 py-4">
                                    <ul class="space-y-1 text-xs text-[#51656f]">
                                        @foreach($order->orderDetails as $detail)
                                            <li>
                                                <strong class="text-[#1f3038]">{{ $detail->service->service_name ?? 'Layanan' }}</strong>
                                                · {{ $detail->qty_or_weight }} {{ $detail->service->unit_type ?? '' }}
                                                <span class="font-mono-num">(Rp {{ number_format($detail->subtotal, 0, ',', '.') }})</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td data-label="Total Harga" class="px-5 py-4 font-mono-num font-semibold text-[#0d7480]">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </td>
                                <td data-label="Status" class="px-5 py-4">
                                    @if($order->status === 'Antrean')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-[#fef3c7] text-[#92400e]">Antrean</span>
                                    @elseif($order->status === 'Selesai')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-[#dcfce7] text-[#166534]">Selesai</span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-[#e0f2fe] text-[#0369a1]">{{ $order->status }}</span>
                                    @endif
                                </td>
                                <td data-label="Aksi" class="px-5 py-4 text-right">
                                    <div class="inline-flex items-center gap-2 justify-end">
                                        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" aria-label="Status pesanan #{{ $order->id }}" onchange="this.form.submit()"
                                                    class="text-xs border border-[#1f3038]/25 rounded-lg pl-2.5 pr-8 py-1.5 bg-white font-medium text-[#1f3038] focus:border-[#0d7480] focus:outline-none">
                                                @foreach(['Antrean', 'Dicuci', 'Selesai'] as $st)
                                                    <option value="{{ $st }}" @selected($order->status === $st)>{{ $st }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                        <a href="{{ route('admin.orders.receipt', $order->id) }}"
                                           class="px-3 py-1.5 rounded-lg border border-[#1f3038]/20 bg-white hover:bg-[#ecf0f2] text-xs font-medium text-[#1f3038]">
                                            Struk
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-[#51656f]">Belum ada pesanan masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
