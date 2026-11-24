<section>
    <header>
        <h2 class="text-lg font-semibold text-[#1f3038]">
            Foto &amp; Informasi Profil
        </h2>
        <p class="mt-1 text-sm text-[#51656f]">
            Perbarui foto profil, nama tampilan, dan alamat email akun Anda.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <!-- Foto Profil -->
        <div class="flex flex-wrap items-center gap-4">
            <div id="avatar-preview-box" class="w-20 h-20 rounded-xl bg-[#e3f2f4] text-[#07474f] border border-[#1f3038]/12 flex items-center justify-center text-2xl font-semibold overflow-hidden shrink-0">
                @if($user->avatar)
                    <img src="{{ $user->avatar }}" alt="Foto profil {{ $user->name }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                @endif
            </div>
            <div class="flex-1 min-w-[220px]">
                <label for="avatar" class="block text-sm font-medium text-[#1f3038] mb-1">Foto Profil (JPG, PNG, WebP — maks. 2 MB)</label>
                <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp"
                       class="block w-full text-sm text-[#51656f] file:mr-3 file:py-2 file:px-3.5 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-[#ecf0f2] file:text-[#1f3038] hover:file:bg-[#e2e8ec]">
                <x-input-error class="mt-1.5" :messages="$errors->get('avatar')" />
            </div>
        </div>

        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Alamat Email" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="px-5 py-2.5 rounded-lg bg-[#0d7480] hover:bg-[#0a5d67] text-white text-sm font-medium transition">
                Simpan Profil
            </button>

            @if($user->avatar)
                <button type="submit" name="remove_avatar" value="1" class="px-4 py-2.5 rounded-lg bg-[#ffe4e6] hover:bg-[#fecdd3] text-[#881337] text-sm font-medium transition">
                    Hapus Foto
                </button>
            @endif

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-sm font-medium text-[#15803d]">
                    Profil berhasil disimpan.
                </p>
            @endif
        </div>
    </form>

    <script>
        document.getElementById('avatar')?.addEventListener('change', (e) => {
            const file = e.target.files?.[0];
            const box = document.getElementById('avatar-preview-box');
            if (!file || !box) return;
            const reader = new FileReader();
            reader.onload = () => {
                box.innerHTML = '';
                const img = document.createElement('img');
                img.src = String(reader.result);
                img.className = 'w-full h-full object-cover';
                box.append(img);
            };
            reader.readAsDataURL(file);
        });
    </script>
</section>
