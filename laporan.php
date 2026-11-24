<?php
/**
 * ============================================================================
 * FILE: laporan.php
 * FUNGSI: Halaman Laporan Keuangan & Rekap Berbasis Relasi Database.
 *         - Menggabungkan tabel `transaksi`, `layanan`, dan `users` (INNER JOIN).
 *         - Menyajikan ringkasan per jenis layanan (GROUP BY) dan rincian order.
 *         - Mendukung filter tanggal, status, layanan, serta cetak laporan.
 * ============================================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_login();

$pdo = db();

// 1. Parameter Filter (GET)
$dari      = trim((string) ($_GET['dari'] ?? ''));
$sampai    = trim((string) ($_GET['sampai'] ?? ''));
$status    = trim((string) ($_GET['status'] ?? ''));
$layananId = (int) ($_GET['layanan_id'] ?? 0);

$where  = [];
$params = [];

if ($dari !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dari)) {
    $where[] = 'DATE(t.tanggal_masuk) >= :dari';
    $params['dari'] = $dari;
}

if ($sampai !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $sampai)) {
    $where[] = 'DATE(t.tanggal_masuk) <= :sampai';
    $params['sampai'] = $sampai;
}

if (in_array($status, TRANSAKSI_STATUSES, true)) {
    $where[] = 't.status = :status';
    $params['status'] = $status;
}

if ($layananId > 0) {
    $where[] = 't.layanan_id = :layanan_id';
    $params['layanan_id'] = $layananId;
}

$whereSql = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';

// 2. Query Ringkasan Total Keuangan Terfilter
$stmtSummary = $pdo->prepare("
    SELECT
        COUNT(*) AS total_order,
        COALESCE(SUM(t.berat_kg), 0) AS total_berat,
        COALESCE(SUM(t.total_harga), 0) AS total_nilai,
        COALESCE(SUM(CASE WHEN t.status = 'Selesai' THEN t.total_harga ELSE 0 END), 0) AS pendapatan_selesai
    FROM transaksi t
    INNER JOIN layanan l ON t.layanan_id = l.id
    {$whereSql}
");
$stmtSummary->execute($params);
$summary = $stmtSummary->fetch();

// 3. Query Rekap Pendapatan per Jenis Layanan (Relasi JOIN + GROUP BY)
$stmtByService = $pdo->prepare("
    SELECT
        l.nama_layanan,
        l.harga_per_kg,
        l.estimasi_waktu,
        COUNT(t.id) AS jumlah_order,
        COALESCE(SUM(t.berat_kg), 0) AS total_berat,
        COALESCE(SUM(t.total_harga), 0) AS total_omzet
    FROM transaksi t
    INNER JOIN layanan l ON t.layanan_id = l.id
    {$whereSql}
    GROUP BY l.id, l.nama_layanan, l.harga_per_kg, l.estimasi_waktu
    ORDER BY total_omzet DESC
");
$stmtByService->execute($params);
$rekapLayanan = $stmtByService->fetchAll();

// 4. Query Rincian Transaksi Berelasi Lengkap
$stmtDetail = $pdo->prepare("
    SELECT
        t.id,
        t.kode_transaksi,
        t.nama_pelanggan,
        t.no_hp,
        t.berat_kg,
        t.harga_satuan,
        t.total_harga,
        t.status,
        t.tanggal_masuk,
        t.tanggal_selesai,
        l.nama_layanan,
        u.nama AS nama_petugas
    FROM transaksi t
    INNER JOIN layanan l ON t.layanan_id = l.id
    INNER JOIN users u   ON t.user_id = u.id
    {$whereSql}
    ORDER BY t.tanggal_masuk DESC, t.id DESC
");
$stmtDetail->execute($params);
$details = $stmtDetail->fetchAll();

// 5. Opsi Filter Jenis Layanan
$layananOptions = $pdo->query(
    'SELECT id, nama_layanan FROM layanan ORDER BY harga_per_kg ASC'
)->fetchAll();

$pageTitle  = 'Laporan Keuangan';
$activeMenu = 'laporan';
require __DIR__ . '/includes/layout_top.php';
?>

<!-- 1. Header Halaman -->
<div class="page-head">
  <div>
    <h1 class="page-head__title">Laporan Keuangan Laundry</h1>
    <p class="page-head__desc">
      Rekapitulasi transaksi dan omzet per jenis layanan berdasarkan relasi tabel <code>transaksi</code> dan <code>layanan</code>.
    </p>
  </div>
  <button type="button" class="btn btn--primary no-print" onclick="window.print()">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
    <span>Cetak Laporan</span>
  </button>
</div>

<!-- 2. Filter Periode & Layanan -->
<section class="card no-print" aria-label="Filter laporan keuangan">
  <form class="filter-bar" method="get" action="laporan.php" style="border-bottom:0;background:transparent">
    <div class="field">
      <label class="field__label" for="lap-dari">Dari Tanggal</label>
      <input class="field__input" type="date" id="lap-dari" name="dari" value="<?= e($dari) ?>">
    </div>

    <div class="field">
      <label class="field__label" for="lap-sampai">Sampai Tanggal</label>
      <input class="field__input" type="date" id="lap-sampai" name="sampai" value="<?= e($sampai) ?>">
    </div>

    <div class="field">
      <label class="field__label" for="lap-layanan">Jenis Layanan</label>
      <select class="field__select" id="lap-layanan" name="layanan_id">
        <option value="0">Semua Layanan</option>
        <?php foreach ($layananOptions as $opt): ?>
          <option value="<?= (int) $opt['id'] ?>"<?= $layananId === (int) $opt['id'] ? ' selected' : '' ?>>
            <?= e($opt['nama_layanan']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="field">
      <label class="field__label" for="lap-status">Status</label>
      <select class="field__select" id="lap-status" name="status">
        <option value="">Semua Status</option>
        <?php foreach (TRANSAKSI_STATUSES as $st): ?>
          <option value="<?= e($st) ?>"<?= $status === $st ? ' selected' : '' ?>><?= e($st) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="btn-group">
      <button type="submit" class="btn btn--secondary btn--sm">Tampilkan</button>
      <?php if ($dari !== '' || $sampai !== '' || $status !== '' || $layananId > 0): ?>
        <a href="laporan.php" class="btn btn--ghost btn--sm">Reset</a>
      <?php endif; ?>
    </div>
  </form>
</section>

<!-- 3. Ringkasan 4 KPI Keuangan -->
<section class="kpi-grid" aria-label="Ringkasan laporan keuangan">
  <article class="kpi-card">
    <span class="kpi-card__label">Jumlah Order</span>
    <p class="kpi-card__value mono"><?= (int) ($summary['total_order'] ?? 0) ?></p>
    <p class="kpi-card__meta">Transaksi sesuai filter</p>
  </article>

  <article class="kpi-card">
    <span class="kpi-card__label">Total Berat Cucian</span>
    <p class="kpi-card__value mono"><?= e(format_kg($summary['total_berat'] ?? 0)) ?></p>
    <p class="kpi-card__meta">Akumulasi kilogram diproses</p>
  </article>

  <article class="kpi-card">
    <span class="kpi-card__label">Pendapatan Selesai</span>
    <p class="kpi-card__value mono"><?= e(rupiah($summary['pendapatan_selesai'] ?? 0)) ?></p>
    <p class="kpi-card__meta">Dari order berstatus Selesai</p>
  </article>

  <article class="kpi-card">
    <span class="kpi-card__label">Total Nilai Order</span>
    <p class="kpi-card__value mono"><?= e(rupiah($summary['total_nilai'] ?? 0)) ?></p>
    <p class="kpi-card__meta">Termasuk antrean berjalan</p>
  </article>
</section>

<!-- 4. Tabel Rekap per Jenis Layanan (GROUP BY) -->
<section class="card" aria-labelledby="rekap-layanan-heading">
  <div class="card__header">
    <div>
      <h2 class="card__title" id="rekap-layanan-heading">Rekap Pendapatan per Jenis Layanan</h2>
      <p class="card__subtitle">Dihitung langsung melalui relasi <code>INNER JOIN</code> dan <code>GROUP BY layanan.id</code></p>
    </div>
  </div>
  <div class="card__body card__body--flush">
    <?php if (empty($rekapLayanan)): ?>
      <div class="empty-state">
        <p class="empty-state__title">Belum ada data pada periode ini</p>
      </div>
    <?php else: ?>
      <div tabindex="0" role="region" aria-labelledby="rekap-layanan-heading" class="table-wrapper">
        <table class="data-table data-table--responsive">
          <caption class="visually-hidden">Rekap omzet berdasarkan jenis layanan laundry</caption>
          <thead>
            <tr>
              <th scope="col">Jenis Layanan</th>
              <th scope="col">Tarif Aktif</th>
              <th scope="col">Estimasi</th>
              <th scope="col">Jumlah Order</th>
              <th scope="col">Total Berat</th>
              <th scope="col" class="text-right">Total Omzet</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rekapLayanan as $rk): ?>
              <tr>
                <th scope="row"><?= e($rk['nama_layanan']) ?></th>
                <td data-label="Tarif Aktif" class="mono"><?= e(rupiah($rk['harga_per_kg'])) ?>/kg</td>
                <td data-label="Estimasi"><?= e($rk['estimasi_waktu']) ?></td>
                <td data-label="Jumlah Order" class="mono"><?= (int) $rk['jumlah_order'] ?> order</td>
                <td data-label="Total Berat" class="mono"><?= e(format_kg($rk['total_berat'])) ?></td>
                <td data-label="Total Omzet" class="mono text-right" style="font-weight:500">
                  <?= e(rupiah($rk['total_omzet'])) ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- 5. Tabel Rincian Transaksi Berelasi -->
<section class="card" aria-labelledby="detail-laporan-heading">
  <div class="card__header">
    <div>
      <h2 class="card__title" id="detail-laporan-heading">Rincian Transaksi Keuangan</h2>
      <p class="card__subtitle">Data lengkap relasi pelanggan, jenis layanan, petugas pencatat, dan status akhir</p>
    </div>
  </div>
  <div class="card__body card__body--flush">
    <?php if (empty($details)): ?>
      <div class="empty-state">
        <p class="empty-state__title">Tidak ada transaksi sesuai filter</p>
      </div>
    <?php else: ?>
      <div tabindex="0" role="region" aria-labelledby="detail-laporan-heading" class="table-wrapper">
        <table class="data-table data-table--responsive">
          <caption class="visually-hidden">Rincian transaksi keuangan laundry</caption>
          <thead>
            <tr>
              <th scope="col">Kode Transaksi</th>
              <th scope="col">Tanggal Masuk</th>
              <th scope="col">Pelanggan</th>
              <th scope="col">Layanan</th>
              <th scope="col">Petugas</th>
              <th scope="col">Berat</th>
              <th scope="col">Status</th>
              <th scope="col" class="text-right">Total</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($details as $d): ?>
              <tr>
                <th scope="row" class="mono"><?= e($d['kode_transaksi']) ?></th>
                <td data-label="Tanggal Masuk"><?= e(format_tanggal($d['tanggal_masuk'])) ?></td>
                <td data-label="Pelanggan">
                  <div><?= e($d['nama_pelanggan']) ?></div>
                  <div class="muted mono" style="font-size:0.75rem"><?= e($d['no_hp']) ?></div>
                </td>
                <td data-label="Layanan"><?= e($d['nama_layanan']) ?></td>
                <td data-label="Petugas"><?= e($d['nama_petugas']) ?></td>
                <td data-label="Berat" class="mono"><?= e(format_kg($d['berat_kg'])) ?></td>
                <td data-label="Status">
                  <span class="badge <?= e(status_badge_class($d['status'])) ?>"><?= e($d['status']) ?></span>
                </td>
                <td data-label="Total" class="mono text-right" style="font-weight:500">
                  <?= e(rupiah($d['total_harga'])) ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
