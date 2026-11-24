<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-semibold tracking-tight text-[#1f3038]">Buat Akun Baru</h2>
        <p class="text-sm text-[#51656f] mt-1">Daftarkan akun pelanggan untuk memesan layanan {{ config('app.name', 'KilaWash') }}.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-[#1f3038] mb-1.5">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   placeholder="Contoh: Bari Achmad"
                   class="w-full min-h-[44px] px-3.5 py-2.5 rounded-lg border border-[#1f3038]/25 bg-white text-[#1f3038] focus:border-[#0d7480] focus:ring-2 focus:ring-[#0d7480]/20 focus:outline-none">
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-[#1f3038] mb-1.5">Alamat Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" spellcheck="false"
                   placeholder="nama@email.com"
                   class="w-full min-h-[44px] px-3.5 py-2.5 rounded-lg border border-[#1f3038]/25 bg-white text-[#1f3038] focus:border-[#0d7480] focus:ring-2 focus:ring-[#0d7480]/20 focus:outline-none">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label for="password" class="block text-sm font-medium text-[#1f3038] mb-1.5">Kata Sandi</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                       placeholder="Min. 8 karakter"
                       class="w-full min-h-[44px] px-3.5 py-2.5 rounded-lg border border-[#1f3038]/25 bg-white text-[#1f3038] focus:border-[#0d7480] focus:ring-2 focus:ring-[#0d7480]/20 focus:outline-none">
                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-[#1f3038] mb-1.5">Ulangi Sandi</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                       placeholder="Ketik ulang sandi"
                       class="w-full min-h-[44px] px-3.5 py-2.5 rounded-lg border border-[#1f3038]/25 bg-white text-[#1f3038] focus:border-[#0d7480] focus:ring-2 focus:ring-[#0d7480]/20 focus:outline-none">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
            </div>
        </div>

        <button type="submit"
                class="w-full min-h-[44px] px-5 py-2.5 rounded-lg bg-[#0d7480] hover:bg-[#0a5d67] text-white font-medium text-sm transition active:scale-[0.99]">
            Daftar Akun
        </button>
    </form>

    <p class="mt-5 text-center text-sm text-[#51656f]">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="text-[#0d7480] font-medium underline">Masuk di sini</a>
    </p>
</x-guest-layout>
