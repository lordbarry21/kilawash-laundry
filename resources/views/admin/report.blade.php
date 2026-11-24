<x-app-layout>
    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-[#1f3038]">Laporan Keuangan Laundry</h1>
                <p class="text-sm text-[#51656f] mt-1">Rekap omzet per jenis layanan menggunakan relasi <code>orders → order_details → services</code> (SQL JOIN + GROUP BY).</p>
            </div>
            <button type="button" onclick="window.print()"
                    class="no-print inline-flex items-center gap-2 min-h-[44px] px-5 py-2.5 rounded-lg bg-[#0d7480] hover:bg-[#0a5d67] text-white text-sm font-medium transition">
                Cetak Laporan
            </button>
        </div>

        <!-- Filter Periode & Status -->
        <form method="GET" action="{{ route('admin.report') }}"
              class="no-print bg-white border border-[#1f3038]/12 rounded-2xl p-5 flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[160px]">
                <label for="from" class="block text-xs font-medium text-[#51656f] mb-1">Dari Tanggal</label>
                <input type="date" id="from" name="from" value="{{ $from }}"
                       class="w-full min-h-[40px] px-3 py-1.5 rounded-lg border border-[#1f3038]/20 text-sm">
            </div>
            <div class="flex-1 min-w-[160px]">
                <label for="to" class="block text-xs font-medium text-[#51656f] mb-1">Sampai Tanggal</label>
                <input type="date" id="to" name="to" value="{{ $to }}"
                       class="w-full min-h-[40px] px-3 py-1.5 rounded-lg border border-[#1f3038]/20 text-sm">
            </div>
            <div class="flex-1 min-w-[160px]">
                <label for="status" class="block text-xs font-medium text-[#51656f] mb-1">Status Pesanan</label>
                <select id="status" name="status" class="w-full min-h-[40px] px-3 py-1.5 rounded-lg border border-[#1f3038]/20 text-sm bg-white">
                    <option value="">Semua Status</option>
                    @foreach(['Antrean', 'Dicuci', 'Selesai'] as $st)
                        <option value="{{ $st }}" @selected($status === $st)>{{ $st }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="min-h-[40px] px-4 rounded-lg bg-[#ecf0f2] hover:bg-[#e2e8ec] text-sm font-medium text-[#1f3038]">Tampilkan</button>
                @if($from !== '' || $to !== '' || $status !== '')
                    <a href="{{ route('admin.report') }}" class="text-xs text-[#51656f] hover:underline px-2">Reset</a>
                @endif
            </div>
        </form>

        <!-- Ringkasan 4 KPI -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <article class="bg-white border border-[#1f3038]/12 rounded-xl p-5">
                <p class="text-xs font-medium text-[#51656f]">Jumlah Pesanan</p>
                <p class="font-mono-num text-2xl font-semibold text-[#1f3038] mt-1.5">{{ $summary['total_orders'] }}</p>
            </article>
            <article class="bg-white border border-[#1f3038]/12 rounded-xl p-5">
                <p class="text-xs font-medium text-[#51656f]">Pesanan Selesai</p>
                <p class="font-mono-num text-2xl font-semibold text-[#166534] mt-1.5">{{ $summary['completed_count'] }}</p>
            </article>
            <article class="bg-white border border-[#1f3038]/12 rounded-xl p-5">
                <p class="text-xs font-medium text-[#51656f]">Pendapatan Selesai</p>
                <p class="font-mono-num text-2xl font-semibold text-[#166534] mt-1.5">Rp {{ number_format($summary['completed_revenue'], 0, ',', '.') }}</p>
            </article>
            <article class="bg-white border border-[#1f3038]/12 rounded-xl p-5">
                <p class="text-xs font-medium text-[#51656f]">Total Nilai Order</p>
                <p class="font-mono-num text-2xl font-semibold text-[#0d7480] mt-1.5">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</p>
            </article>
        </section>

        <!-- Rekap per Layanan (JOIN + GROUP BY) -->
        <section class="bg-white border border-[#1f3038]/12 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-[#1f3038]/10">
                <h2 class="text-base font-semibold text-[#1f3038]">Rekap Pendapatan per Jenis Layanan</h2>
            </div>
            <div class="kw-table-wrap">
                <table class="kw-table-responsive w-full text-left border-collapse text-sm">
                    <thead class="bg-[#ecf0f2] text-[#51656f] text-xs font-medium">
                        <tr>
                            <th class="px-5 py-3">Jenis Layanan</th>
                            <th class="px-5 py-3">Kategori</th>
                            <th class="px-5 py-3">Tarif</th>
                            <th class="px-5 py-3">Total Kuantitas</th>
                            <th class="px-5 py-3 text-right">Total Omzet</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#1f3038]/10">
                        @forelse($serviceBreakdown as $row)
                            <tr>
                                <th scope="row" class="px-5 py-3.5 font-semibold text-[#1f3038]">{{ $row->service_name }}</th>
                                <td data-label="Kategori" class="px-5 py-3.5">{{ $row->category }}</td>
                                <td data-label="Tarif" class="px-5 py-3.5 font-mono-num">Rp {{ number_format($row->price_per_unit, 0, ',', '.') }} / {{ $row->unit_type }}</td>
                                <td data-label="Kuantitas" class="px-5 py-3.5 font-mono-num">{{ $row->total_qty }} {{ $row->unit_type }}</td>
                                <td data-label="Omzet" class="px-5 py-3.5 font-mono-num font-semibold text-right text-[#0d7480]">Rp {{ number_format($row->total_omzet, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-[#51656f]">Belum ada data layanan pada filter ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Rincian Pesanan -->
        <section class="bg-white border border-[#1f3038]/12 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-[#1f3038]/10">
                <h2 class="text-base font-semibold text-[#1f3038]">Rincian Transaksi Berelasi</h2>
            </div>
            <div class="kw-table-wrap">
                <table class="kw-table-responsive w-full text-left border-collapse text-sm">
                    <thead class="bg-[#ecf0f2] text-[#51656f] text-xs font-medium">
                        <tr>
                            <th class="px-5 py-3"># ID</th>
                            <th class="px-5 py-3">Tanggal</th>
                            <th class="px-5 py-3">Pelanggan</th>
                            <th class="px-5 py-3">Rincian Item</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#1f3038]/10">
                        @forelse($orders as $order)
                            <tr>
                                <th scope="row" class="px-5 py-3.5 font-mono-num font-semibold">#{{ $order->id }}</th>
                                <td data-label="Tanggal" class="px-5 py-3.5 text-[#51656f]">{{ $order->created_at?->format('d M Y, H:i') }}</td>
                                <td data-label="Pelanggan" class="px-5 py-3.5">
                                    <div class="font-medium text-[#1f3038]">{{ $order->customer_name }}</div>
                                    <div class="text-xs font-mono-num text-[#51656f]">{{ $order->whatsapp_number }}</div>
                                </td>
                                <td data-label="Item" class="px-5 py-3.5 text-xs text-[#51656f]">
                                    @foreach($order->orderDetails as $d)
                                        <div>{{ $d->service->service_name ?? 'Layanan' }} ({{ $d->qty_or_weight }} {{ $d->service->unit_type ?? '' }})</div>
                                    @endforeach
                                </td>
                                <td data-label="Status" class="px-5 py-3.5">{{ $order->status }}</td>
                                <td data-label="Total" class="px-5 py-3.5 font-mono-num font-semibold text-right">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-8 text-center text-[#51656f]">Tidak ada pesanan ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
