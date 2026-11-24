<section>
    <header>
        <h2 class="text-lg font-semibold tracking-[-0.02em] text-[#141416]">
            Foto &amp; Informasi Profil
        </h2>
        <p class="mt-1 text-sm text-[#71717a]">
            Perbarui foto profil, nama tampilan, dan alamat email akun Anda.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form id="profileUpdateForm" method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')
        <input type="hidden" id="avatar_data" name="avatar_data" value="">

        <!-- Foto Profil (Auto-compress ke 256x256 JPEG agar cepat & anti error ukuran) -->
        <div class="flex flex-wrap items-center gap-4">
            <div id="avatar-preview-box" class="w-20 h-20 rounded-full bg-[#141416] text-white border border-black/[0.08] flex items-center justify-center text-2xl font-semibold overflow-hidden shrink-0 shadow-sm">
                @if($user->avatar)
                    <img src="{{ $user->avatar }}" alt="Foto profil {{ $user->name }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                @endif
            </div>
            <div class="flex-1 min-w-[220px]">
                <label for="avatar_picker" class="block text-xs font-medium text-[#52525b] mb-1.5">
                    Foto Profil (JPG, PNG, WebP — otomatis dioptimalkan)
                </label>
                <input id="avatar_picker" type="file" accept="image/jpeg,image/png,image/webp"
                       class="block w-full text-xs text-[#71717a] file:mr-3 file:py-2 file:px-4 file:rounded-full file:border file:border-black/10 file:text-xs file:font-medium file:bg-[#f4f4f6] file:text-[#141416] hover:file:bg-[#e4e4e7] file:transition cursor-pointer">
                <p id="avatar_status" class="mt-1.5 text-xs text-[#16a34a] hidden"></p>
                <x-input-error class="mt-1.5" :messages="$errors->get('avatar')" />
                <x-input-error class="mt-1.5" :messages="$errors->get('avatar_data')" />
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

        <div class="flex flex-wrap items-center gap-3 pt-1">
            <button type="submit" id="saveProfileBtn" class="btn-ink">
                Simpan Profil
            </button>

            @if($user->avatar)
                <button type="submit" name="remove_avatar" value="1" class="px-4 py-2.5 rounded-full bg-[#ffe4e6] hover:bg-[#fecdd3] text-[#9f1239] text-xs font-medium transition">
                    Hapus Foto
                </button>
            @endif

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm font-medium text-[#16a34a]">
                    Profil berhasil disimpan!
                </p>
            @endif
        </div>
    </form>

    <script>
        (() => {
            const picker = document.getElementById('avatar_picker');
            const hidden = document.getElementById('avatar_data');
            const box = document.getElementById('avatar-preview-box');
            const status = document.getElementById('avatar_status');
            if (!picker || !hidden || !box) return;

            picker.addEventListener('change', (e) => {
                const file = e.target.files?.[0];
                if (!file) return;

                if (!/^image\/(jpeg|png|webp)$/.test(file.type)) {
                    status.textContent = 'Gunakan gambar berformat JPG, PNG, atau WebP.';
                    status.className = 'mt-1.5 text-xs text-[#e11d48]';
                    hidden.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = () => {
                    const img = new Image();
                    img.onload = () => {
                        const size = 256;
                        const canvas = document.createElement('canvas');
                        canvas.width = size;
                        canvas.height = size;
                        const ctx = canvas.getContext('2d');

                        // Center square crop
                        const minSide = Math.min(img.width, img.height);
                        const sx = (img.width - minSide) / 2;
                        const sy = (img.height - minSide) / 2;
                        ctx.drawImage(img, sx, sy, minSide, minSide, 0, 0, size, size);

                        const compressed = canvas.toDataURL('image/jpeg', 0.86);
                        hidden.value = compressed;

                        box.innerHTML = '';
                        const preview = document.createElement('img');
                        preview.src = compressed;
                        preview.alt = 'Preview foto profil';
                        preview.className = 'w-full h-full object-cover';
                        box.append(preview);

                        const kb = Math.max(1, Math.round((compressed.length * 3) / 4 / 1024));
                        status.textContent = `Foto siap disimpan (${kb} KB) — klik "Simpan Profil".`;
                        status.className = 'mt-1.5 text-xs text-[#16a34a]';
                    };
                    img.src = String(reader.result);
                };
                reader.readAsDataURL(file);
            });
        })();
    </script>
</section>
