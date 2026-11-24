<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk Pesanan #{{ $order->id }} — {{ config('app.name', 'KilaWash') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Host+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f7f9fa] text-[#1f3038] antialiased min-h-screen py-10 px-4">
    <main class="max-w-md mx-auto space-y-4">
        <div class="flex justify-center gap-2 no-print">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg border border-[#1f3038]/20 bg-white text-sm font-medium text-[#1f3038]">Kembali ke Dashboard</a>
            <button type="button" onclick="window.print()" class="px-4 py-2 rounded-lg bg-[#0d7480] hover:bg-[#0a5d67] text-white text-sm font-medium">Cetak Struk</button>
        </div>

        <article class="bg-white border border-[#1f3038]/15 rounded-2xl p-6 shadow-sm space-y-5">
            <header class="text-center pb-4 border-b border-dashed border-[#1f3038]/25">
                <h1 class="text-xl font-semibold text-[#1f3038]">{{ config('app.name', 'KilaWash') }}</h1>
                <p class="text-xs text-[#51656f] mt-1">Jl. Melati Raya, Kecamatan Sukmajaya</p>
                <p class="font-mono-num text-sm font-semibold text-[#0d7480] mt-2">ORDER #{{ str_pad((string) $order->id, 4, '0', STR_PAD_LEFT) }}</p>
            </header>

            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-[#51656f]">Pelanggan</span>
                    <strong>{{ $order->customer_name }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#51656f]">WhatsApp</span>
                    <span class="font-mono-num">{{ $order->whatsapp_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#51656f]">Tanggal Masuk</span>
                    <span>{{ $order->created_at?->format('d M Y, H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#51656f]">Status</span>
                    <strong class="text-[#0d7480]">{{ $order->status }}</strong>
                </div>
            </div>

            <div class="pt-4 border-t border-dashed border-[#1f3038]/25 space-y-2.5 text-sm">
                <p class="text-xs font-semibold text-[#51656f] uppercase">Rincian Layanan</p>
                @foreach($order->orderDetails as $detail)
                    <div class="flex justify-between gap-2">
                        <div>
                            <div class="font-medium text-[#1f3038]">{{ $detail->service->service_name ?? 'Layanan' }}</div>
                            <div class="text-xs text-[#51656f] font-mono-num">
                                {{ $detail->qty_or_weight }} {{ $detail->service->unit_type ?? '' }} × Rp {{ number_format($detail->service->price_per_unit ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                        <span class="font-mono-num font-semibold shrink-0">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            <div class="pt-4 border-t border-dashed border-[#1f3038]/25 flex justify-between items-center text-base font-semibold">
                <span>Total Pembayaran</span>
                <span class="font-mono-num text-lg text-[#0d7480]">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>

            <p class="text-center text-xs text-[#51656f]">Terima kasih telah mempercayakan cucian Anda kepada {{ config('app.name', 'KilaWash') }}.</p>
        </article>
    </main>
</body>
</html>
