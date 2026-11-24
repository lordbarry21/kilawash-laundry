<?php
/**
 * ============================================================================
 * FILE: struk.php
 * FUNGSI: Halaman Nota / Struk Transaksi Laundry siap cetak (window.print).
 *         Mengambil data lengkap transaksi melalui SQL INNER JOIN.
 * ============================================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_login();

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    set_flash('error', 'Transaksi tidak ditemukan.');
    redirect('transaksi.php');
}

$pdo = db();
$stmt = $pdo->prepare("
    SELECT
        t.*,
        l.nama_layanan,
        l.estimasi_waktu,
        u.nama AS nama_petugas
    FROM transaksi t
    INNER JOIN layanan l ON t.layanan_id = l.id
    INNER JOIN users u   ON t.user_id = u.id
    WHERE t.id = :id
    LIMIT 1
");
$stmt->execute(['id' => $id]);
$trx = $stmt->fetch();

if (!$trx) {
    set_flash('error', 'Data transaksi tidak ditemukan.');
    redirect('transaksi.php');
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Struk <?= e($trx['kode_transaksi']) ?> — <?= e(APP_NAME) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Host+Grotesk:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/tokens.css">
  <link rel="stylesheet" href="assets/css/components.css">
  <link rel="stylesheet" href="assets/css/layout.css">
</head>
<body>
  <main class="receipt-shell">
    <div class="btn-group no-print">
      <a href="transaksi.php" class="btn btn--secondary">Kembali ke Rekap</a>
      <button type="button" class="btn btn--primary" onclick="window.print()">Cetak Struk</button>
    </div>

    <article class="receipt-card" aria-label="Nota transaksi laundry">
      <header class="receipt-head">
        <h1 style="font-size:1.375rem"><?= e(APP_NAME) ?></h1>
        <p class="muted" style="font-size:0.8125rem;margin-top:2px"><?= e(APP_ADDRESS) ?></p>
        <p class="mono" style="font-size:0.875rem;margin-top:8px;font-weight:500"><?= e($trx['kode_transaksi']) ?></p>
      </header>

      <div class="receipt-rows">
        <div class="receipt-row">
          <span class="muted">Pelanggan</span>
          <strong><?= e($trx['nama_pelanggan']) ?></strong>
        </div>
        <div class="receipt-row">
          <span class="muted">No. HP</span>
          <span class="mono"><?= e($trx['no_hp']) ?></span>
        </div>
        <div class="receipt-row">
          <span class="muted">Tanggal Masuk</span>
          <span><?= e(format_tanggal($trx['tanggal_masuk'])) ?></span>
        </div>
        <div class="receipt-row">
          <span class="muted">Status</span>
          <span class="badge <?= e(status_badge_class($trx['status'])) ?>"><?= e($trx['status']) ?></span>
        </div>
        <?php if ($trx['tanggal_selesai']): ?>
          <div class="receipt-row">
            <span class="muted">Tanggal Selesai</span>
            <span><?= e(format_tanggal($trx['tanggal_selesai'])) ?></span>
          </div>
        <?php endif; ?>
      </div>

      <div class="receipt-rows" style="padding-top:12px;border-top:1px dashed var(--hairline-strong)">
        <div class="receipt-row">
          <span class="muted">Jenis Layanan</span>
          <strong><?= e($trx['nama_layanan']) ?></strong>
        </div>
        <div class="receipt-row">
          <span class="muted">Estimasi</span>
          <span><?= e($trx['estimasi_waktu']) ?></span>
        </div>
        <div class="receipt-row">
          <span class="muted">Berat × Tarif</span>
          <span class="mono"><?= e(format_kg($trx['berat_kg'])) ?> × <?= e(rupiah($trx['harga_satuan'])) ?></span>
        </div>
        <?php if (!empty($trx['catatan'])): ?>
          <div class="receipt-row">
            <span class="muted">Catatan</span>
            <span><?= e($trx['catatan']) ?></span>
          </div>
        <?php endif; ?>
        <div class="receipt-row">
          <span class="muted">Petugas</span>
          <span><?= e($trx['nama_petugas']) ?></span>
        </div>
      </div>

      <div class="receipt-total">
        <span>Total Pembayaran</span>
        <span class="mono"><?= e(rupiah($trx['total_harga'])) ?></span>
      </div>

      <p class="muted" style="text-align:center;font-size:0.75rem">
        Terima kasih telah mempercayakan cucian Anda kepada <?= e(APP_NAME) ?>.
      </p>
    </article>
  </main>
</body>
</html>
