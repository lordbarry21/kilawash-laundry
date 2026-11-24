<?php
/**
 * ============================================================================
 * FILE: login.php
 * FUNGSI: Halaman autentikasi untuk Admin dan Kasir.
 *         Mengikuti standar modern-web-guidance (autofill-sign-in-form,
 *         validate-input-after-interaction, accessible-error-announcement).
 * ============================================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

// 1. Jika sudah login, langsung arahkan ke Dashboard
if (is_logged_in()) {
    redirect('index.php');
}

$error = '';
$usernameInput = '';

// 2. Proses Login saat Form Dikirim (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf('login.php');

    $usernameInput = trim((string) ($_POST['username'] ?? ''));
    $passwordInput = (string) ($_POST['password'] ?? '');

    if ($usernameInput === '' || $passwordInput === '') {
        $error = 'Masukkan username dan kata sandi terlebih dahulu.';
    } elseif (attempt_login($usernameInput, $passwordInput)) {
        $user = current_user();
        set_flash('success', 'Selamat datang, ' . ($user['nama'] ?? 'Pengguna') . '.');
        redirect('index.php');
    } else {
        $error = 'Username atau kata sandi tidak cocok. Periksa kembali akun Anda.';
    }
}

$flash = get_flash();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#f7f9fa">
  <title>Masuk — <?= e(APP_NAME) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Host+Grotesk:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/tokens.css">
  <link rel="stylesheet" href="assets/css/components.css">
  <link rel="stylesheet" href="assets/css/layout.css">
</head>
<body>
  <div class="login-page">
    <!-- Panel Kiri: Hero Fotografi Editorial & Informasi Layanan -->
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
        <h1 class="login-aside__heading">Kelola layanan, transaksi, dan laporan laundry dalam satu pintu.</h1>
        <p class="login-aside__lead">
          Pencatatan order otomatis terhubung dengan tarif layanan, pemantauan status pengerjaan dari Masuk hingga Selesai, dan rekap keuangan real-time untuk Admin maupun Kasir.
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

    <!-- Panel Kanan: Kartu Form Login (di atas pada layar mobile agar tidak tertutup keyboard) -->
    <main class="login-main">
      <section class="login-card" aria-labelledby="login-heading">
        <div>
          <h2 id="login-heading" style="font-size:1.5rem">Masuk ke <?= e(APP_NAME) ?></h2>
          <p class="muted" style="font-size:0.875rem;margin-top:4px">Gunakan akun Admin atau Kasir Anda untuk melanjutkan.</p>
        </div>

        <?php if ($flash): ?>
          <div class="flash flash--<?= e($flash['type']) ?>" role="status" style="margin-bottom:0">
            <span><?= e($flash['message']) ?></span>
          </div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
          <div class="flash flash--error" role="alert" style="margin-bottom:0">
            <span><?= e($error) ?></span>
          </div>
        <?php endif; ?>

        <form class="login-form" method="post" action="login.php">
          <?= csrf_field() ?>

          <div class="field">
            <label class="field__label" for="username">Username</label>
            <input
              class="field__input"
              type="text"
              id="username"
              name="username"
              value="<?= e($usernameInput) ?>"
              autocomplete="username"
              spellcheck="false"
              autocapitalize="none"
              autocorrect="off"
              enterkeyhint="next"
              required
              data-error="username-error"
              placeholder="Contoh: admin atau kasir"
            >
            <span id="username-error" class="error-msg">Masukkan username akun Anda.</span>
          </div>

          <div class="field">
            <label class="field__label" for="password">Kata Sandi</label>
            <div class="password-wrap">
              <input
                class="field__input"
                type="password"
                id="password"
                name="password"
                autocomplete="current-password"
                enterkeyhint="done"
                required
                data-error="password-error"
                placeholder="Masukkan kata sandi"
              >
              <button
                type="button"
                class="password-toggle"
                id="toggle-password"
                aria-label="Tampilkan kata sandi"
                aria-pressed="false"
              >
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
            <span id="password-error" class="error-msg">Masukkan kata sandi Anda.</span>
          </div>

          <button type="submit" class="btn btn--primary btn--full">Masuk ke Sistem</button>
        </form>

        <p class="muted" style="font-size:0.875rem;text-align:center">
          Belum punya akun petugas? <a href="register.php" style="color:var(--accent);font-weight:500;text-decoration:underline">Daftar sekarang</a>
        </p>

        <!-- Akun Uji Coba 1-Klik untuk Demo Presentasi -->
        <div class="demo-box">
          <p class="demo-box__title">Akun Uji Coba (Klik untuk isi otomatis)</p>
          <div class="demo-box__grid">
            <button type="button" class="demo-chip" data-fill-demo data-username="admin" data-password="admin123">
              <span class="demo-chip__role">Admin (Full Akses)</span>
              <span class="demo-chip__cred mono">admin / admin123</span>
            </button>
            <button type="button" class="demo-chip" data-fill-demo data-username="kasir" data-password="kasir123">
              <span class="demo-chip__role">Kasir (Operasional)</span>
              <span class="demo-chip__cred mono">kasir / kasir123</span>
            </button>
          </div>
        </div>
      </section>
    </main>
  </div>

  <script src="assets/js/validation.js" defer></script>
  <script src="assets/js/app.js" defer></script>
</body>
</html>
