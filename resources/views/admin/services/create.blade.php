<x-app-layout>
    <div class="py-8 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-medium tracking-[-0.03em] text-[#141416]">Tambah Layanan</h1>
                <p class="text-sm text-[#71717a] mt-1">Tambahkan jenis layanan cuci baru ke katalog.</p>
            </div>
            <a href="{{ route('services.index') }}" class="btn-soft">Kembali</a>
        </div>

        @if($errors->any())
            <ul class="list-disc rounded-2xl bg-[#ffe4e6] border border-[#e11d48]/20 p-4 pl-8 text-sm text-[#881337] space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form action="{{ route('services.store') }}" method="POST" class="bento-card p-6 sm:p-8 space-y-5">
            @csrf

            <div>
                <label for="service_name" class="block text-xs font-medium text-[#52525b] mb-1.5">Nama Layanan</label>
                <input type="text" id="service_name" name="service_name" value="{{ old('service_name') }}" required
                       placeholder="Contoh: Cuci Sepatu"
                       class="w-full min-h-[44px] border border-black/15 rounded-xl px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/15 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="category" class="block text-xs font-medium text-[#52525b] mb-1.5">Kategori</label>
                    <select id="category" name="category" required
                            class="w-full min-h-[44px] border border-black/15 rounded-xl px-3.5 py-2.5 text-sm bg-white focus:border-[#2563eb] focus:outline-none">
                        <option value="Kiloan" @selected(old('category') === 'Kiloan')>Kiloan</option>
                        <option value="Satuan" @selected(old('category') === 'Satuan')>Satuan</option>
                        <option value="Express" @selected(old('category') === 'Express')>Express</option>
                    </select>
                </div>

                <div>
                    <label for="unit_type" class="block text-xs font-medium text-[#52525b] mb-1.5">Satuan</label>
                    <select id="unit_type" name="unit_type" required
                            class="w-full min-h-[44px] border border-black/15 rounded-xl px-3.5 py-2.5 text-sm bg-white focus:border-[#2563eb] focus:outline-none">
                        <option value="Kg" @selected(old('unit_type') === 'Kg')>Kg</option>
                        <option value="Pcs" @selected(old('unit_type') === 'Pcs')>Pcs</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="price_per_unit" class="block text-xs font-medium text-[#52525b] mb-1.5">Tarif per Unit (Rp)</label>
                <input type="number" id="price_per_unit" name="price_per_unit" min="0" max="2147483647"
                       value="{{ old('price_per_unit') }}" required placeholder="15000"
                       class="w-full min-h-[44px] font-mono-num border border-black/15 rounded-xl px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:outline-none">
            </div>

            <div>
                <label for="description" class="block text-xs font-medium text-[#52525b] mb-1.5">Deskripsi</label>
                <textarea id="description" name="description" rows="3" required
                          placeholder="Jelaskan cakupan layanan dan estimasi pengerjaan"
                          class="w-full border border-black/15 rounded-xl px-3.5 py-2.5 text-sm focus:border-[#2563eb] focus:outline-none">{{ old('description') }}</textarea>
            </div>

            <div class="flex justify-end gap-2.5 pt-2">
                <a href="{{ route('services.index') }}" class="btn-soft">Batal</a>
                <button type="submit" class="btn-ink">Simpan Data</button>
            </div>
        </form>
    </div>
</x-app-layout>
