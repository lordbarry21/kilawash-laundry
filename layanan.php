<?php
/**
 * ============================================================================
 * FILE: layanan.php
 * FUNGSI: Halaman Master Data Layanan (CRUD: Lihat, Tambah, Ubah, Hapus).
 *         - Admin : Akses penuh (Tambah, Ubah, Hapus, Lihat).
 *         - Kasir : Akses Read-Only (Lihat daftar layanan dan tarif aktif).
 * ============================================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_login();

$pdo = db();

// 1. Ambil Seluruh Data Layanan + Hitung Jumlah Transaksi Berelasi (LEFT JOIN)
$layananRows = $pdo->query("
    SELECT
        l.id,
        l.nama_layanan,
        l.harga_per_kg,
        l.estimasi_waktu,
        l.deskripsi,
        l.updated_at,
        COUNT(t.id) AS total_transaksi
    FROM layanan l
    LEFT JOIN transaksi t ON t.layanan_id = l.id
    GROUP BY l.id
    ORDER BY l.harga_per_kg ASC, l.id ASC
")->fetchAll();

$pageTitle  = 'Data Layanan';
$activeMenu = 'layanan';
require __DIR__ . '/includes/layout_top.php';
?>

<!-- 1. Header Halaman -->
<div class="page-head">
  <div>
    <h1 class="page-head__title">Data Layanan Laundry</h1>
    <p class="page-head__desc">
      Kelola jenis layanan cuci (Reguler, Ekspress, Superkilat), tarif per kilogram, dan estimasi waktu pengerjaan.
    </p>
  </div>

  <?php if (is_admin()): ?>
    <button type="button" class="btn btn--primary" data-add-layanan>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      <span>Tambah Layanan</span>
    </button>
  <?php else: ?>
    <span class="badge badge--neutral">Mode Baca Saja (Kasir)</span>
  <?php endif; ?>
</div>

<?php if (!is_admin()): ?>
  <div class="flash flash--info" role="note" style="margin-bottom:0">
    <span>Anda masuk sebagai <strong>Kasir</strong>. Perubahan tarif dan penambahan jenis layanan hanya dapat dilakukan oleh akun <strong>Admin</strong>.</span>
  </div>
<?php endif; ?>

<!-- 2. Tabel Daftar Jenis Layanan -->
<section class="card" aria-labelledby="layanan-table-heading">
  <div class="card__header">
    <div>
      <h2 class="card__title" id="layanan-table-heading">Daftar Jenis Layanan Aktif</h2>
      <p class="card__subtitle">Total <?= count($layananRows) ?> layanan tersimpan di tabel <code>layanan</code></p>
    </div>
  </div>

  <div class="card__body card__body--flush">
    <?php if (empty($layananRows)): ?>
      <div class="empty-state">
        <p class="empty-state__title">Belum ada data layanan</p>
        <p>Tambahkan layanan pertama seperti Reguler, Ekspress, atau Superkilat.</p>
      </div>
    <?php else: ?>
      <div tabindex="0" role="region" aria-labelledby="layanan-table-heading" class="table-wrapper">
        <table class="data-table data-table--responsive">
          <caption class="visually-hidden">Tabel daftar layanan laundry beserta tarif dan estimasi</caption>
          <thead>
            <tr>
              <th scope="col">Nama Layanan</th>
              <th scope="col">Tarif / kg</th>
              <th scope="col">Estimasi Selesai</th>
              <th scope="col">Deskripsi</th>
              <th scope="col">Relasi Order</th>
              <?php if (is_admin()): ?>
                <th scope="col" class="text-right">Aksi</th>
              <?php endif; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($layananRows as $row): ?>
              <tr>
                <th scope="row"><?= e($row['nama_layanan']) ?></th>
                <td data-label="Tarif / kg" class="mono"><?= e(rupiah($row['harga_per_kg'])) ?>/kg</td>
                <td data-label="Estimasi Selesai">
                  <span class="badge badge--accent"><?= e($row['estimasi_waktu']) ?></span>
                </td>
                <td data-label="Deskripsi" class="muted" style="max-width:340px">
                  <?= e($row['deskripsi'] ?: '—') ?>
                </td>
                <td data-label="Relasi Order" class="mono">
                  <?= (int) $row['total_transaksi'] ?> transaksi
                </td>
                <?php if (is_admin()): ?>
                  <td data-label="Aksi" class="cell-actions text-right">
                    <div class="btn-group">
                      <button
                        type="button"
                        class="btn btn--secondary btn--sm"
                        data-edit-layanan
                        data-id="<?= (int) $row['id'] ?>"
                        data-nama="<?= e($row['nama_layanan']) ?>"
                        data-harga="<?= (int) $row['harga_per_kg'] ?>"
                        data-estimasi="<?= e($row['estimasi_waktu']) ?>"
                        data-deskripsi="<?= e($row['deskripsi']) ?>"
                      >
                        Ubah
                      </button>
                      <button
                        type="button"
                        class="btn btn--danger-soft btn--sm"
                        data-delete-layanan
                        data-id="<?= (int) $row['id'] ?>"
                        data-nama="<?= e($row['nama_layanan']) ?>"
                      >
                        Hapus
                      </button>
                    </div>
                  </td>
                <?php endif; ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php if (is_admin()): ?>
  <!-- 3. MODAL TAMBAH / UBAH LAYANAN (Native <dialog> sesuai modern-web-guidance) -->
  <dialog class="modal" id="modal-layanan" closedby="any" aria-labelledby="modal-layanan-title">
    <div class="modal__header">
      <h1 class="modal__title" id="modal-layanan-title">Tambah Jenis Layanan</h1>
      <button type="button" class="modal__close" data-close-modal aria-label="Tutup dialog">&times;</button>
    </div>

    <form id="form-layanan" method="post" action="actions/layanan_action.php">
      <?= csrf_field() ?>
      <input type="hidden" name="action" id="layanan-action" value="create">
      <input type="hidden" name="id" id="layanan-id" value="">

      <div class="modal__body">
        <div class="field">
          <label class="field__label" for="layanan-nama">Nama Jenis Layanan</label>
          <input
            class="field__input"
            type="text"
            id="layanan-nama"
            name="nama_layanan"
            maxlength="80"
            required
            autofocus
            data-error="layanan-nama-error"
            placeholder="Contoh: Reguler, Ekspress, Superkilat"
          >
          <span id="layanan-nama-error" class="error-msg">Nama jenis layanan wajib diisi.</span>
        </div>

        <div class="form-grid">
          <div class="field">
            <label class="field__label" for="layanan-harga">Tarif per Kilogram (Rp)</label>
            <input
              class="field__input mono"
              type="number"
              id="layanan-harga"
              name="harga_per_kg"
              min="1000"
              step="500"
              inputmode="numeric"
              required
              data-error="layanan-harga-error"
              placeholder="Contoh: 7000"
            >
            <span id="layanan-harga-error" class="error-msg">Masukkan tarif minimal Rp 1.000/kg.</span>
          </div>

          <div class="field">
            <label class="field__label" for="layanan-estimasi">Estimasi Waktu</label>
            <input
              class="field__input"
              type="text"
              id="layanan-estimasi"
              name="estimasi_waktu"
              maxlength="60"
              required
              data-error="layanan-estimasi-error"
              placeholder="Contoh: 3-4 hari atau 5 jam selesai"
            >
            <span id="layanan-estimasi-error" class="error-msg">Estimasi waktu pengerjaan wajib diisi.</span>
          </div>
        </div>

        <div class="field">
          <label class="field__label" for="layanan-deskripsi">Deskripsi Layanan (Opsional)</label>
          <textarea
            class="field__textarea"
            id="layanan-deskripsi"
            name="deskripsi"
            rows="3"
            placeholder="Jelaskan cakupan layanan seperti cuci, kering, setrika uap, dan lipat."
          ></textarea>
        </div>
      </div>

      <div class="modal__footer">
        <button type="button" class="btn btn--secondary" data-close-modal>Batal</button>
        <button type="submit" class="btn btn--primary" id="layanan-submit-btn">Simpan Layanan</button>
      </div>
    </form>
  </dialog>

  <!-- 4. MODAL KONFIRMASI HAPUS LAYANAN (autofocus pada tombol aman: Batal) -->
  <dialog class="modal modal--sm" id="modal-hapus-layanan" closedby="any" aria-labelledby="hapus-layanan-title">
    <div class="modal__header">
      <h1 class="modal__title" id="hapus-layanan-title">Hapus Jenis Layanan?</h1>
      <button type="button" class="modal__close" data-close-modal aria-label="Tutup dialog">&times;</button>
    </div>

    <form method="post" action="actions/layanan_action.php">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="id" id="hapus-layanan-id" value="">

      <div class="modal__body">
        <p>
          Anda akan menghapus layanan <strong id="hapus-layanan-nama"></strong>.
          Jika layanan ini sudah digunakan pada riwayat transaksi, penghapusan akan dicegah demi menjaga integritas relasi database.
        </p>
      </div>

      <div class="modal__footer">
        <button type="button" class="btn btn--secondary" data-close-modal autofocus>Batal</button>
        <button type="submit" class="btn btn--danger">Ya, Hapus Layanan</button>
      </div>
    </form>
  </dialog>
<?php endif; ?>

<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
