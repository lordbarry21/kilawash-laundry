<x-app-layout>
    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Page Header -->
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl sm:text-[34px] font-semibold tracking-[-0.035em] text-[#141416] leading-none">
                    Buat Pesanan
                </h1>
                <p class="text-sm text-[#71717a] mt-1.5">
                    Pilih layanan kiloan, express, atau satuan lalu lengkapi data pelanggan di sebelah kanan.
                </p>
            </div>

            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-black/[0.08] text-xs font-medium text-[#52525b]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#16a34a]"></span>
                Buka 07:00 – 21:00 WIB
            </span>
        </div>

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

            <!-- Left (8 cols): Clean Service Catalog with Inline Category Filter Tabs -->
            <section class="xl:col-span-8 bento-card overflow-hidden">
                <div class="px-6 py-4 border-b border-black/[0.06] flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-[16px] font-semibold tracking-[-0.02em] text-[#141416]">Pilih Layanan</h2>
                        <p class="text-xs text-[#71717a] mt-0.5">Tekan <strong>+</strong> untuk menambah berat (Kg) atau jumlah (Pcs)</p>
                    </div>

                    <!-- Inline Category Filter Pills (Right inside the catalog, NOT in sidebar!) -->
                    <div class="inline-flex flex-wrap items-center gap-1 p-1 rounded-full bg-[#f4f4f6] border border-black/[0.05]">
                        <button type="button" onclick="filterCategory('all', this)"
                                class="cat-tab px-3 py-1 rounded-full text-xs font-medium transition bg-white text-[#141416] shadow-xs">
                            Semua <span class="font-mono-num text-[11px] opacity-65 ml-0.5">{{ $services->count() }}</span>
                        </button>
                        @foreach(['Kiloan', 'Express', 'Satuan'] as $cat)
                            <button type="button" onclick="filterCategory('{{ $cat }}', this)"
                                    class="cat-tab px-3 py-1 rounded-full text-xs font-medium transition text-[#71717a] hover:text-[#141416]">
                                {{ $cat }} <span class="font-mono-num text-[11px] opacity-65 ml-0.5">{{ $services->where('category', $cat)->count() }}</span>
                            </button>
                        @endforeach
                    </div>
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
                    <p class="text-xs text-[#71717a] mt-0.5">Isi nama &amp; nomor WhatsApp pelanggan</p>
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
            document.querySelectorAll('.cat-tab').forEach((btn) => {
                btn.className = 'cat-tab px-3 py-1 rounded-full text-xs font-medium transition text-[#71717a] hover:text-[#141416]';
            });
            element.className = 'cat-tab px-3 py-1 rounded-full text-xs font-medium transition bg-white text-[#141416] shadow-xs';

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
</x-app-layout>
