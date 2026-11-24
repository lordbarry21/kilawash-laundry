<x-app-layout>
    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-[#1f3038]">Master Data Layanan</h1>
                <p class="text-sm text-[#51656f] mt-1">Kelola katalog layanan cuci kiloan, satuan, dan express beserta tarif per unit.</p>
            </div>

            @can('manage-laundry')
                <a href="{{ route('services.create') }}"
                   class="inline-flex items-center gap-2 min-h-[44px] px-5 py-2.5 rounded-lg bg-[#0d7480] hover:bg-[#0a5d67] text-white text-sm font-medium transition">
                    + Tambah Layanan
                </a>
            @else
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-[#ecf0f2] text-[#51656f]">
                    Mode Baca Saja (Kasir)
                </span>
            @endcan
        </div>

        @cannot('manage-laundry')
            <div role="note" class="bg-[#e0f2fe] border border-[#0369a1]/20 text-[#075985] px-4 py-3 rounded-xl text-sm">
                Anda masuk sebagai <strong>Kasir</strong>. Penambahan, perubahan, dan penghapusan tarif layanan hanya dapat dilakukan oleh akun <strong>Admin</strong> (Gate: <code>manage-laundry</code>).
            </div>
        @endcannot

        @if(session('success'))
            <div role="status" class="bg-[#dcfce7] border border-[#15803d]/25 text-[#14532d] px-4 py-3 rounded-xl text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div role="alert" class="bg-[#ffe4e6] border border-[#be123c]/25 text-[#881337] px-4 py-3 rounded-xl text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        <section class="bg-white border border-[#1f3038]/12 rounded-2xl shadow-sm overflow-hidden">
            <div class="kw-table-wrap" tabindex="0" role="region" aria-label="Daftar master data layanan">
                <table class="kw-table-responsive w-full text-left border-collapse text-sm">
                    <thead class="bg-[#ecf0f2] text-[#51656f] text-xs font-medium">
                        <tr>
                            <th class="px-5 py-3.5 border-b border-[#1f3038]/10">Nama Layanan</th>
                            <th class="px-5 py-3.5 border-b border-[#1f3038]/10">Kategori</th>
                            <th class="px-5 py-3.5 border-b border-[#1f3038]/10">Tarif per Unit</th>
                            <th class="px-5 py-3.5 border-b border-[#1f3038]/10">Deskripsi</th>
                            <th class="px-5 py-3.5 border-b border-[#1f3038]/10">Relasi Order</th>
                            @can('manage-laundry')
                                <th class="px-5 py-3.5 border-b border-[#1f3038]/10 text-right">Aksi</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#1f3038]/10">
                        @forelse($services as $service)
                            <tr class="hover:bg-[#f9fbfc]">
                                <th scope="row" class="px-5 py-4 font-semibold text-[#1f3038]">
                                    {{ $service->service_name }}
                                </th>
                                <td data-label="Kategori" class="px-5 py-4">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#e3f2f4] text-[#07474f]">
                                        {{ $service->category }}
                                    </span>
                                </td>
                                <td data-label="Tarif" class="px-5 py-4 font-mono-num font-semibold text-[#0d7480]">
                                    Rp {{ number_format($service->price_per_unit, 0, ',', '.') }} / {{ $service->unit_type }}
                                </td>
                                <td data-label="Deskripsi" class="px-5 py-4 text-[#51656f] max-w-xs">
                                    {{ $service->description }}
                                </td>
                                <td data-label="Relasi Order" class="px-5 py-4 font-mono-num text-[#51656f]">
                                    {{ $service->order_details_count }} item
                                </td>
                                @can('manage-laundry')
                                    <td data-label="Aksi" class="px-5 py-4 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('services.edit', $service->id) }}"
                                               class="px-3 py-1.5 rounded-lg border border-[#1f3038]/20 bg-white hover:bg-[#ecf0f2] text-xs font-medium text-[#1f3038]">
                                                Edit
                                            </a>
                                            <form action="{{ route('services.destroy', $service->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Yakin hapus layanan {{ $service->service_name }}?')"
                                                        class="px-3 py-1.5 rounded-lg bg-[#ffe4e6] hover:bg-[#fecdd3] text-xs font-medium text-[#881337]">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-[#51656f]">Belum ada data layanan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div>{{ $services->links() }}</div>
    </div>
</x-app-layout>
