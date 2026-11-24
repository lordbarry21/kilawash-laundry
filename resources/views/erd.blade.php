<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f9fa">
    <title>ERD &amp; Laravel Migrations — {{ config('app.name', 'KilaWash') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Host+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .erd-stage { position: relative; min-width: 960px; display: grid; grid-template-columns: 1fr 1.15fr 1fr; gap: 52px; align-items: start; }
        .erd-lines { position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1; }
        .erd-card { position: relative; z-index: 2; background: #fff; border: 1.5px solid rgba(31,48,56,.2); border-radius: 12px; overflow: hidden; transition: border-color 150ms, box-shadow 150ms; }
        .erd-card.is-highlighted { border-color: #0d7480; box-shadow: 0 0 0 3px rgba(13,116,128,.16); }
    </style>
</head>
<body class="bg-[#f7f9fa] text-[#1f3038] antialiased min-h-screen">
    <header class="bg-white border-b border-[#1f3038]/12 sticky top-0 z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('customer.index') }}" class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-lg bg-[#0d7480] text-white inline-flex items-center justify-center">
                    <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="6"/><path d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/><line x1="7" y1="6" x2="7.01" y2="6"/><line x1="11" y1="6" x2="11.01" y2="6"/></svg>
                </span>
                <span class="leading-tight">
                    <span class="block text-base font-semibold text-[#1f3038]">{{ config('app.name', 'KilaWash') }} — ERD &amp; Laravel Migrations</span>
                    <span class="block text-xs text-[#51656f]">Bari Hartanto Achmad • XII RPL 1 • db_laundry_express</span>
                </span>
            </a>

            <div class="flex items-center gap-2">
                <a href="{{ route('customer.index') }}" class="px-3.5 py-2 rounded-lg bg-[#0d7480] hover:bg-[#0a5d67] text-white text-sm font-medium">Buka Aplikasi</a>
                <a href="https://github.com/lordbarry21/kilawash-laundry" target="_blank" rel="noopener" class="px-3.5 py-2 rounded-lg border border-[#1f3038]/20 bg-white text-sm font-medium text-[#1f3038]">GitHub Repo</a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-[#1f3038]">Entity Relationship Diagram (ERD) &amp; Laravel Migrations</h1>
                <p class="text-sm text-[#51656f] mt-1">Database MySQL <code>db_laundry_express</code> dengan 4 tabel utama: <code>users</code>, <code>services</code>, <code>orders</code>, dan <code>order_details</code>.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="px-3 py-1 rounded-full bg-[#e3f2f4] text-[#07474f] text-xs font-medium">Laravel 13 Eloquent ORM</span>
                <span class="px-3 py-1 rounded-full bg-[#e0f2fe] text-[#0369a1] text-xs font-medium">Relasi 1:N &amp; N:M via order_details</span>
            </div>
        </div>

        <!-- 1. Visual Interactive SVG ERD -->
        <section class="bg-white border border-[#1f3038]/12 rounded-2xl p-6 shadow-sm overflow-x-auto">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-5">
                <div>
                    <h2 class="text-base font-semibold text-[#1f3038]">Diagram Relasi Tabel Interaktif</h2>
                    <p class="text-xs text-[#51656f]">Arahkan kursor pada <code>order_id</code> atau <code>service_id</code> di tabel tengah untuk menyorot tabel induknya.</p>
                </div>
                <span class="font-mono-num text-xs text-[#51656f]">orders (1) ──&lt; order_details (N) &gt;── services (1)</span>
            </div>

            <div class="erd-stage" id="erd-stage">
                <svg class="erd-lines" id="erd-svg" aria-hidden="true"></svg>

                <!-- Left Column: orders + users -->
                <div class="space-y-6">
                    <article class="erd-card" id="tbl-orders">
                        <header class="bg-[#1f3038] text-white px-4 py-3 flex justify-between items-center">
                            <span class="font-mono-num text-sm font-semibold">orders</span>
                            <span class="text-xs text-white/75">Pesanan Pelanggan</span>
                        </header>
                        <ul class="divide-y divide-[#1f3038]/10 text-xs">
                            <li id="col-orders-id" class="px-3.5 py-2.5 flex justify-between bg-[#f0f9fa]">
                                <span><strong class="px-1.5 py-0.5 rounded bg-[#e3f2f4] text-[#07474f] font-mono-num mr-1.5">PK</strong><strong class="font-mono-num">id</strong></span>
                                <span class="font-mono-num text-[#51656f]">BIGINT AI</span>
                            </li>
                            <li class="px-3.5 py-2 flex justify-between"><span class="font-mono-num">customer_name</span><span class="font-mono-num text-[#51656f]">VARCHAR(255)</span></li>
                            <li class="px-3.5 py-2 flex justify-between"><span class="font-mono-num">whatsapp_number</span><span class="font-mono-num text-[#51656f]">VARCHAR(255)</span></li>
                            <li class="px-3.5 py-2 flex justify-between"><span class="font-mono-num">total_price</span><span class="font-mono-num text-[#51656f]">INT</span></li>
                            <li class="px-3.5 py-2 flex justify-between"><span class="font-mono-num">status</span><span class="font-mono-num text-[#51656f]">ENUM(Antrean,Dicuci,Selesai)</span></li>
                            <li class="px-3.5 py-2 flex justify-between"><span class="font-mono-num">timestamps()</span><span class="font-mono-num text-[#51656f]">created_at / updated_at</span></li>
                        </ul>
                    </article>

                    <article class="erd-card" id="tbl-users">
                        <header class="bg-[#1f3038] text-white px-4 py-3 flex justify-between items-center">
                            <span class="font-mono-num text-sm font-semibold">users</span>
                            <span class="text-xs text-white/75">Admin, Kasir &amp; Akun</span>
                        </header>
                        <ul class="divide-y divide-[#1f3038]/10 text-xs">
                            <li class="px-3.5 py-2 flex justify-between bg-[#f0f9fa]">
                                <span><strong class="px-1.5 py-0.5 rounded bg-[#e3f2f4] text-[#07474f] font-mono-num mr-1.5">PK</strong><strong class="font-mono-num">id</strong></span>
                                <span class="font-mono-num text-[#51656f]">BIGINT AI</span>
                            </li>
                            <li class="px-3.5 py-2 flex justify-between"><span class="font-mono-num">name / email (UK)</span><span class="font-mono-num text-[#51656f]">VARCHAR(255)</span></li>
                            <li class="px-3.5 py-2 flex justify-between"><span class="font-mono-num">password</span><span class="font-mono-num text-[#51656f]">BCRYPT HASH</span></li>
                            <li class="px-3.5 py-2 flex justify-between"><span class="font-mono-num">is_admin</span><span class="font-mono-num text-[#0d7480]">BOOLEAN (Gate)</span></li>
                            <li class="px-3.5 py-2 flex justify-between"><span class="font-mono-num">role</span><span class="font-mono-num text-[#51656f]">ENUM(admin,kasir,customer)</span></li>
                            <li class="px-3.5 py-2 flex justify-between"><span class="font-mono-num">avatar</span><span class="font-mono-num text-[#51656f]">MEDIUMTEXT NULL</span></li>
                        </ul>
                    </article>
                </div>

                <!-- Center Column: order_details (Pivot Relasional) -->
                <article class="erd-card" id="tbl-details">
                    <header class="bg-[#0d7480] text-white px-4 py-3 flex justify-between items-center">
                        <span class="font-mono-num text-sm font-semibold">order_details</span>
                        <span class="text-xs text-white/85">Rincian Item &amp; Relasi FK</span>
                    </header>
                    <ul class="divide-y divide-[#1f3038]/10 text-xs">
                        <li class="px-3.5 py-2.5 flex justify-between bg-[#f0f9fa]">
                            <span><strong class="px-1.5 py-0.5 rounded bg-[#e3f2f4] text-[#07474f] font-mono-num mr-1.5">PK</strong><strong class="font-mono-num">id</strong></span>
                            <span class="font-mono-num text-[#51656f]">BIGINT AI</span>
                        </li>
                        <li id="col-det-order" data-rel-target="tbl-orders" class="px-3.5 py-2.5 flex justify-between bg-[#fffbeb] cursor-pointer">
                            <span><strong class="px-1.5 py-0.5 rounded bg-[#fef3c7] text-[#92400e] font-mono-num mr-1.5">FK</strong><strong class="font-mono-num">order_id</strong></span>
                            <span class="font-mono-num text-[#92400e]">→ orders.id (CASCADE)</span>
                        </li>
                        <li id="col-det-service" data-rel-target="tbl-services" class="px-3.5 py-2.5 flex justify-between bg-[#fffbeb] cursor-pointer">
                            <span><strong class="px-1.5 py-0.5 rounded bg-[#fef3c7] text-[#92400e] font-mono-num mr-1.5">FK</strong><strong class="font-mono-num">service_id</strong></span>
                            <span class="font-mono-num text-[#92400e]">→ services.id (CASCADE)</span>
                        </li>
                        <li class="px-3.5 py-2.5 flex justify-between"><span class="font-mono-num">qty_or_weight</span><span class="font-mono-num text-[#51656f]">INT</span></li>
                        <li class="px-3.5 py-2.5 flex justify-between"><span class="font-mono-num">subtotal</span><span class="font-mono-num text-[#51656f]">INT</span></li>
                        <li class="px-3.5 py-2.5 flex justify-between"><span class="font-mono-num">timestamps()</span><span class="font-mono-num text-[#51656f]">created_at / updated_at</span></li>
                    </ul>
                </article>

                <!-- Right Column: services -->
                <article class="erd-card" id="tbl-services">
                    <header class="bg-[#1f3038] text-white px-4 py-3 flex justify-between items-center">
                        <span class="font-mono-num text-sm font-semibold">services</span>
                        <span class="text-xs text-white/75">Master Katalog Tarif</span>
                    </header>
                    <ul class="divide-y divide-[#1f3038]/10 text-xs">
                        <li id="col-services-id" class="px-3.5 py-2.5 flex justify-between bg-[#f0f9fa]">
                            <span><strong class="px-1.5 py-0.5 rounded bg-[#e3f2f4] text-[#07474f] font-mono-num mr-1.5">PK</strong><strong class="font-mono-num">id</strong></span>
                            <span class="font-mono-num text-[#51656f]">BIGINT AI</span>
                        </li>
                        <li class="px-3.5 py-2 flex justify-between"><span class="font-mono-num">service_name</span><span class="font-mono-num text-[#51656f]">VARCHAR(255)</span></li>
                        <li class="px-3.5 py-2 flex justify-between"><span class="font-mono-num">category</span><span class="font-mono-num text-[#51656f]">ENUM(Kiloan,Satuan,Express)</span></li>
                        <li class="px-3.5 py-2 flex justify-between"><span class="font-mono-num">price_per_unit</span><span class="font-mono-num text-[#51656f]">INT</span></li>
                        <li class="px-3.5 py-2 flex justify-between"><span class="font-mono-num">unit_type</span><span class="font-mono-num text-[#51656f]">ENUM(Kg,Pcs)</span></li>
                        <li class="px-3.5 py-2 flex justify-between"><span class="font-mono-num">description</span><span class="font-mono-num text-[#51656f]">TEXT NULL</span></li>
                        <li class="px-3.5 py-2 flex justify-between"><span class="font-mono-num">timestamps()</span><span class="font-mono-num text-[#51656f]">created_at / updated_at</span></li>
                    </ul>
                </article>
            </div>
        </section>

        <!-- 2. Kode Lengkap Seluruh File Laravel Migration (Utuh Tanpa Terpotong) -->
        <section class="space-y-4">
            <h2 class="text-xl font-semibold tracking-tight text-[#1f3038]">Kode Lengkap Laravel Migrations (<code>database/migrations/</code>)</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                @foreach($migrations as $m)
                    <article class="bg-white border border-[#1f3038]/12 rounded-2xl overflow-hidden shadow-sm">
                        <header class="px-5 py-3.5 bg-[#ecf0f2] border-b border-[#1f3038]/10 flex items-center justify-between gap-2">
                            <span class="font-mono-num text-xs font-semibold text-[#1f3038] truncate">{{ $m['name'] }}</span>
                            <span class="px-2 py-0.5 rounded-full bg-[#e3f2f4] text-[#07474f] text-[11px] font-medium shrink-0">Migration</span>
                        </header>
                        <pre class="p-4 bg-[#16232a] text-[#e6eff2] font-mono-num text-xs leading-relaxed overflow-x-auto">{{ $m['code'] }}</pre>
                    </article>
                @endforeach
            </div>
        </section>
    </main>

    <script>
        function drawErdLines() {
            const stage = document.getElementById('erd-stage');
            const svg = document.getElementById('erd-svg');
            if (!stage || !svg) return;

            const sRect = stage.getBoundingClientRect();
            svg.setAttribute('viewBox', `0 0 ${sRect.width} ${sRect.height}`);
            svg.innerHTML = '';

            const connect = (fromId, toId, color, labelLeft, labelRight) => {
                const a = document.getElementById(fromId);
                const b = document.getElementById(toId);
                if (!a || !b) return;
                const ra = a.getBoundingClientRect();
                const rb = b.getBoundingClientRect();
                const fromRight = ra.left < rb.left;
                const x1 = (fromRight ? ra.right : ra.left) - sRect.left;
                const y1 = (ra.top + ra.height / 2) - sRect.top;
                const x2 = (fromRight ? rb.left : rb.right) - sRect.left;
                const y2 = (rb.top + rb.height / 2) - sRect.top;
                const cx = (x1 + x2) / 2;

                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.setAttribute('d', `M ${x1} ${y1} C ${cx} ${y1}, ${cx} ${y2}, ${x2} ${y2}`);
                path.setAttribute('fill', 'none');
                path.setAttribute('stroke', color);
                path.setAttribute('stroke-width', '2.4');
                svg.appendChild(path);

                for (const [cxDot, cyDot, txt, dx] of [
                    [x1, y1, labelLeft, fromRight ? 10 : -16],
                    [x2, y2, labelRight, fromRight ? -18 : 10],
                ]) {
                    const dot = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    dot.setAttribute('cx', cxDot);
                    dot.setAttribute('cy', cyDot);
                    dot.setAttribute('r', '4');
                    dot.setAttribute('fill', color);
                    svg.appendChild(dot);

                    const t = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                    t.setAttribute('x', cxDot + dx);
                    t.setAttribute('y', cyDot - 8);
                    t.setAttribute('fill', color);
                    t.setAttribute('font-size', '11');
                    t.setAttribute('font-weight', '700');
                    t.setAttribute('font-family', 'JetBrains Mono, monospace');
                    t.textContent = txt;
                    svg.appendChild(t);
                }
            };

            connect('col-orders-id', 'col-det-order', '#0d7480', '1', 'N');
            connect('col-services-id', 'col-det-service', '#b45309', '1', 'N');
        }

        window.addEventListener('load', drawErdLines);
        window.addEventListener('resize', drawErdLines);

        document.querySelectorAll('[data-rel-target]').forEach((el) => {
            const target = document.getElementById(el.dataset.relTarget);
            if (!target) return;
            el.addEventListener('mouseenter', () => target.classList.add('is-highlighted'));
            el.addEventListener('mouseleave', () => target.classList.remove('is-highlighted'));
        });
    </script>
</body>
</html>
