<?php
/**
 * ============================================================================
 * FILE: index.php
 * FUNGSI: Halaman Dashboard Utama.
 *         Menampilkan ringkasan KPI (Total Transaksi, Sedang Diproses, Selesai,
 *         Total Pendapatan), daftar tarif layanan aktif, dan 5 transaksi terbaru.
 * ============================================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_login();

$pdo = db();

// 1. Query Ringkasan KPI dari Tabel `transaksi`
$kpi = $pdo->query("
    SELECT
        COUNT(*) AS total_transaksi,
        SUM(CASE WHEN status IN ('Masuk', 'Proses') THEN 1 ELSE 0 END) AS sedang_berjalan,
        SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END) AS selesai,
        COALESCE(SUM(total_harga), 0) AS total_nilai,
        COALESCE(SUM(CASE WHEN status = 'Selesai' THEN total_harga ELSE 0 END), 0) AS pendapatan_selesai
    FROM transaksi
")->fetch();

// 2. Query 5 Transaksi Terbaru (INNER JOIN dengan tabel `layanan`)
$recentTransactions = $pdo->query("
    SELECT
        t.id,
        t.kode_transaksi,
        t.nama_pelanggan,
        t.no_hp,
        t.berat_kg,
        t.total_harga,
        t.status,
        t.tanggal_masuk,
        l.nama_layanan
    FROM transaksi t
    INNER JOIN layanan l ON t.layanan_id = l.id
    ORDER BY t.tanggal_masuk DESC, t.id DESC
    LIMIT 5
")->fetchAll();

// 3. Query Daftar Layanan Aktif + Jumlah Order
$layananList = $pdo->query("
    SELECT
        l.id,
        l.nama_layanan,
        l.harga_per_kg,
        l.estimasi_waktu,
        COUNT(t.id) AS jumlah_order
    FROM layanan l
    LEFT JOIN transaksi t ON t.layanan_id = l.id
    GROUP BY l.id
    ORDER BY l.harga_per_kg ASC
")->fetchAll();

$pageTitle  = 'Dashboard';
$activeMenu = 'dashboard';
require __DIR__ . '/includes/layout_top.php';
?>

<!-- 1. Header Halaman -->
<div class="page-head">
  <div>
    <h1 class="page-head__title">Ringkasan Operasional</h1>
    <p class="page-head__desc">Pantau antrean cucian, status pengerjaan, dan pendapatan usaha laundry secara langsung.</p>
  </div>
  <div class="btn-group">
    <a href="transaksi.php?new=1" class="btn btn--primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      <span>Transaksi Baru</span>
    </a>
    <a href="laporan.php" class="btn btn--secondary">Lihat Laporan</a>
  </div>
</div>

<!-- 2. Grid 4 Kartu KPI -->
<section class="kpi-grid" aria-label="Statistik utama laundry">
  <article class="kpi-card">
    <div class="kpi-card__top">
      <span class="kpi-card__label">Total Transaksi</span>
      <span class="kpi-card__icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><polyline points="14 2 14 8 20 8"/></svg>
      </span>
    </div>
    <p class="kpi-card__value mono"><?= (int) ($kpi['total_transaksi'] ?? 0) ?></p>
    <p class="kpi-card__meta">Seluruh order tercatat</p>
  </article>

  <article class="kpi-card">
    <div class="kpi-card__top">
      <span class="kpi-card__label">Masuk &amp; Diproses</span>
      <span class="kpi-card__icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15 15"/></svg>
      </span>
    </div>
    <p class="kpi-card__value mono"><?= (int) ($kpi['sedang_berjalan'] ?? 0) ?></p>
    <p class="kpi-card__meta">Perlu dipantau hingga selesai</p>
  </article>

  <article class="kpi-card">
    <div class="kpi-card__top">
      <span class="kpi-card__label">Cucian Selesai</span>
      <span class="kpi-card__icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      </span>
    </div>
    <p class="kpi-card__value mono"><?= (int) ($kpi['selesai'] ?? 0) ?></p>
    <p class="kpi-card__meta">Siap diambil pelanggan</p>
  </article>

  <article class="kpi-card">
    <div class="kpi-card__top">
      <span class="kpi-card__label">Total Nilai Order</span>
      <span class="kpi-card__icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
      </span>
    </div>
    <p class="kpi-card__value mono"><?= rupiah($kpi['total_nilai'] ?? 0) ?></p>
    <p class="kpi-card__meta">Selesai: <?= rupiah($kpi['pendapatan_selesai'] ?? 0) ?></p>
  </article>
</section>

<!-- 3. Konten 2 Kolom: Transaksi Terbaru + Daftar Layanan -->
<div class="dashboard-grid">
  <!-- Tabel 5 Transaksi Terbaru -->
  <section class="card" aria-labelledby="recent-trx-heading">
    <div class="card__header">
      <div>
        <h2 class="card__title" id="recent-trx-heading">Transaksi Terbaru</h2>
        <p class="card__subtitle">5 pencatatan terakhir beserta status pengerjaan</p>
      </div>
      <a href="transaksi.php" class="btn btn--secondary btn--sm">Kelola Semua</a>
    </div>

    <div class="card__body card__body--flush">
      <?php if (empty($recentTransactions)): ?>
        <div class="empty-state">
          <p class="empty-state__title">Belum ada transaksi</p>
          <p>Klik tombol Transaksi Baru untuk mulai mencatat order cucian.</p>
        </div>
      <?php else: ?>
        <div tabindex="0" role="region" aria-labelledby="recent-trx-heading" class="table-wrapper">
          <table class="data-table data-table--responsive">
            <caption class="visually-hidden">Daftar 5 transaksi laundry terbaru</caption>
            <thead>
              <tr>
                <th scope="col">Pelanggan</th>
                <th scope="col">Layanan</th>
                <th scope="col">Berat</th>
                <th scope="col">Total</th>
                <th scope="col">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentTransactions as $trx): ?>
                <tr>
                  <th scope="row">
                    <div><?= e($trx['nama_pelanggan']) ?></div>
                    <div class="muted mono" style="font-size:0.75rem;font-weight:400"><?= e($trx['kode_transaksi']) ?></div>
                  </th>
                  <td data-label="Layanan"><?= e($trx['nama_layanan']) ?></td>
                  <td data-label="Berat" class="mono"><?= e(format_kg($trx['berat_kg'])) ?></td>
                  <td data-label="Total" class="mono"><?= e(rupiah($trx['total_harga'])) ?></td>
                  <td data-label="Status">
                    <span class="badge <?= e(status_badge_class($trx['status'])) ?>">
                      <?= e($trx['status']) ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- Panel Daftar Tarif Layanan -->
  <section class="card" aria-labelledby="service-list-heading">
    <div class="card__header">
      <div>
        <h2 class="card__title" id="service-list-heading">Jenis Layanan Aktif</h2>
        <p class="card__subtitle">Acuan tarif otomatis</p>
      </div>
      <a href="layanan.php" class="btn btn--secondary btn--sm">
        <?= is_admin() ? 'Kelola Tarif' : 'Lihat Detail' ?>
      </a>
    </div>

    <div class="card__body card__body--flush">
      <div tabindex="0" role="region" aria-labelledby="service-list-heading" class="table-wrapper">
        <table class="data-table">
          <caption class="visually-hidden">Daftar tarif jenis layanan laundry</caption>
          <thead>
            <tr>
              <th scope="col">Layanan</th>
              <th scope="col">Tarif / kg</th>
              <th scope="col">Order</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($layananList as $lay): ?>
              <tr>
                <th scope="row">
                  <div><?= e($lay['nama_layanan']) ?></div>
                  <div class="muted" style="font-size:0.75rem;font-weight:400"><?= e($lay['estimasi_waktu']) ?></div>
                </th>
                <td class="mono"><?= e(rupiah($lay['harga_per_kg'])) ?></td>
                <td class="mono"><?= (int) $lay['jumlah_order'] ?>x</td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
