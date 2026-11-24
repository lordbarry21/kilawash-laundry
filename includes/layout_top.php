<?php
/**
 * ============================================================================
 * FILE: includes/layout_top.php
 * FUNGSI: Template pembuka halaman dashboard (Head HTML, Google Fonts,
 *         Skip Link, Sidebar Desktop, Mobile Navigation Drawer, dan Topbar).
 * VARIABEL MASUKAN:
 *   $pageTitle  (string) — Judul halaman yang tampil di <title> dan topbar
 *   $activeMenu (string) — 'dashboard' | 'layanan' | 'transaksi' | 'laporan' | 'profil'
 * ============================================================================
 */

declare(strict_types=1);

$user       = current_user();
$flash      = get_flash();
$pageTitle  = $pageTitle ?? 'Dashboard';
$activeMenu = $activeMenu ?? 'dashboard';
$initials   = strtoupper(substr((string) ($user['nama'] ?? 'U'), 0, 1));
$avatarUrl  = $user['avatar'] ?? null;

// Daftar menu utama (sesuai spesifikasi tugas ASAS + Pengaturan Akun)
$navItems = [
    [
        'key'   => 'dashboard',
        'href'  => 'index.php',
        'label' => 'Dashboard',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>',
    ],
    [
        'key'   => 'layanan',
        'href'  => 'layanan.php',
        'label' => 'Data Layanan',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.59 13.41 13.42 20.58a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82Z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>',
    ],
    [
        'key'   => 'transaksi',
        'href'  => 'transaksi.php',
        'label' => 'Rekap Transaksi',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>',
    ],
    [
        'key'   => 'laporan',
        'href'  => 'laporan.php',
        'label' => 'Laporan Keuangan',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>',
    ],
    [
        'key'   => 'profil',
        'href'  => 'profil.php',
        'label' => 'Pengaturan Akun',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>',
    ],
];
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#f7f9fa">
  <title><?= e($pageTitle) ?> — <?= e(APP_NAME) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Host+Grotesk:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/tokens.css">
  <link rel="stylesheet" href="assets/css/components.css">
  <link rel="stylesheet" href="assets/css/layout.css">
</head>
<body>
  <a class="skip-link" href="#main-content">Lewati ke konten utama</a>

  <!-- 1. MOBILE NAVIGATION DRAWER (modern-web-guidance: modal <dialog> + scroll-snap) -->
  <dialog class="drawer" id="mobile-drawer" aria-label="Menu navigasi utama">
    <div class="drawer-scroller">
      <nav class="drawer-sheet" aria-label="Navigasi mobile">
        <div class="drawer-sheet__top">
          <a href="index.php" class="brand">
            <span class="brand__mark" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="6"/><path d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/><line x1="7" y1="6" x2="7.01" y2="6"/><line x1="11" y1="6" x2="11.01" y2="6"/></svg>
            </span>
            <span>
              <span class="brand__name"><?= e(APP_NAME) ?></span>
              <span class="brand__sub">Manajemen Laundry</span>
            </span>
          </a>
          <button type="button" class="modal__close" id="drawer-close" aria-label="Tutup menu navigasi" autofocus>
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>

        <div>
          <p class="nav-section-label">Menu Utama</p>
          <ul class="nav-list">
            <?php foreach ($navItems as $item): ?>
              <li>
                <a class="nav-link" href="<?= e($item['href']) ?>"<?= $activeMenu === $item['key'] ? ' aria-current="page"' : '' ?>>
                  <?= $item['icon'] ?>
                  <span><?= e($item['label']) ?></span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>

        <div class="sidebar__footer">
          <a href="profil.php" class="user-pill user-pill--link" title="Buka Pengaturan Akun">
            <span class="user-pill__avatar" aria-hidden="true">
              <?php if (!empty($avatarUrl)): ?>
                <img src="<?= e($avatarUrl) ?>" alt="">
              <?php else: ?>
                <?= e($initials) ?>
              <?php endif; ?>
            </span>
            <div class="user-pill__info">
              <p class="user-pill__name"><?= e($user['nama'] ?? '') ?></p>
              <p class="user-pill__role">Role: <?= e(ucfirst($user['role'] ?? '')) ?></p>
            </div>
          </a>
          <a href="logout.php" class="btn btn--secondary btn--full">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            <span>Keluar</span>
          </a>
        </div>
      </nav>
    </div>
  </dialog>

  <!-- 2. SHELL UTAMA (Sidebar Desktop + Main Content) -->
  <div class="app-shell">
    <aside class="sidebar" aria-label="Navigasi samping">
      <a href="index.php" class="brand">
        <span class="brand__mark" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="6"/><path d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/><line x1="7" y1="6" x2="7.01" y2="6"/><line x1="11" y1="6" x2="11.01" y2="6"/></svg>
        </span>
        <span>
          <span class="brand__name"><?= e(APP_NAME) ?></span>
          <span class="brand__sub">Manajemen Laundry</span>
        </span>
      </a>

      <nav aria-label="Menu utama">
        <p class="nav-section-label">Menu Utama</p>
        <ul class="nav-list">
          <?php foreach ($navItems as $item): ?>
            <li>
              <a class="nav-link" href="<?= e($item['href']) ?>"<?= $activeMenu === $item['key'] ? ' aria-current="page"' : '' ?>>
                <?= $item['icon'] ?>
                <span><?= e($item['label']) ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </nav>

      <div class="sidebar__footer">
        <a href="profil.php" class="user-pill user-pill--link" title="Buka Pengaturan Akun">
          <span class="user-pill__avatar" aria-hidden="true">
            <?php if (!empty($avatarUrl)): ?>
              <img src="<?= e($avatarUrl) ?>" alt="">
            <?php else: ?>
              <?= e($initials) ?>
            <?php endif; ?>
          </span>
          <div class="user-pill__info">
            <p class="user-pill__name"><?= e($user['nama'] ?? '') ?></p>
            <p class="user-pill__role">Role: <?= e(ucfirst($user['role'] ?? '')) ?></p>
          </div>
        </a>
        <a href="logout.php" class="btn btn--secondary btn--full">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          <span>Keluar</span>
        </a>
      </div>
    </aside>

    <div class="app-main-wrap">
      <header class="topbar">
        <div class="topbar__left">
          <button
            type="button"
            class="topbar__menu-btn"
            id="drawer-open"
            aria-label="Buka menu navigasi"
            aria-expanded="false"
            aria-controls="mobile-drawer"
          >
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/></svg>
          </button>
          <span class="topbar__title"><?= e($pageTitle) ?></span>
        </div>

        <div class="btn-group">
          <span class="badge <?= is_admin() ? 'badge--accent' : 'badge--masuk' ?>">
            <?= is_admin() ? 'Admin — Akses Penuh' : 'Kasir — Operasional' ?>
          </span>
        </div>
      </header>

      <main class="app-content" id="main-content">
        <?php if ($flash): ?>
          <div class="flash flash--<?= e($flash['type']) ?>" role="status">
            <span><?= e($flash['message']) ?></span>
            <button type="button" class="flash__close" data-dismiss-flash aria-label="Tutup pemberitahuan">&times;</button>
          </div>
        <?php endif; ?>
