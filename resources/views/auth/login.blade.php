<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-semibold tracking-tight text-[#1f3038]">Masuk ke {{ config('app.name', 'KilaWash') }}</h2>
        <p class="text-sm text-[#51656f] mt-1">Gunakan akun Admin, Kasir, atau Pelanggan Anda.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-[#1f3038] mb-1.5">Alamat Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" spellcheck="false" enterkeyhint="next"
                   placeholder="admin@gmail.com"
                   class="w-full min-h-[44px] px-3.5 py-2.5 rounded-lg border border-[#1f3038]/25 bg-white text-[#1f3038] focus:border-[#0d7480] focus:ring-2 focus:ring-[#0d7480]/20 focus:outline-none">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-[#1f3038] mb-1.5">Kata Sandi</label>
            <div class="relative">
                <input id="password" type="password" name="password" required autocomplete="current-password" enterkeyhint="done"
                       placeholder="Masukkan kata sandi"
                       class="w-full min-h-[44px] px-3.5 py-2.5 pr-12 rounded-lg border border-[#1f3038]/25 bg-white text-[#1f3038] focus:border-[#0d7480] focus:ring-2 focus:ring-[#0d7480]/20 focus:outline-none">
                <button type="button" id="toggle-pwd" aria-label="Tampilkan kata sandi"
                        onclick="const p=document.getElementById('password');p.type=p.type==='password'?'text':'password';"
                        class="absolute right-2 top-1/2 -translate-y-1/2 w-9 h-9 rounded-md inline-flex items-center justify-center text-[#51656f] hover:bg-[#ecf0f2]">
                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div class="flex items-center justify-between text-sm">
            <label for="remember_me" class="inline-flex items-center gap-2 text-[#51656f]">
                <input id="remember_me" type="checkbox" name="remember" class="rounded border-[#1f3038]/30 text-[#0d7480] focus:ring-[#0d7480]">
                <span>Ingat saya</span>
            </label>
            <a href="{{ route('customer.index') }}" class="text-[#0d7480] hover:underline font-medium">Kembali ke Katalog</a>
        </div>

        <button type="submit"
                class="w-full min-h-[44px] px-5 py-2.5 rounded-lg bg-[#0d7480] hover:bg-[#0a5d67] text-white font-medium text-sm transition active:scale-[0.99]">
            Masuk ke Sistem
        </button>
    </form>

    <p class="mt-4 text-center text-sm text-[#51656f]">
        Belum punya akun?
        <a href="{{ route('register') }}" class="text-[#0d7480] font-medium underline">Daftar sekarang</a>
    </p>

    <!-- Quick-Fill Akun Demo untuk Presentasi -->
    <div class="mt-5 pt-5 border-t border-[#1f3038]/12">
        <p class="text-xs font-medium text-[#51656f] mb-2.5">Akun Uji Coba (Klik untuk isi otomatis)</p>
        <div class="grid grid-cols-2 gap-2.5">
            <button type="button"
                    onclick="document.getElementById('email').value='admin@gmail.com';document.getElementById('password').value='password123';"
                    class="text-left p-2.5 rounded-lg border border-[#1f3038]/15 bg-[#f7f9fa] hover:border-[#0d7480] hover:bg-[#e3f2f4] transition">
                <span class="block text-xs font-semibold text-[#1f3038]">Admin (Full Akses)</span>
                <span class="block font-mono-num text-[11px] text-[#51656f] mt-0.5 truncate">admin@gmail.com</span>
            </button>
            <button type="button"
                    onclick="document.getElementById('email').value='kasir@gmail.com';document.getElementById('password').value='password123';"
                    class="text-left p-2.5 rounded-lg border border-[#1f3038]/15 bg-[#f7f9fa] hover:border-[#0d7480] hover:bg-[#e3f2f4] transition">
                <span class="block text-xs font-semibold text-[#1f3038]">Kasir (Operasional)</span>
                <span class="block font-mono-num text-[11px] text-[#51656f] mt-0.5 truncate">kasir@gmail.com</span>
            </button>
        </div>
    </div>
</x-guest-layout>
