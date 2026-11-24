<x-app-layout>
    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl sm:text-[36px] font-medium tracking-[-0.04em] text-[#141416] leading-tight">Laporan Keuangan</h1>
                <p class="text-sm text-[#71717a] mt-1">Rekapitulasi transaksi dan omzet per jenis layanan secara real-time.</p>
            </div>
            <button type="button" onclick="window.print()" class="no-print btn-ink">
                Cetak Laporan
            </button>
        </div>

        <form method="GET" action="{{ route('admin.report') }}"
              class="no-print bento-card p-5 flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[160px]">
                <label for="from" class="block text-xs font-medium text-[#71717a] mb-1.5">Dari Tanggal</label>
                <input type="date" id="from" name="from" value="{{ $from }}"
                       class="w-full min-h-[42px] px-3.5 py-2 rounded-xl border border-black/15 text-sm">
            </div>
            <div class="flex-1 min-w-[160px]">
                <label for="to" class="block text-xs font-medium text-[#71717a] mb-1.5">Sampai Tanggal</label>
                <input type="date" id="to" name="to" value="{{ $to }}"
                       class="w-full min-h-[42px] px-3.5 py-2 rounded-xl border border-black/15 text-sm">
            </div>
            <div class="flex-1 min-w-[160px]">
                <label for="status" class="block text-xs font-medium text-[#71717a] mb-1.5">Status Pesanan</label>
                <select id="status" name="status" class="w-full min-h-[42px] pl-3.5 pr-8 py-2 rounded-xl border border-black/15 text-sm bg-white">
                    <option value="">Semua Status</option>
                    @foreach(['Antrean', 'Dicuci', 'Selesai'] as $st)
                        <option value="{{ $st }}" @selected($status === $st)>{{ $st }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="btn-ink !min-h-[42px]">Tampilkan</button>
                @if($from !== '' || $to !== '' || $status !== '')
                    <a href="{{ route('admin.report') }}" class="text-xs font-medium text-[#71717a] hover:text-[#141416] px-2">Reset</a>
                @endif
            </div>
        </form>

        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <article class="bento-card p-6">
                <p class="text-xs font-medium text-[#71717a]">Jumlah Pesanan</p>
                <p class="text-3xl font-medium tracking-[-0.03em] text-[#141416] tabular mt-2">{{ $summary['total_orders'] }}</p>
            </article>
            <article class="bento-card p-6">
                <p class="text-xs font-medium text-[#71717a]">Pesanan Selesai</p>
                <p class="text-3xl font-medium tracking-[-0.03em] text-[#16a34a] tabular mt-2">{{ $summary['completed_count'] }}</p>
            </article>
            <article class="bento-card p-6">
                <p class="text-xs font-medium text-[#71717a]">Pendapatan Selesai</p>
                <p class="text-2xl sm:text-3xl font-medium tracking-[-0.03em] text-[#16a34a] tabular mt-2">Rp {{ number_format($summary['completed_revenue'], 0, ',', '.') }}</p>
            </article>
            <article class="bento-card p-6">
                <p class="text-xs font-medium text-[#71717a]">Total Nilai Order</p>
                <p class="text-2xl sm:text-3xl font-medium tracking-[-0.03em] text-[#141416] tabular mt-2">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</p>
            </article>
        </section>

        <section class="bento-card overflow-hidden">
            <div class="px-6 py-4 border-b border-black/[0.06]">
                <h2 class="text-base font-semibold text-[#141416]">Rekap Pendapatan per Jenis Layanan</h2>
            </div>
            <div class="kw-table-wrap">
                <table class="kw-table-responsive w-full text-left border-collapse text-sm">
                    <thead class="bg-[#fafafa] text-[#71717a] text-xs font-medium">
                        <tr>
                            <th class="px-6 py-3.5 border-b border-black/[0.06]">Jenis Layanan</th>
                            <th class="px-6 py-3.5 border-b border-black/[0.06]">Kategori</th>
                            <th class="px-6 py-3.5 border-b border-black/[0.06]">Tarif</th>
                            <th class="px-6 py-3.5 border-b border-black/[0.06]">Total Kuantitas</th>
                            <th class="px-6 py-3.5 border-b border-black/[0.06] text-right">Total Omzet</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/[0.05]">
                        @forelse($serviceBreakdown as $row)
                            <tr>
                                <th scope="row" class="px-6 py-4 font-semibold text-[#141416]">{{ $row->service_name }}</th>
                                <td data-label="Kategori" class="px-6 py-4 text-[#52525b]">{{ $row->category }}</td>
                                <td data-label="Tarif" class="px-6 py-4 font-mono-num text-[#52525b]">Rp {{ number_format($row->price_per_unit, 0, ',', '.') }}/{{ $row->unit_type }}</td>
                                <td data-label="Kuantitas" class="px-6 py-4 font-mono-num">{{ $row->total_qty }} {{ $row->unit_type }}</td>
                                <td data-label="Omzet" class="px-6 py-4 font-mono-num font-semibold text-right text-[#141416]">Rp {{ number_format($row->total_omzet, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-10 text-center text-[#71717a]">Belum ada data layanan pada filter ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="bento-card overflow-hidden">
            <div class="px-6 py-4 border-b border-black/[0.06]">
                <h2 class="text-base font-semibold text-[#141416]">Rincian Transaksi</h2>
            </div>
            <div class="kw-table-wrap">
                <table class="kw-table-responsive w-full text-left border-collapse text-sm">
                    <thead class="bg-[#fafafa] text-[#71717a] text-xs font-medium">
                        <tr>
                            <th class="px-6 py-3.5 border-b border-black/[0.06]">Order</th>
                            <th class="px-6 py-3.5 border-b border-black/[0.06]">Tanggal</th>
                            <th class="px-6 py-3.5 border-b border-black/[0.06]">Pelanggan</th>
                            <th class="px-6 py-3.5 border-b border-black/[0.06]">Item</th>
                            <th class="px-6 py-3.5 border-b border-black/[0.06]">Status</th>
                            <th class="px-6 py-3.5 border-b border-black/[0.06] text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/[0.05]">
                        @forelse($orders as $order)
                            <tr>
                                <th scope="row" class="px-6 py-4 font-mono-num font-semibold">#{{ str_pad((string) $order->id, 3, '0', STR_PAD_LEFT) }}</th>
                                <td data-label="Tanggal" class="px-6 py-4 text-[#71717a]">{{ $order->created_at?->format('d M Y, H:i') }}</td>
                                <td data-label="Pelanggan" class="px-6 py-4">
                                    <div class="font-medium text-[#141416]">{{ $order->customer_name }}</div>
                                    <div class="text-xs font-mono-num text-[#71717a]">{{ $order->whatsapp_number }}</div>
                                </td>
                                <td data-label="Item" class="px-6 py-4 text-xs text-[#52525b]">
                                    @foreach($order->orderDetails as $d)
                                        <div>{{ $d->service->service_name ?? 'Layanan' }} ({{ $d->qty_or_weight }} {{ $d->service->unit_type ?? '' }})</div>
                                    @endforeach
                                </td>
                                <td data-label="Status" class="px-6 py-4">{{ $order->status }}</td>
                                <td data-label="Total" class="px-6 py-4 font-mono-num font-semibold text-right">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-10 text-center text-[#71717a]">Tidak ada pesanan ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
