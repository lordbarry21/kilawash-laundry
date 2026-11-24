<?php
/**
 * ============================================================================
 * FILE: profil.php
 * FUNGSI: Halaman Pengaturan Akun Pengguna (Admin & Kasir).
 *         Memungkinkan pengguna mengubah:
 *         1. Foto Profil (upload JPG/PNG/WebP maks. 2 MB atau hapus foto)
 *         2. Nama Lengkap & Username
 *         3. Kata Sandi (verifikasi sandi lama + hash bcrypt baru)
 * ============================================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_login();

$user     = current_user();
$initials = strtoupper(substr((string) ($user['nama'] ?? 'U'), 0, 1));
$avatar   = $user['avatar'] ?? null;

$pageTitle  = 'Pengaturan Akun';
$activeMenu = 'profil';
require __DIR__ . '/includes/layout_top.php';
?>

<!-- 1. Header Halaman -->
<div class="page-head">
  <div>
    <h1 class="page-head__title">Pengaturan Akun</h1>
    <p class="page-head__desc">Perbarui foto profil, nama tampilan, dan kata sandi akun Anda secara langsung.</p>
  </div>
  <span class="badge <?= is_admin() ? 'badge--accent' : 'badge--masuk' ?>">
    Role: <?= e(ucfirst($user['role'] ?? '')) ?>
  </span>
</div>

<div class="dashboard-grid">
  <!-- 2. Kartu Profil: Foto & Identitas -->
  <section class="card" aria-labelledby="profil-info-heading">
    <div class="card__header">
      <div>
        <h2 class="card__title" id="profil-info-heading">Foto &amp; Informasi Profil</h2>
        <p class="card__subtitle">Perubahan langsung tampil di sidebar dan nota transaksi</p>
      </div>
    </div>

    <div class="card__body">
      <form method="post" action="actions/profil_action.php" enctype="multipart/form-data" class="login-form">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="update_profile">

        <!-- Pratinjau Avatar + Input Upload -->
        <div class="profile-avatar-row">
          <div class="profile-avatar-lg" id="avatar-preview-box">
            <?php if (!empty($avatar)): ?>
              <img src="<?= e($avatar) ?>" alt="Foto profil <?= e($user['nama'] ?? '') ?>">
            <?php else: ?>
              <span><?= e($initials) ?></span>
            <?php endif; ?>
          </div>

          <div style="display:flex;flex-direction:column;gap:8px;min-width:0;flex:1">
            <label class="field__label" for="profil-avatar">Foto Profil</label>
            <input
              class="field__input"
              type="file"
              id="profil-avatar"
              name="avatar"
              accept="image/jpeg,image/png,image/webp"
              style="padding:8px 10px;font-size:0.875rem"
            >
            <p class="field__hint">Format JPG, PNG, atau WebP — ukuran maksimal 2 MB.</p>
          </div>
        </div>

        <div class="form-grid">
          <div class="field">
            <label class="field__label" for="profil-nama">Nama Lengkap</label>
            <input
              class="field__input"
              type="text"
              id="profil-nama"
              name="nama"
              value="<?= e($user['nama'] ?? '') ?>"
              maxlength="100"
              required
              data-error="profil-nama-error"
              placeholder="Contoh: Bari Achmad"
            >
            <span id="profil-nama-error" class="error-msg">Nama lengkap wajib diisi.</span>
          </div>

          <div class="field">
            <label class="field__label" for="profil-username">Username Login</label>
            <input
              class="field__input mono"
              type="text"
              id="profil-username"
              name="username"
              value="<?= e($user['username'] ?? '') ?>"
              maxlength="50"
              pattern="[a-zA-Z0-9._-]{3,50}"
              spellcheck="false"
              autocapitalize="none"
              required
              data-error="profil-username-error"
            >
            <span id="profil-username-error" class="error-msg">Username 3–50 karakter (huruf, angka, titik, atau garis bawah).</span>
          </div>
        </div>

        <div class="btn-group" style="justify-content:space-between;width:100%;padding-top:4px">
          <div>
            <?php if (!empty($avatar)): ?>
              <button type="submit" name="remove_avatar" value="1" class="btn btn--danger-soft btn--sm">
                Hapus Foto
              </button>
            <?php endif; ?>
          </div>
          <button type="submit" class="btn btn--primary">Simpan Profil</button>
        </div>
      </form>
    </div>
  </section>

  <!-- 3. Kartu Ubah Kata Sandi -->
  <section class="card" aria-labelledby="profil-pass-heading">
    <div class="card__header">
      <div>
        <h2 class="card__title" id="profil-pass-heading">Ubah Kata Sandi</h2>
        <p class="card__subtitle">Gunakan minimal 6 karakter demi keamanan akun</p>
      </div>
    </div>

    <div class="card__body">
      <form method="post" action="actions/profil_action.php" class="login-form">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="update_password">

        <div class="field">
          <label class="field__label" for="old-password">Kata Sandi Saat Ini</label>
          <input
            class="field__input"
            type="password"
            id="old-password"
            name="current_password"
            autocomplete="current-password"
            required
            data-error="old-pass-error"
            placeholder="Masukkan kata sandi lama"
          >
          <span id="old-pass-error" class="error-msg">Masukkan kata sandi saat ini untuk verifikasi.</span>
        </div>

        <div class="field">
          <label class="field__label" for="new-password">Kata Sandi Baru</label>
          <input
            class="field__input"
            type="password"
            id="new-password"
            name="new_password"
            autocomplete="new-password"
            minlength="6"
            required
            data-error="new-pass-error"
            placeholder="Minimal 6 karakter"
          >
          <span id="new-pass-error" class="error-msg">Kata sandi baru minimal 6 karakter.</span>
        </div>

        <div class="field">
          <label class="field__label" for="confirm-password">Konfirmasi Kata Sandi Baru</label>
          <input
            class="field__input"
            type="password"
            id="confirm-password"
            name="confirm_password"
            autocomplete="new-password"
            minlength="6"
            required
            data-error="confirm-pass-error"
            placeholder="Ulangi kata sandi baru"
          >
          <span id="confirm-pass-error" class="error-msg">Konfirmasi kata sandi wajib diisi.</span>
        </div>

        <button type="submit" class="btn btn--secondary btn--full">Perbarui Kata Sandi</button>
      </form>
    </div>
  </section>
</div>

<script>
  // Pratinjau langsung foto profil sebelum klik Simpan
  const avatarInput = document.getElementById('profil-avatar');
  const avatarBox = document.getElementById('avatar-preview-box');
  if (avatarInput && avatarBox) {
    avatarInput.addEventListener('change', () => {
      const file = avatarInput.files?.[0];
      if (!file || !file.type.startsWith('image/')) return;
      const reader = new FileReader();
      reader.onload = () => {
        avatarBox.innerHTML = '';
        const img = document.createElement('img');
        img.src = String(reader.result);
        img.alt = 'Pratinjau foto profil';
        avatarBox.append(img);
      };
      reader.readAsDataURL(file);
    });
  }
</script>

<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
