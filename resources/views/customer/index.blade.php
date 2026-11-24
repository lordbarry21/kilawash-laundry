<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#f7f9fa">
    <title>Layanan Laundry — {{ config('app.name', 'KilaWash') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Host+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f7f9fa] text-[#1f3038] antialiased">
    <!-- Top Bar Navigasi Pelanggan -->
    <header class="bg-white border-b border-[#1f3038]/12 sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('customer.index') }}" class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-lg bg-[#0d7480] text-white inline-flex items-center justify-center">
                    <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="13" r="6"/>
                        <path d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/>
                        <line x1="7" y1="6" x2="7.01" y2="6"/>
                        <line x1="11" y1="6" x2="11.01" y2="6"/>
                    </svg>
                </span>
                <span class="leading-tight">
                    <span class="block text-base font-semibold tracking-tight text-[#1f3038]">{{ config('app.name', 'KilaWash') }}</span>
                    <span class="block text-[11px] text-[#51656f]">Layanan Laundry Kiloan &amp; Satuan</span>
                </span>
            </a>

            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ route('erd.index') }}" class="hidden sm:inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium text-[#51656f] hover:bg-[#ecf0f2] hover:text-[#1f3038] transition">
                    ERD Database
                </a>
                @guest
                    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-[#0d7480] hover:bg-[#0a5d67] text-white text-sm font-medium transition">
                        Masuk Petugas / Admin
                    </a>
                @else
                    <a href="{{ auth()->user()->can('operate-laundry') ? route('dashboard') : route('profile.edit') }}"
                       class="inline-flex items-center px-4 py-2 rounded-lg bg-[#0d7480] hover:bg-[#0a5d67] text-white text-sm font-medium transition">
                        {{ auth()->user()->can('operate-laundry') ? 'Dashboard Admin' : 'Akun Saya' }}
                    </a>
                @endguest
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-10 space-y-8">
        <!-- Editorial Hero Banner -->
        <section class="grid grid-cols-1 lg:grid-cols-12 bg-white border border-[#1f3038]/12 rounded-2xl overflow-hidden shadow-sm">
            <div class="lg:col-span-7 p-7 sm:p-10 flex flex-col justify-between gap-6">
                <div>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#e3f2f4] text-[#07474f] text-xs font-medium">
                        Kiloan, Satuan &amp; Express dalam 1 Pesanan
                    </span>
                    <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight text-[#1f3038] leading-tight mt-3">
                        Pesan layanan cuci bersih tanpa antre.
                    </h1>
                    <p class="text-sm sm:text-base text-[#51656f] mt-3 max-w-[48ch] leading-relaxed">
                        Pilih layanan reguler, express, atau satuan, isi perkiraan berat atau jumlah pakaian, lalu kirim pesanan dengan nomor WhatsApp Anda.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3 text-xs text-[#51656f]">
                    <span class="px-3 py-1.5 rounded-lg bg-[#f7f9fa] border border-[#1f3038]/10 font-mono-num">Reguler Rp 7.000/Kg</span>
                    <span class="px-3 py-1.5 rounded-lg bg-[#f7f9fa] border border-[#1f3038]/10 font-mono-num">Express Rp 12.000/Kg</span>
                    <span class="px-3 py-1.5 rounded-lg bg-[#f7f9fa] border border-[#1f3038]/10 font-mono-num">Superkilat Rp 25.000/Kg</span>
                </div>
            </div>

            <div class="lg:col-span-5 relative min-h-[220px] bg-[#ecf0f2] overflow-hidden order-first lg:order-last">
                <img src="{{ asset('images/laundry-banner.jpg') }}" alt="Tumpukan handuk dan pakaian bersih siap ambil" fetchpriority="high"
                     class="kw-hero-img w-full h-full object-cover">
            </div>
        </section>

        <!-- Flash Messages -->
        @if(session('success'))
            <div role="status" class="bg-[#dcfce7] border border-[#15803d]/25 text-[#14532d] px-4 py-3.5 rounded-xl font-medium text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div role="alert" class="bg-[#ffe4e6] border border-[#be123c]/25 text-[#881337] px-4 py-3.5 rounded-xl font-medium text-sm">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div role="alert" class="bg-[#ffe4e6] border border-[#be123c]/25 text-[#881337] px-4 py-3.5 rounded-xl text-sm">
                <ul class="list-disc list-inside space-y-1 font-medium">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Filter Kategori -->
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold tracking-tight text-[#1f3038]">Formulir Pendaftaran Cucian</h2>
            <div class="flex flex-wrap gap-2" role="group" aria-label="Filter kategori layanan">
                <button type="button" onclick="filterCategory('all', this)"
                        class="btn-category px-4 py-2 rounded-full text-sm font-medium transition bg-[#0d7480] text-white">
                    Semua Layanan
                </button>
                <button type="button" onclick="filterCategory('Kiloan', this)"
                        class="btn-category px-4 py-2 rounded-full text-sm font-medium transition bg-white text-[#1f3038] border border-[#1f3038]/20 hover:bg-[#ecf0f2]">
                    Kiloan
                </button>
                <button type="button" onclick="filterCategory('Satuan', this)"
                        class="btn-category px-4 py-2 rounded-full text-sm font-medium transition bg-white text-[#1f3038] border border-[#1f3038]/20 hover:bg-[#ecf0f2]">
                    Satuan
                </button>
                <button type="button" onclick="filterCategory('Express', this)"
                        class="btn-category px-4 py-2 rounded-full text-sm font-medium transition bg-white text-[#1f3038] border border-[#1f3038]/20 hover:bg-[#ecf0f2]">
                    Express
                </button>
            </div>
        </div>

        <form id="orderForm" action="{{ route('customer.checkout') }}" method="POST" class="space-y-6">
            @csrf

            <!-- 1. Informasi Pemesan -->
            <section class="bg-white p-6 rounded-2xl border border-[#1f3038]/12 shadow-sm">
                <h3 class="text-base font-semibold text-[#1f3038] pb-3 mb-4 border-b border-[#1f3038]/10">1. Informasi Pemesan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-[#1f3038] mb-1.5">Nama Lengkap</label>
                        <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}"
                               maxlength="255" required autocomplete="name" placeholder="Contoh: Dina Putri"
                               class="w-full min-h-[44px] border border-[#1f3038]/25 rounded-lg px-3.5 py-2.5 text-[#1f3038] focus:border-[#0d7480] focus:ring-2 focus:ring-[#0d7480]/20 focus:outline-none">
                    </div>
                    <div>
                        <label for="whatsapp_number" class="block text-sm font-medium text-[#1f3038] mb-1.5">Nomor WhatsApp</label>
                        <input type="tel" id="whatsapp_number" name="whatsapp_number" required maxlength="16"
                               pattern="\+?[0-9]{8,15}" inputmode="tel" autocomplete="tel" value="{{ old('whatsapp_number') }}"
                               placeholder="Contoh: 081234567890"
                               class="w-full min-h-[44px] font-mono-num border border-[#1f3038]/25 rounded-lg px-3.5 py-2.5 text-[#1f3038] focus:border-[#0d7480] focus:ring-2 focus:ring-[#0d7480]/20 focus:outline-none">
                    </div>
                </div>
            </section>

            <!-- 2. Pilih Layanan -->
            <section class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h3 class="text-base font-semibold text-[#1f3038]">2. Pilih Layanan &amp; Jumlah</h3>
                    <p class="text-sm text-[#51656f]">Estimasi Total: <strong id="liveTotalText" class="font-mono-num text-[#07474f] text-base">Rp 0</strong></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @forelse($services as $service)
                        <article class="service-card bg-white rounded-2xl border border-[#1f3038]/12 shadow-sm overflow-hidden flex flex-col justify-between transition hover:-translate-y-0.5 hover:shadow-md"
                                 data-category="{{ $service->category }}">
                            <div class="p-5 space-y-2.5">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#e3f2f4] text-[#07474f]">
                                        {{ $service->category }}
                                    </span>
                                    <span class="font-mono-num font-semibold text-sm text-[#0d7480]">
                                        Rp {{ number_format($service->price_per_unit, 0, ',', '.') }} / {{ $service->unit_type }}
                                    </span>
                                </div>
                                <h4 class="font-semibold text-[#1f3038] text-base leading-snug">{{ $service->service_name }}</h4>
                                <p class="text-xs text-[#51656f] leading-relaxed">{{ $service->description }}</p>
                            </div>

                            <div class="p-4 bg-[#f7f9fa] border-t border-[#1f3038]/10">
                                <label for="service-{{ $service->id }}" class="block text-xs font-medium text-[#51656f] mb-1.5">
                                    {{ $service->unit_type === 'Kg' ? 'Perkiraan berat (Kg)' : 'Jumlah (Pcs)' }}
                                </label>
                                <input type="number" id="service-{{ $service->id }}" name="items[{{ $service->id }}]"
                                       min="0" step="1" value="{{ old('items.'.$service->id, 0) }}"
                                       data-unit="{{ $service->unit_type }}"
                                       data-name="{{ $service->service_name }}"
                                       data-price="{{ $service->price_per_unit }}"
                                       oninput="updateLiveTotal()"
                                       class="item-qty w-full min-h-[42px] font-mono-num border border-[#1f3038]/25 bg-white rounded-lg px-3 py-1.5 text-center font-semibold text-[#1f3038] focus:border-[#0d7480] focus:ring-2 focus:ring-[#0d7480]/20 focus:outline-none">
                            </div>
                        </article>
                    @empty
                        <div class="md:col-span-3 bg-white p-8 rounded-2xl border border-[#1f3038]/12 text-center text-[#51656f]">
                            Belum ada layanan laundry yang tersedia.
                        </div>
                    @endforelse
                </div>

                <p id="emptyCategoryMessage" class="hidden text-center text-[#51656f] bg-white p-6 rounded-2xl border border-[#1f3038]/12">
                    Belum ada layanan pada kategori ini.
                </p>
            </section>

            <div class="flex justify-end pt-2">
                <button type="button" id="review-order" onclick="showConfirmationModal()"
                        class="min-h-[48px] px-8 py-3 rounded-xl bg-[#0d7480] hover:bg-[#0a5d67] text-white font-semibold text-sm shadow-sm transition active:scale-[0.985]">
                    Pesan Sekarang
                </button>
            </div>

            <!-- Modal Konfirmasi Pesanan (Native <dialog> sesuai modern-web-guidance) -->
            <dialog id="confirmModal" class="kw-modal" closedby="any" aria-labelledby="confirm-title">
                <div class="flex items-center justify-between px-6 py-4 border-b border-[#1f3038]/12">
                    <h1 id="confirm-title" class="text-lg font-semibold text-[#1f3038]">Konfirmasi Pesanan</h1>
                    <button type="button" onclick="closeConfirmationModal()" aria-label="Tutup konfirmasi"
                            class="w-9 h-9 rounded-lg inline-flex items-center justify-center text-[#51656f] hover:bg-[#ecf0f2]">&times;</button>
                </div>

                <div class="p-6 space-y-4 overflow-y-auto">
                    <div class="space-y-1.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-[#51656f]">Nama Pemesan</span>
                            <strong id="modalName" class="text-[#1f3038]"></strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#51656f]">WhatsApp</span>
                            <strong id="modalWhatsapp" class="font-mono-num text-[#1f3038]"></strong>
                        </div>
                    </div>

                    <div class="border-t border-b border-[#1f3038]/12 py-3.5 max-h-48 overflow-y-auto">
                        <p class="text-xs font-semibold text-[#51656f] uppercase tracking-wider mb-2">Rincian Item</p>
                        <ul id="modalItemList" class="space-y-2 text-sm text-[#1f3038]"></ul>
                    </div>

                    <div class="flex justify-between items-center pt-1">
                        <span class="font-semibold text-[#1f3038]">Total Pembayaran</span>
                        <span id="modalTotalPrice" class="font-mono-num text-xl font-semibold text-[#0d7480]">Rp 0</span>
                    </div>
                </div>

                <div class="px-6 py-4 bg-[#f7f9fa] border-t border-[#1f3038]/12 flex gap-3">
                    <button type="button" id="cancel-confirmation" onclick="closeConfirmationModal()" autofocus
                            class="w-1/2 min-h-[44px] rounded-lg border border-[#1f3038]/25 bg-white hover:bg-[#ecf0f2] text-[#1f3038] font-medium text-sm transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="w-1/2 min-h-[44px] rounded-lg bg-[#0d7480] hover:bg-[#0a5d67] text-white font-semibold text-sm transition">
                        Ya, Kirim Pesanan
                    </button>
                </div>
            </dialog>
        </form>
    </main>

    <script>
        function updateLiveTotal() {
            let total = 0;
            document.querySelectorAll('.item-qty').forEach((input) => {
                const qty = Math.max(0, Number(input.value) || 0);
                total += qty * Number(input.dataset.price || 0);
            });
            document.getElementById('liveTotalText').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        function filterCategory(category, element) {
            document.querySelectorAll('.btn-category').forEach((btn) => {
                btn.className = 'btn-category px-4 py-2 rounded-full text-sm font-medium transition bg-white text-[#1f3038] border border-[#1f3038]/20 hover:bg-[#ecf0f2]';
            });
            element.className = 'btn-category px-4 py-2 rounded-full text-sm font-medium transition bg-[#0d7480] text-white';

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

        // Light-dismiss fallback untuk Safari yang belum mendukung closedby
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

        updateLiveTotal();
    </script>
</body>
</html>
