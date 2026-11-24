<x-guest-layout>
    <div class="mb-7">
        <h2 class="text-[26px] font-semibold tracking-[-0.03em] text-[#141416]">Buat akun baru</h2>
        <p class="text-sm text-[#71717a] mt-1">Daftar untuk memesan layanan kilawash dengan lebih cepat.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-xs font-medium text-[#52525b] mb-1.5">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   placeholder="Contoh: Bari Achmad"
                   class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-black/15 bg-white text-sm text-[#141416] focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/15 focus:outline-none">
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        <div>
            <label for="email" class="block text-xs font-medium text-[#52525b] mb-1.5">Alamat Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" spellcheck="false"
                   placeholder="nama@email.com"
                   class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-black/15 bg-white text-sm text-[#141416] focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/15 focus:outline-none">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label for="password" class="block text-xs font-medium text-[#52525b] mb-1.5">Kata Sandi</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                       placeholder="Min. 8 karakter"
                       class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-black/15 bg-white text-sm text-[#141416] focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/15 focus:outline-none">
                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-medium text-[#52525b] mb-1.5">Ulangi Sandi</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                       placeholder="Ketik ulang sandi"
                       class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-black/15 bg-white text-sm text-[#141416] focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/15 focus:outline-none">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
            </div>
        </div>

        <button type="submit" class="btn-ink w-full mt-2">
            Daftar Akun
        </button>
    </form>

    <p class="mt-5 text-center text-xs text-[#71717a]">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="text-[#141416] font-semibold underline ml-0.5">Masuk di sini</a>
    </p>
</x-guest-layout>
