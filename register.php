<?php
/**
 * ============================================================================
 * FILE: register.php
 * FUNGSI: Halaman Registrasi Akun Baru (Kasir / Admin) dengan validasi modern,
 *         hash kata sandi Bcrypt, dan proteksi CSRF.
 * ============================================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    redirect('index.php');
}

$error         = '';
$namaInput     = '';
$usernameInput = '';
$roleInput     = 'kasir';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf('register.php');

    $namaInput     = trim((string) ($_POST['nama'] ?? ''));
    $usernameInput = trim((string) ($_POST['username'] ?? ''));
    $passwordInput = (string) ($_POST['password'] ?? '');
    $confirmInput  = (string) ($_POST['confirm_password'] ?? '');
    $roleInput     = in_array($_POST['role'] ?? 'kasir', ['kasir', 'admin'], true) ? (string) $_POST['role'] : 'kasir';

    if ($namaInput === '' || !preg_match('/^[a-zA-Z0-9._-]{3,50}$/', $usernameInput)) {
        $error = 'Isi nama lengkap dan username 3–50 karakter (tanpa spasi).';
    } elseif (strlen($passwordInput) < 6) {
        $error = 'Kata sandi minimal 6 karakter.';
    } elseif ($passwordInput !== $confirmInput) {
        $error = 'Konfirmasi kata sandi tidak cocok.';
    } else {
        try {
            $pdo  = db();
            $stmt = $pdo->prepare(
                'INSERT INTO users (nama, username, password_hash, role)
                 VALUES (:nama, :username, :hash, :role)'
            );
            $stmt->execute([
                'nama'     => $namaInput,
                'username' => $usernameInput,
                'hash'     => password_hash($passwordInput, PASSWORD_BCRYPT),
                'role'     => $roleInput,
            ]);

            set_flash('success', "Akun \"{$usernameInput}\" berhasil dibuat. Silakan masuk.");
            redirect('login.php');
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? 0) === 1062 || str_contains($e->getMessage(), 'UNIQUE')) {
                $error = "Username \"{$usernameInput}\" sudah terdaftar. Pilih username lain.";
            } else {
                $error = 'Gagal membuat akun: ' . $e->getMessage();
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#f7f9fa">
  <title>Daftar Akun — <?= e(APP_NAME) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Host+Grotesk:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/tokens.css">
  <link rel="stylesheet" href="assets/css/components.css">
  <link rel="stylesheet" href="assets/css/layout.css">
</head>
<body>
  <div class="login-page">
    <section class="login-aside" aria-label="Informasi sistem">
      <img class="login-aside__bg" src="assets/img/laundry-hero.jpg" alt="" fetchpriority="high">
      <div class="login-aside__brand">
        <span class="brand__mark" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="6"/><path d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/><line x1="7" y1="6" x2="7.01" y2="6"/><line x1="11" y1="6" x2="11.01" y2="6"/></svg>
        </span>
        <div>
          <p class="brand__name" style="color:#ffffff"><?= e(APP_NAME) ?></p>
          <p class="brand__sub" style="color:rgba(247,249,250,0.72)"><?= e(APP_SUBTITLE) ?></p>
        </div>
      </div>

      <div>
        <h1 class="login-aside__heading">Tambahkan petugas kasir atau admin outlet baru dengan cepat.</h1>
        <p class="login-aside__lead">
          Setiap akun dilengkapi hak akses berbasis peran (RBAC): Admin mengelola penuh tarif layanan &amp; laporan, sedangkan Kasir fokus pada pencatatan order dan pembaruan status cucian.
        </p>

        <div class="login-rates" aria-label="Ringkasan tarif standar">
          <div class="login-rate-item">
            <p class="login-rate-item__name">Reguler</p>
            <p class="login-rate-item__price mono">Rp 7.000/kg</p>
            <p class="login-rate-item__eta">3–4 hari</p>
          </div>
          <div class="login-rate-item">
            <p class="login-rate-item__name">Ekspress</p>
            <p class="login-rate-item__price mono">Rp 12.000/kg</p>
            <p class="login-rate-item__eta">1–2 hari</p>
          </div>
          <div class="login-rate-item">
            <p class="login-rate-item__name">Superkilat</p>
            <p class="login-rate-item__price mono">Rp 25.000/kg</p>
            <p class="login-rate-item__eta">5 jam selesai</p>
          </div>
        </div>
      </div>

      <p style="font-size:0.8125rem;color:rgba(247,249,250,0.6)"><?= e(APP_ADDRESS) ?></p>
    </section>

    <main class="login-main">
      <section class="login-card" aria-labelledby="register-heading">
        <div>
          <h2 id="register-heading" style="font-size:1.5rem">Buat Akun Baru</h2>
          <p class="muted" style="font-size:0.875rem;margin-top:4px">Daftarkan akun Admin atau Kasir untuk mengakses <?= e(APP_NAME) ?>.</p>
        </div>

        <?php if ($error !== ''): ?>
          <div class="flash flash--error" role="alert" style="margin-bottom:0">
            <span><?= e($error) ?></span>
          </div>
        <?php endif; ?>

        <form class="login-form" method="post" action="register.php">
          <?= csrf_field() ?>

          <div class="field">
            <label class="field__label" for="reg-nama">Nama Lengkap</label>
            <input
              class="field__input"
              type="text"
              id="reg-nama"
              name="nama"
              value="<?= e($namaInput) ?>"
              autocomplete="name"
              maxlength="100"
              required
              data-error="reg-nama-error"
              placeholder="Contoh: Bari Achmad"
            >
            <span id="reg-nama-error" class="error-msg">Nama lengkap wajib diisi.</span>
          </div>

          <div class="form-grid">
            <div class="field">
              <label class="field__label" for="reg-username">Username</label>
              <input
                class="field__input mono"
                type="text"
                id="reg-username"
                name="username"
                value="<?= e($usernameInput) ?>"
                autocomplete="username"
                spellcheck="false"
                autocapitalize="none"
                autocorrect="off"
                pattern="[a-zA-Z0-9._-]{3,50}"
                required
                data-error="reg-username-error"
                placeholder="kasir_baru"
              >
              <span id="reg-username-error" class="error-msg">3–50 karakter tanpa spasi.</span>
            </div>

            <div class="field">
              <label class="field__label" for="reg-role">Peran (Role)</label>
              <select class="field__select" id="reg-role" name="role">
                <option value="kasir"<?= $roleInput === 'kasir' ? ' selected' : '' ?>>Kasir (Operasional)</option>
                <option value="admin"<?= $roleInput === 'admin' ? ' selected' : '' ?>>Admin (Akses Penuh)</option>
              </select>
            </div>
          </div>

          <div class="form-grid">
            <div class="field">
              <label class="field__label" for="reg-password">Kata Sandi</label>
              <input
                class="field__input"
                type="password"
                id="reg-password"
                name="password"
                autocomplete="new-password"
                minlength="6"
                required
                data-error="reg-password-error"
                placeholder="Min. 6 karakter"
              >
              <span id="reg-password-error" class="error-msg">Minimal 6 karakter.</span>
            </div>

            <div class="field">
              <label class="field__label" for="reg-confirm">Ulangi Sandi</label>
              <input
                class="field__input"
                type="password"
                id="reg-confirm"
                name="confirm_password"
                autocomplete="new-password"
                minlength="6"
                required
                data-error="reg-confirm-error"
                placeholder="Ketik ulang sandi"
              >
              <span id="reg-confirm-error" class="error-msg">Konfirmasi sandi wajib diisi.</span>
            </div>
          </div>

          <button type="submit" class="btn btn--primary btn--full">Daftar Akun</button>
        </form>

        <p class="muted" style="font-size:0.875rem;text-align:center">
          Sudah punya akun? <a href="login.php" style="color:var(--accent);font-weight:500;text-decoration:underline">Masuk di sini</a>
        </p>
      </section>
    </main>
  </div>

  <script src="assets/js/validation.js" defer></script>
  <script src="assets/js/app.js" defer></script>
</body>
</html>
