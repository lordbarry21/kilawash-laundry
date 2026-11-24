<x-app-layout>
    <div class="py-8 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-[#1f3038]">Edit Layanan</h1>
                <p class="text-sm text-[#51656f] mt-1">Perbarui detail dan tarif layanan <strong>{{ $service->service_name }}</strong>.</p>
            </div>
            <a href="{{ route('services.index') }}" class="px-4 py-2 rounded-lg border border-[#1f3038]/20 bg-white text-sm font-medium text-[#1f3038] hover:bg-[#ecf0f2]">
                Kembali
            </a>
        </div>

        @if($errors->any())
            <ul class="list-disc rounded-xl bg-[#ffe4e6] border border-[#be123c]/25 p-4 pl-8 text-sm text-[#881337] space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form action="{{ route('services.update', $service->id) }}" method="POST"
              class="bg-white p-6 sm:p-8 rounded-2xl border border-[#1f3038]/12 shadow-sm space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="service_name" class="block text-sm font-medium text-[#1f3038] mb-1.5">Nama Layanan</label>
                <input type="text" id="service_name" name="service_name" value="{{ old('service_name', $service->service_name) }}" required
                       class="w-full min-h-[44px] border border-[#1f3038]/25 rounded-lg px-3.5 py-2.5 focus:border-[#0d7480] focus:ring-2 focus:ring-[#0d7480]/20 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="category" class="block text-sm font-medium text-[#1f3038] mb-1.5">Kategori</label>
                    <select id="category" name="category" required
                            class="w-full min-h-[44px] border border-[#1f3038]/25 rounded-lg px-3.5 py-2.5 bg-white focus:border-[#0d7480] focus:ring-2 focus:ring-[#0d7480]/20 focus:outline-none">
                        <option value="Kiloan" @selected(old('category', $service->category) === 'Kiloan')>Kiloan</option>
                        <option value="Satuan" @selected(old('category', $service->category) === 'Satuan')>Satuan</option>
                        <option value="Express" @selected(old('category', $service->category) === 'Express')>Express</option>
                    </select>
                </div>

                <div>
                    <label for="unit_type" class="block text-sm font-medium text-[#1f3038] mb-1.5">Satuan</label>
                    <select id="unit_type" name="unit_type" required
                            class="w-full min-h-[44px] border border-[#1f3038]/25 rounded-lg px-3.5 py-2.5 bg-white focus:border-[#0d7480] focus:ring-2 focus:ring-[#0d7480]/20 focus:outline-none">
                        <option value="Kg" @selected(old('unit_type', $service->unit_type) === 'Kg')>Kg</option>
                        <option value="Pcs" @selected(old('unit_type', $service->unit_type) === 'Pcs')>Pcs</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="price_per_unit" class="block text-sm font-medium text-[#1f3038] mb-1.5">Tarif per Unit (Rp)</label>
                <input type="number" id="price_per_unit" name="price_per_unit" min="0" max="2147483647"
                       value="{{ old('price_per_unit', $service->price_per_unit) }}" required
                       class="w-full min-h-[44px] font-mono-num border border-[#1f3038]/25 rounded-lg px-3.5 py-2.5 focus:border-[#0d7480] focus:ring-2 focus:ring-[#0d7480]/20 focus:outline-none">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-[#1f3038] mb-1.5">Deskripsi</label>
                <textarea id="description" name="description" rows="3" required
                          class="w-full border border-[#1f3038]/25 rounded-lg px-3.5 py-2.5 focus:border-[#0d7480] focus:ring-2 focus:ring-[#0d7480]/20 focus:outline-none">{{ old('description', $service->description) }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('services.index') }}" class="px-5 py-2.5 rounded-lg border border-[#1f3038]/20 bg-white text-sm font-medium text-[#1f3038]">Batal</a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-[#0d7480] hover:bg-[#0a5d67] text-white text-sm font-semibold transition">
                    Update Data
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
