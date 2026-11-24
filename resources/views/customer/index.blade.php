<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#f4f4f6">
    <title>KilaWash — Layanan Laundry Kiloan &amp; Satuan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f4f4f6] text-[#141416] antialiased">
    <!-- Minimalist Top Pill Navbar (Zero ERD link exposed) -->
    <header class="bg-[#f4f4f6]/90 backdrop-blur-md sticky top-0 z-30 border-b border-black/[0.05]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('customer.index') }}" class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-[9px] bg-[#141416] text-white inline-flex items-center justify-center shadow-sm">
                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="13" r="5.5"/>
                        <rect x="4" y="3" width="16" height="18" rx="2.5"/>
                        <line x1="8" y1="6.5" x2="8.01" y2="6.5"/>
                        <line x1="11.5" y1="6.5" x2="11.51" y2="6.5"/>
                    </svg>
                </span>
                <span class="text-[17px] font-semibold tracking-[-0.03em] text-[#141416]">kilawash</span>
            </a>

            <div class="flex items-center gap-2.5">
                @guest
                    <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center px-4 py-2 rounded-full text-[13.5px] font-medium text-[#52525b] hover:text-[#141416] hover:bg-black/[0.04] transition">
                        Daftar
                    </a>
                    <a href="{{ route('login') }}" class="btn-ink !min-h-[40px] !px-5">
                        Masuk
                    </a>
                @else
                    <a href="{{ auth()->user()->can('operate-laundry') ? route('dashboard') : route('profile.edit') }}" class="btn-ink !min-h-[40px] !px-5">
                        {{ auth()->user()->can('operate-laundry') ? 'Dashboard Admin' : 'Akun Saya' }}
                    </a>
                @endguest
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 space-y-8">
        <!-- Editorial Bento Hero -->
        <section class="bento-card overflow-hidden grid grid-cols-1 lg:grid-cols-12">
            <div class="lg:col-span-7 p-7 sm:p-10 lg:p-12 flex flex-col justify-between gap-7">
                <div class="space-y-4">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#f4f4f6] text-[#52525b] text-xs font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#16a34a]"></span>
                        Buka setiap hari • Kiloan, Express &amp; Satuan
                    </span>
                    <h1 class="text-3xl sm:text-[44px] font-medium tracking-[-0.04em] text-[#141416] leading-[1.08] max-w-[16ch]">
                        Cucian bersih, rapi, dan tepat waktu.
                    </h1>
                    <p class="text-sm sm:text-base text-[#71717a] max-w-[46ch] leading-relaxed">
                        Gabungkan layanan kiloan dan satuan dalam satu pesanan. Hitung estimasi biaya seketika dan pantau pengerjaan langsung lewat WhatsApp.
                    </p>
                </div>

                <div class="grid grid-cols-3 gap-3 pt-2 border-t border-black/[0.06]">
                    <div>
                        <p class="text-xs text-[#71717a]">Mulai dari</p>
                        <p class="font-mono-num text-lg sm:text-xl font-semibold text-[#141416] mt-0.5">Rp 7.000</p>
                    </div>
                    <div>
                        <p class="text-xs text-[#71717a]">Express kilat</p>
                        <p class="font-mono-num text-lg sm:text-xl font-semibold text-[#141416] mt-0.5">4–6 jam</p>
                    </div>
                    <div>
                        <p class="text-xs text-[#71717a]">Pilihan katalog</p>
                        <p class="font-mono-num text-lg sm:text-xl font-semibold text-[#141416] mt-0.5">{{ $services->count() }} paket</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 relative min-h-[240px] bg-[#e4e4e7] overflow-hidden order-first lg:order-last">
                <img src="{{ asset('images/laundry-banner.jpg') }}" alt="Handuk dan pakaian bersih terlipat rapi di meja laundry" fetchpriority="high"
                     class="kw-hero-img w-full h-full object-cover">
            </div>
        </section>

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

        <!-- 2-Column Ordering Workspace (Left: Catalog + Steppers | Right: Sticky Order Summary) -->
        <form id="orderForm" action="{{ route('customer.checkout') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            @csrf

            <!-- Left Column (8 cols): Filter Pills + Bento Service Cards -->
            <div class="lg:col-span-8 space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-semibold tracking-[-0.025em] text-[#141416]">Pilih Layanan</h2>
                        <p class="text-xs text-[#71717a] mt-0.5">Atur perkiraan berat (Kg) atau jumlah (Pcs) sesuai kebutuhan Anda</p>
                    </div>

                    <div class="inline-flex flex-wrap items-center gap-1 p-1 rounded-full bg-white border border-black/[0.07]" role="group" aria-label="Filter kategori layanan">
                        <button type="button" onclick="filterCategory('all', this)"
                                class="btn-category px-4 py-1.5 rounded-full text-xs font-medium transition bg-[#141416] text-white">
                            Semua
                        </button>
                        <button type="button" onclick="filterCategory('Kiloan', this)"
                                class="btn-category px-4 py-1.5 rounded-full text-xs font-medium transition text-[#52525b] hover:text-[#141416]">
                            Kiloan
                        </button>
                        <button type="button" onclick="filterCategory('Satuan', this)"
                                class="btn-category px-4 py-1.5 rounded-full text-xs font-medium transition text-[#52525b] hover:text-[#141416]">
                            Satuan
                        </button>
                        <button type="button" onclick="filterCategory('Express', this)"
                                class="btn-category px-4 py-1.5 rounded-full text-xs font-medium transition text-[#52525b] hover:text-[#141416]">
                            Express
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse($services as $service)
                        <article class="service-card bento-card bento-card--hover p-5 flex flex-col justify-between gap-5"
                                 data-category="{{ $service->category }}">
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-[#f4f4f6] text-[#52525b]">
                                        {{ $service->category }}
                                    </span>
                                    <span class="font-mono-num text-sm font-semibold text-[#141416]">
                                        Rp {{ number_format($service->price_per_unit, 0, ',', '.') }}<span class="text-xs font-normal text-[#71717a]">/{{ $service->unit_type }}</span>
                                    </span>
                                </div>
                                <h3 class="text-[16px] font-semibold tracking-[-0.02em] text-[#141416]">{{ $service->service_name }}</h3>
                                <p class="text-xs text-[#71717a] leading-relaxed">{{ $service->description }}</p>
                            </div>

                            <!-- Tactile Stepper Control -->
                            <div class="pt-3.5 border-t border-black/[0.06] flex items-center justify-between gap-3">
                                <label for="service-{{ $service->id }}" class="text-xs font-medium text-[#52525b]">
                                    {{ $service->unit_type === 'Kg' ? 'Berat (Kg)' : 'Jumlah (Pcs)' }}
                                </label>
                                <div class="inline-flex items-center gap-1.5 bg-[#f4f4f6] p-1 rounded-full">
                                    <button type="button" onclick="stepQty('service-{{ $service->id }}', -1)" aria-label="Kurangi {{ $service->service_name }}"
                                            class="w-8 h-8 rounded-full bg-white text-[#141416] border border-black/[0.08] hover:bg-[#141416] hover:text-white inline-flex items-center justify-center text-sm font-semibold transition">−</button>
                                    <input type="number" id="service-{{ $service->id }}" name="items[{{ $service->id }}]"
                                           min="0" step="1" value="{{ old('items.'.$service->id, 0) }}"
                                           data-unit="{{ $service->unit_type }}"
                                           data-name="{{ $service->service_name }}"
                                           data-price="{{ $service->price_per_unit }}"
                                           oninput="updateLiveSummary()"
                                           class="item-qty w-12 text-center bg-transparent border-0 font-mono-num text-sm font-semibold text-[#141416] focus:ring-0 p-0">
                                    <button type="button" onclick="stepQty('service-{{ $service->id }}', 1)" aria-label="Tambah {{ $service->service_name }}"
                                            class="w-8 h-8 rounded-full bg-[#141416] text-white hover:bg-[#27272a] inline-flex items-center justify-center text-sm font-semibold transition">+</button>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="sm:col-span-2 bento-card p-10 text-center text-[#71717a]">
                            Belum ada layanan laundry yang tersedia.
                        </div>
                    @endforelse
                </div>

                <p id="emptyCategoryMessage" class="hidden bento-card p-8 text-center text-sm text-[#71717a]">
                    Belum ada layanan pada kategori ini.
                </p>
            </div>

            <!-- Right Column (4 cols): Sticky Checkout & Ringkasan Pesanan -->
            <aside class="lg:col-span-4 lg:sticky lg:top-24 bento-card p-6 space-y-5">
                <div>
                    <h2 class="text-[17px] font-semibold tracking-[-0.02em] text-[#141416]">Ringkasan Pesanan</h2>
                    <p class="text-xs text-[#71717a] mt-0.5">Isi data pemesan untuk konfirmasi antrean</p>
                </div>

                <div class="space-y-3.5">
                    <div>
                        <label for="customer_name" class="block text-xs font-medium text-[#52525b] mb-1.5">Nama Lengkap</label>
                        <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}"
                               maxlength="255" required autocomplete="name" placeholder="Contoh: Dina Putri"
                               class="w-full min-h-[44px] border border-black/15 rounded-xl px-3.5 py-2 text-sm text-[#141416] focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/15 focus:outline-none">
                    </div>

                    <div>
                        <label for="whatsapp_number" class="block text-xs font-medium text-[#52525b] mb-1.5">Nomor WhatsApp</label>
                        <input type="tel" id="whatsapp_number" name="whatsapp_number" required maxlength="16"
                               pattern="\+?[0-9]{8,15}" inputmode="tel" autocomplete="tel" value="{{ old('whatsapp_number') }}"
                               placeholder="081234567890"
                               class="w-full min-h-[44px] font-mono-num border border-black/15 rounded-xl px-3.5 py-2 text-sm text-[#141416] focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/15 focus:outline-none">
                    </div>
                </div>

                <!-- Live Selected Items List -->
                <div class="pt-4 border-t border-black/[0.06] space-y-2.5">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-[#71717a]">Item Dipilih</p>
                    <ul id="liveSummaryList" class="space-y-2 text-xs text-[#52525b]">
                        <li class="text-[#a1a1aa]">Belum ada layanan yang dipilih.</li>
                    </ul>
                </div>

                <div class="pt-4 border-t border-black/[0.06] flex items-baseline justify-between">
                    <span class="text-xs font-medium text-[#71717a]">Estimasi Total</span>
                    <span id="liveTotalText" class="font-mono-num text-2xl font-semibold tracking-[-0.03em] text-[#141416]">Rp 0</span>
                </div>

                <button type="button" id="review-order" onclick="showConfirmationModal()" class="btn-ink w-full !min-h-[48px]">
                    Pesan Sekarang
                </button>
            </aside>

            <!-- Native <dialog> Modal Konfirmasi Pesanan (modern-web-guidance) -->
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
    </main>

    <script>
        function stepQty(inputId, delta) {
            const el = document.getElementById(inputId);
            if (!el) return;
            const next = Math.max(0, (Number(el.value) || 0) + delta);
            el.value = String(next);
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
            document.querySelectorAll('.btn-category').forEach((btn) => {
                btn.className = 'btn-category px-4 py-1.5 rounded-full text-xs font-medium transition text-[#52525b] hover:text-[#141416]';
            });
            element.className = 'btn-category px-4 py-1.5 rounded-full text-xs font-medium transition bg-[#141416] text-white';

            const cards = document.querySelectorAll('.service-card');
            let visibleCount = 0;
            cards.forEach((card) => {
                const isVisible = category === 'all' || card.dataset.category === category;
                card.style.display = isVisible ? 'flex' : 'none';
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

        // Light-dismiss fallback untuk browser tanpa closedby
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
</body>
</html>
