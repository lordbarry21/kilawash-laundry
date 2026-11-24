<x-app-layout>
    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl sm:text-[36px] font-medium tracking-[-0.04em] text-[#141416] leading-tight">Data Layanan</h1>
                <p class="text-sm text-[#71717a] mt-1">Kelola katalog layanan cuci kiloan, satuan, dan express beserta tarif per unit.</p>
            </div>

            @can('manage-laundry')
                <a href="{{ route('services.create') }}" class="btn-ink">
                    + Tambah Layanan
                </a>
            @else
                <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-medium bg-white border border-black/[0.08] text-[#52525b]">
                    Mode Baca Saja (Kasir)
                </span>
            @endcan
        </div>

        @cannot('manage-laundry')
            <div role="note" class="rounded-2xl bg-[#dbeafe] border border-[#2563eb]/20 text-[#1e40af] px-5 py-3.5 text-sm">
                Anda masuk sebagai <strong>Kasir</strong>. Perubahan dan penghapusan tarif layanan khusus untuk akun <strong>Admin</strong>.
            </div>
        @endcannot

        @if(session('success'))
            <div role="status" class="rounded-2xl bg-[#dcfce7] border border-[#16a34a]/20 text-[#14532d] px-5 py-3.5 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div role="alert" class="rounded-2xl bg-[#ffe4e6] border border-[#e11d48]/20 text-[#881337] px-5 py-3.5 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        <section class="bento-card overflow-hidden">
            <div class="kw-table-wrap" tabindex="0" role="region" aria-label="Daftar master data layanan">
                <table class="kw-table-responsive w-full text-left border-collapse text-sm">
                    <thead class="bg-[#fafafa] text-[#71717a] text-xs font-medium">
                        <tr>
                            <th class="px-6 py-3.5 border-b border-black/[0.06]">Nama Layanan</th>
                            <th class="px-6 py-3.5 border-b border-black/[0.06]">Kategori</th>
                            <th class="px-6 py-3.5 border-b border-black/[0.06]">Tarif / Unit</th>
                            <th class="px-6 py-3.5 border-b border-black/[0.06]">Deskripsi</th>
                            <th class="px-6 py-3.5 border-b border-black/[0.06]">Relasi Order</th>
                            @can('manage-laundry')
                                <th class="px-6 py-3.5 border-b border-black/[0.06] text-right">Aksi</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/[0.05]">
                        @forelse($services as $service)
                            <tr class="hover:bg-[#fafafa] transition-colors">
                                <th scope="row" class="px-6 py-4 font-semibold text-[#141416]">
                                    {{ $service->service_name }}
                                </th>
                                <td data-label="Kategori" class="px-6 py-4">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#f4f4f6] text-[#52525b]">
                                        {{ $service->category }}
                                    </span>
                                </td>
                                <td data-label="Tarif" class="px-6 py-4 font-mono-num font-semibold text-[#141416]">
                                    Rp {{ number_format($service->price_per_unit, 0, ',', '.') }}<span class="text-xs font-normal text-[#71717a]">/{{ $service->unit_type }}</span>
                                </td>
                                <td data-label="Deskripsi" class="px-6 py-4 text-[#71717a] max-w-xs">
                                    {{ $service->description }}
                                </td>
                                <td data-label="Relasi Order" class="px-6 py-4 font-mono-num text-xs text-[#52525b]">
                                    {{ $service->order_details_count }} item
                                </td>
                                @can('manage-laundry')
                                    <td data-label="Aksi" class="px-6 py-4 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('services.edit', $service->id) }}"
                                               class="px-3.5 py-1.5 rounded-full border border-black/15 bg-white hover:bg-[#f4f4f6] text-xs font-medium text-[#141416] transition">
                                                Edit
                                            </a>
                                            <form action="{{ route('services.destroy', $service->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Yakin hapus layanan {{ $service->service_name }}?')"
                                                        class="px-3.5 py-1.5 rounded-full bg-[#ffe4e6] hover:bg-[#fecdd3] text-xs font-medium text-[#9f1239] transition">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-[#71717a]">Belum ada data layanan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div>{{ $services->links() }}</div>
    </div>
</x-app-layout>
