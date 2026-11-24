<x-guest-layout>
    <div class="mb-7">
        <h2 class="text-[26px] font-semibold tracking-[-0.03em] text-[#141416]">Masuk ke akun</h2>
        <p class="text-sm text-[#71717a] mt-1">Gunakan email Admin, Kasir, atau Pelanggan Anda.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-xs font-medium text-[#52525b] mb-1.5">Alamat Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" spellcheck="false" enterkeyhint="next"
                   placeholder="admin@gmail.com"
                   class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-black/15 bg-white text-sm text-[#141416] focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/15 focus:outline-none">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <label for="password" class="block text-xs font-medium text-[#52525b] mb-1.5">Kata Sandi</label>
            <div class="relative">
                <input id="password" type="password" name="password" required autocomplete="current-password" enterkeyhint="done"
                       placeholder="Masukkan kata sandi"
                       class="w-full min-h-[44px] px-3.5 py-2.5 pr-11 rounded-xl border border-black/15 bg-white text-sm text-[#141416] focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/15 focus:outline-none">
                <button type="button" aria-label="Tampilkan kata sandi"
                        onclick="const p=document.getElementById('password');p.type=p.type==='password'?'text':'password';"
                        class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-lg inline-flex items-center justify-center text-[#71717a] hover:bg-[#f4f4f6]">
                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div class="flex items-center justify-between text-xs pt-0.5">
            <label for="remember_me" class="inline-flex items-center gap-2 text-[#52525b]">
                <input id="remember_me" type="checkbox" name="remember" class="rounded border-black/25 text-[#141416] focus:ring-[#2563eb]">
                <span>Ingat saya</span>
            </label>
            <a href="{{ route('customer.index') }}" class="text-[#141416] hover:underline font-medium">Kembali ke Pemesanan</a>
        </div>

        <button type="submit" class="btn-ink w-full mt-2">
            Masuk ke Sistem
        </button>
    </form>

    <p class="mt-5 text-center text-xs text-[#71717a]">
        Belum punya akun?
        <a href="{{ route('register') }}" class="text-[#141416] font-semibold underline ml-0.5">Daftar sekarang</a>
    </p>

    <div class="mt-6 pt-5 border-t border-black/[0.07]">
        <p class="text-[11px] font-medium text-[#71717a] mb-2.5">Akun Uji Coba (Klik untuk isi cepat)</p>
        <div class="grid grid-cols-2 gap-2.5">
            <button type="button"
                    onclick="document.getElementById('email').value='admin@gmail.com';document.getElementById('password').value='password123';"
                    class="text-left p-3 rounded-xl border border-black/[0.08] bg-[#f4f4f6]/70 hover:bg-[#f4f4f6] hover:border-black/20 transition">
                <span class="block text-xs font-semibold text-[#141416]">Admin</span>
                <span class="block font-mono-num text-[11px] text-[#71717a] mt-0.5 truncate">admin@gmail.com</span>
            </button>
            <button type="button"
                    onclick="document.getElementById('email').value='kasir@gmail.com';document.getElementById('password').value='password123';"
                    class="text-left p-3 rounded-xl border border-black/[0.08] bg-[#f4f4f6]/70 hover:bg-[#f4f4f6] hover:border-black/20 transition">
                <span class="block text-xs font-semibold text-[#141416]">Kasir</span>
                <span class="block font-mono-num text-[11px] text-[#71717a] mt-0.5 truncate">kasir@gmail.com</span>
            </button>
        </div>
    </div>
</x-guest-layout>
