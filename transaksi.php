<?php
/**
 * ============================================================================
 * FILE: transaksi.php
 * FUNGSI: Halaman Rekap & Pencatatan Transaksi Laundry.
 *         - Pencatatan transaksi baru (otomatis terhubung ke tabel `layanan`
 *           dan menghitung total tarif = berat_kg × harga_per_kg).
 *         - Pencarian & filter berdasarkan status dan jenis layanan.
 *         - Update status pengerjaan 1-klik: Masuk -> Proses -> Selesai.
 *         - Cetak struk/nota (`struk.php?id=...`).
 * ============================================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_login();

$pdo = db();

// 1. Ambil Parameter Filter dari Query String (GET)
$search     = trim((string) ($_GET['q'] ?? ''));
$statusFilt = trim((string) ($_GET['status'] ?? ''));
$layananId  = (int) ($_GET['layanan_id'] ?? 0);
$openNew    = isset($_GET['new']) && $_GET['new'] === '1';

// 2. Susun Query Transaksi dengan INNER JOIN ke `layanan` dan `users`
$where  = [];
$params = [];

if ($search !== '') {
    $where[] = '(t.kode_transaksi LIKE :q1 OR t.nama_pelanggan LIKE :q2 OR t.no_hp LIKE :q3)';
    $like = '%' . $search . '%';
    $params['q1'] = $like;
    $params['q2'] = $like;
    $params['q3'] = $like;
}

if (in_array($statusFilt, TRANSAKSI_STATUSES, true)) {
    $where[] = 't.status = :status';
    $params['status'] = $statusFilt;
}

if ($layananId > 0) {
    $where[] = 't.layanan_id = :layanan_id';
    $params['layanan_id'] = $layananId;
}

$sql = "
    SELECT
        t.id,
        t.kode_transaksi,
        t.nama_pelanggan,
        t.no_hp,
        t.berat_kg,
        t.harga_satuan,
        t.total_harga,
        t.status,
        t.catatan,
        t.tanggal_masuk,
        t.tanggal_selesai,
        l.nama_layanan,
        l.estimasi_waktu,
        u.nama AS nama_petugas
    FROM transaksi t
    INNER JOIN layanan l ON t.layanan_id = l.id
    INNER JOIN users u   ON t.user_id = u.id
";

if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$sql .= ' ORDER BY t.tanggal_masuk DESC, t.id DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$transactions = $stmt->fetchAll();

// 3. Ambil Master Layanan untuk Dropdown Filter & Form Input Transaksi
$layananOptions = $pdo->query(
    'SELECT id, nama_layanan, harga_per_kg, estimasi_waktu FROM layanan ORDER BY harga_per_kg ASC'
)->fetchAll();

$pageTitle  = 'Rekap Transaksi Laundry';
$activeMenu = 'transaksi';
require __DIR__ . '/includes/layout_top.php';
?>

<!-- 1. Header Halaman -->
<div class="page-head">
  <div>
    <h1 class="page-head__title">Rekap Transaksi Laundry</h1>
    <p class="page-head__desc">Catat cucian masuk, hitung tarif otomatis dari tabel layanan, dan perbarui status pengerjaan.</p>
  </div>

  <button type="button" class="btn btn--primary" data-open-modal="modal-transaksi">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    <span>Catat Transaksi Baru</span>
  </button>
</div>

<!-- 2. Kartu Filter & Tabel Transaksi -->
<section class="card" aria-labelledby="trx-table-heading">
  <div class="card__header">
    <div>
      <h2 class="card__title" id="trx-table-heading">Daftar Transaksi Pelanggan</h2>
      <p class="card__subtitle">Menampilkan <?= count($transactions) ?> transaksi (relasi <code>transaksi.layanan_id → layanan.id</code>)</p>
    </div>
  </div>

  <!-- Baris Filter Pencarian -->
  <form class="filter-bar" method="get" action="transaksi.php">
    <div class="field">
      <label class="field__label" for="filter-q">Cari Pelanggan / Kode / HP</label>
      <input
        class="field__input"
        type="search"
        id="filter-q"
        name="q"
        value="<?= e($search) ?>"
        placeholder="Ketik nama, nomor HP, atau kode..."
      >
    </div>

    <div class="field">
      <label class="field__label" for="filter-status">Status Pengerjaan</label>
      <select class="field__select" id="filter-status" name="status">
        <option value="">Semua Status</option>
        <?php foreach (TRANSAKSI_STATUSES as $st): ?>
          <option value="<?= e($st) ?>"<?= $statusFilt === $st ? ' selected' : '' ?>><?= e($st) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="field">
      <label class="field__label" for="filter-layanan">Jenis Layanan</label>
      <select class="field__select" id="filter-layanan" name="layanan_id">
        <option value="0">Semua Layanan</option>
        <?php foreach ($layananOptions as $opt): ?>
          <option value="<?= (int) $opt['id'] ?>"<?= $layananId === (int) $opt['id'] ? ' selected' : '' ?>>
            <?= e($opt['nama_layanan']) ?> (<?= e(rupiah($opt['harga_per_kg'])) ?>/kg)
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="btn-group">
      <button type="submit" class="btn btn--secondary btn--sm">Terapkan Filter</button>
      <?php if ($search !== '' || $statusFilt !== '' || $layananId > 0): ?>
        <a href="transaksi.php" class="btn btn--ghost btn--sm">Reset</a>
      <?php endif; ?>
    </div>
  </form>

  <!-- Tabel Data Transaksi -->
  <div class="card__body card__body--flush">
    <?php if (empty($transactions)): ?>
      <div class="empty-state">
        <p class="empty-state__title">Tidak ada transaksi ditemukan</p>
        <p>Ubah filter pencarian atau catat transaksi laundry baru.</p>
      </div>
    <?php else: ?>
      <div tabindex="0" role="region" aria-labelledby="trx-table-heading" class="table-wrapper">
        <table class="data-table data-table--responsive">
          <caption class="visually-hidden">Daftar rekap transaksi laundry pelanggan</caption>
          <thead>
            <tr>
              <th scope="col">Kode &amp; Pelanggan</th>
              <th scope="col">No. HP</th>
              <th scope="col">Layanan</th>
              <th scope="col">Berat</th>
              <th scope="col">Total Tarif</th>
              <th scope="col">Status</th>
              <th scope="col" class="text-right">Aksi Pengerjaan</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($transactions as $trx): ?>
              <tr>
                <th scope="row">
                  <div><?= e($trx['nama_pelanggan']) ?></div>
                  <div class="muted mono" style="font-size:0.75rem;font-weight:400">
                    <?= e($trx['kode_transaksi']) ?> • <?= e(format_tanggal($trx['tanggal_masuk'])) ?>
                  </div>
                </th>
                <td data-label="No. HP" class="mono"><?= e($trx['no_hp']) ?></td>
                <td data-label="Layanan">
                  <div><?= e($trx['nama_layanan']) ?></div>
                  <div class="muted mono" style="font-size:0.75rem"><?= e(rupiah($trx['harga_satuan'])) ?>/kg</div>
                </td>
                <td data-label="Berat" class="mono"><?= e(format_kg($trx['berat_kg'])) ?></td>
                <td data-label="Total Tarif" class="mono" style="font-weight:500">
                  <?= e(rupiah($trx['total_harga'])) ?>
                </td>
                <td data-label="Status">
                  <span class="badge <?= e(status_badge_class($trx['status'])) ?>">
                    <?= e($trx['status']) ?>
                  </span>
                  <?php if ($trx['status'] === 'Selesai' && $trx['tanggal_selesai']): ?>
                    <div class="muted" style="font-size:0.75rem;margin-top:3px">
                      <?= e(format_tanggal($trx['tanggal_selesai'])) ?>
                    </div>
                  <?php endif; ?>
                </td>
                <td data-label="Aksi Pengerjaan" class="cell-actions text-right">
                  <div class="btn-group">
                    <?php if ($trx['status'] === 'Masuk'): ?>
                      <form method="post" action="actions/transaksi_action.php" style="display:inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?= (int) $trx['id'] ?>">
                        <input type="hidden" name="status" value="Proses">
                        <button type="submit" class="btn btn--secondary btn--sm">Mulai Proses</button>
                      </form>
                    <?php elseif ($trx['status'] === 'Proses'): ?>
                      <form method="post" action="actions/transaksi_action.php" style="display:inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?= (int) $trx['id'] ?>">
                        <input type="hidden" name="status" value="Selesai">
                        <button type="submit" class="btn btn--success-soft btn--sm">Tandai Selesai</button>
                      </form>
                    <?php endif; ?>

                    <a href="struk.php?id=<?= (int) $trx['id'] ?>" class="btn btn--secondary btn--sm">Struk</a>

                    <?php if (is_admin()): ?>
                      <button
                        type="button"
                        class="btn btn--danger-soft btn--sm"
                        data-delete-transaksi
                        data-id="<?= (int) $trx['id'] ?>"
                        data-kode="<?= e($trx['kode_transaksi']) ?>"
                      >
                        Hapus
                      </button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- 3. MODAL CATAT TRANSAKSI BARU (Native <dialog> dengan Live Kalkulator Tarif) -->
<dialog class="modal" id="modal-transaksi" closedby="any" aria-labelledby="modal-transaksi-title">
  <div class="modal__header">
    <h1 class="modal__title" id="modal-transaksi-title">Catat Transaksi Laundry Baru</h1>
    <button type="button" class="modal__close" data-close-modal aria-label="Tutup dialog">&times;</button>
  </div>

  <form method="post" action="actions/transaksi_action.php">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="create">

    <div class="modal__body">
      <div class="form-grid">
        <div class="field">
          <label class="field__label" for="trx-nama">Nama Pelanggan</label>
          <input
            class="field__input"
            type="text"
            id="trx-nama"
            name="nama_pelanggan"
            autocomplete="name"
            maxlength="120"
            required
            autofocus
            data-error="trx-nama-error"
            placeholder="Contoh: Budi Santoso"
          >
          <span id="trx-nama-error" class="error-msg">Nama pelanggan wajib diisi.</span>
        </div>

        <div class="field">
          <label class="field__label" for="trx-hp">Nomor HP / WhatsApp</label>
          <input
            class="field__input mono"
            type="tel"
            id="trx-hp"
            name="no_hp"
            autocomplete="tel"
            inputmode="tel"
            pattern="[0-9+\-\s]{8,20}"
            required
            data-error="trx-hp-error"
            placeholder="081234567890"
          >
          <span id="trx-hp-error" class="error-msg">Masukkan nomor HP yang valid (8–20 digit).</span>
        </div>
      </div>

      <div class="form-grid">
        <div class="field">
          <label class="field__label" for="trx-layanan">Jenis Layanan</label>
          <select
            class="field__select"
            id="trx-layanan"
            name="layanan_id"
            required
            data-error="trx-layanan-error"
          >
            <option value="">-- Pilih Jenis Layanan --</option>
            <?php foreach ($layananOptions as $opt): ?>
              <option
                value="<?= (int) $opt['id'] ?>"
                data-harga="<?= (int) $opt['harga_per_kg'] ?>"
                data-estimasi="<?= e($opt['estimasi_waktu']) ?>"
              >
                <?= e($opt['nama_layanan']) ?> — <?= e(rupiah($opt['harga_per_kg'])) ?>/kg (<?= e($opt['estimasi_waktu']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
          <span id="trx-layanan-error" class="error-msg">Pilih salah satu jenis layanan laundry.</span>
        </div>

        <div class="field">
          <label class="field__label" for="trx-berat">Berat Cucian (kg)</label>
          <input
            class="field__input mono"
            type="number"
            id="trx-berat"
            name="berat_kg"
            min="0.5"
            max="500"
            step="0.5"
            value="2"
            inputmode="decimal"
            required
            data-error="trx-berat-error"
          >
          <span id="trx-berat-error" class="error-msg">Masukkan berat minimal 0,5 kg.</span>
        </div>
      </div>

      <!-- Pratinjau Perhitungan Tarif Otomatis -->
      <div class="tariff-preview" aria-live="polite">
        <div>
          <p class="tariff-preview__label">Estimasi Total Tarif</p>
          <p class="tariff-preview__meta" id="trx-preview-meta">Pilih jenis layanan dan masukkan berat cucian</p>
        </div>
        <p class="tariff-preview__total mono" id="trx-preview-total">Rp 0</p>
      </div>

      <div class="form-grid">
        <div class="field">
          <label class="field__label" for="trx-status">Status Awal Pengerjaan</label>
          <select class="field__select" id="trx-status" name="status">
            <option value="Proses" selected>Proses (Langsung Dikerjakan)</option>
            <option value="Masuk">Masuk (Antrean Baru)</option>
          </select>
        </div>

        <div class="field">
          <label class="field__label" for="trx-catatan">Catatan Khusus (Opsional)</label>
          <input
            class="field__input"
            type="text"
            id="trx-catatan"
            name="catatan"
            maxlength="255"
            placeholder="Contoh: Pisahkan baju putih"
          >
        </div>
      </div>
    </div>

    <div class="modal__footer">
      <button type="button" class="btn btn--secondary" data-close-modal>Batal</button>
      <button type="submit" class="btn btn--primary">Simpan Transaksi</button>
    </div>
  </form>
</dialog>

<?php if (is_admin()): ?>
  <!-- 4. MODAL KONFIRMASI HAPUS TRANSAKSI (Khusus Admin) -->
  <dialog class="modal modal--sm" id="modal-hapus-transaksi" closedby="any" aria-labelledby="hapus-trx-title">
    <div class="modal__header">
      <h1 class="modal__title" id="hapus-trx-title">Hapus Data Transaksi?</h1>
      <button type="button" class="modal__close" data-close-modal aria-label="Tutup dialog">&times;</button>
    </div>

    <form method="post" action="actions/transaksi_action.php">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="id" id="hapus-transaksi-id" value="">

      <div class="modal__body">
        <p>Transaksi dengan kode <strong class="mono" id="hapus-transaksi-kode"></strong> akan dihapus permanen dari database.</p>
      </div>

      <div class="modal__footer">
        <button type="button" class="btn btn--secondary" data-close-modal autofocus>Batal</button>
        <button type="submit" class="btn btn--danger">Ya, Hapus Transaksi</button>
      </div>
    </form>
  </dialog>
<?php endif; ?>

<?php if ($openNew): ?>
  <script>
    window.addEventListener('DOMContentLoaded', () => {
      document.getElementById('modal-transaksi')?.showModal();
    });
  </script>
<?php endif; ?>

<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
