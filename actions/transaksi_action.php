<?php
/**
 * ============================================================================
 * FILE: actions/transaksi_action.php
 * FUNGSI: Endpoint pemrosesan POST untuk tabel `transaksi`:
 *         - action = 'create'        -> Catat transaksi baru + hitung tarif server-side
 *         - action = 'update_status' -> Ubah status pengerjaan (Masuk -> Proses -> Selesai)
 *         - action = 'delete'        -> Hapus transaksi (Khusus Admin)
 * ============================================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../transaksi.php');
}

require_login();
verify_csrf('../transaksi.php');

$pdo    = db();
$action = (string) ($_POST['action'] ?? '');

// ----------------------------------------------------------------------------
// 1. CREATE — Pencatatan Transaksi Baru + Perhitungan Tarif Otomatis di Server
// ----------------------------------------------------------------------------
if ($action === 'create') {
    $namaPelanggan = trim((string) ($_POST['nama_pelanggan'] ?? ''));
    $noHp          = trim((string) ($_POST['no_hp'] ?? ''));
    $layananId     = (int) ($_POST['layanan_id'] ?? 0);
    $beratKg       = (float) ($_POST['berat_kg'] ?? 0);
    $status        = (string) ($_POST['status'] ?? 'Proses');
    $catatan       = trim((string) ($_POST['catatan'] ?? ''));

    if (!in_array($status, ['Masuk', 'Proses'], true)) {
        $status = 'Proses';
    }

    if ($namaPelanggan === '' || $noHp === '' || $layananId <= 0 || $beratKg < 0.5) {
        set_flash('error', 'Lengkapi nama pelanggan, nomor HP, jenis layanan, dan berat minimal 0,5 kg.');
        redirect('../transaksi.php');
    }

    // Ambil harga asli dari tabel `layanan` (menjamin integritas relasi & akurasi tarif)
    $stmtLayanan = $pdo->prepare('SELECT id, nama_layanan, harga_per_kg FROM layanan WHERE id = :id LIMIT 1');
    $stmtLayanan->execute(['id' => $layananId]);
    $layanan = $stmtLayanan->fetch();

    if (!$layanan) {
        set_flash('error', 'Jenis layanan yang dipilih tidak ditemukan.');
        redirect('../transaksi.php');
    }

    $hargaSatuan   = (int) $layanan['harga_per_kg'];
    $totalHarga    = (int) round($beratKg * $hargaSatuan);
    $kodeTransaksi = generate_kode_transaksi($pdo);
    $userId        = (int) (current_user()['id'] ?? 1);

    $stmtInsert = $pdo->prepare(
        'INSERT INTO transaksi
            (kode_transaksi, nama_pelanggan, no_hp, layanan_id, user_id, berat_kg, harga_satuan, total_harga, status, catatan)
         VALUES
            (:kode, :nama, :hp, :layanan_id, :user_id, :berat, :harga_satuan, :total, :status, :catatan)'
    );

    $stmtInsert->execute([
        'kode'         => $kodeTransaksi,
        'nama'         => $namaPelanggan,
        'hp'           => $noHp,
        'layanan_id'   => $layananId,
        'user_id'      => $userId,
        'berat'        => $beratKg,
        'harga_satuan' => $hargaSatuan,
        'total'        => $totalHarga,
        'status'       => $status,
        'catatan'      => $catatan !== '' ? $catatan : null,
    ]);

    db_sync_if_cloud();
    set_flash(
        'success',
        "Transaksi {$kodeTransaksi} atas nama {$namaPelanggan} (" . $layanan['nama_layanan'] . ', ' . rupiah($totalHarga) . ') berhasil dicatat.'
    );
    redirect('../transaksi.php');
}

// ----------------------------------------------------------------------------
// 2. UPDATE STATUS — Perubahan Status Pengerjaan (Masuk -> Proses -> Selesai)
// ----------------------------------------------------------------------------
if ($action === 'update_status') {
    $id        = (int) ($_POST['id'] ?? 0);
    $newStatus = (string) ($_POST['status'] ?? '');

    if ($id <= 0 || !in_array($newStatus, TRANSAKSI_STATUSES, true)) {
        set_flash('error', 'Permintaan pembaruan status tidak valid.');
        redirect('../transaksi.php');
    }

    if ($newStatus === 'Selesai') {
        $stmt = $pdo->prepare(
            'UPDATE transaksi
             SET status = :status, tanggal_selesai = :selesai
             WHERE id = :id'
        );
        $stmt->execute([
            'status'  => $newStatus,
            'selesai' => date('Y-m-d H:i:s'),
            'id'      => $id,
        ]);
    } else {
        $stmt = $pdo->prepare(
            'UPDATE transaksi
             SET status = :status, tanggal_selesai = NULL
             WHERE id = :id'
        );
        $stmt->execute([
            'status' => $newStatus,
            'id'     => $id,
        ]);
    }

    db_sync_if_cloud();
    set_flash('success', "Status transaksi berhasil diperbarui menjadi \"{$newStatus}\".");
    redirect('../transaksi.php');
}

// ----------------------------------------------------------------------------
// 3. DELETE — Hapus Transaksi (Khusus Role Admin)
// ----------------------------------------------------------------------------
if ($action === 'delete') {
    require_role('admin', '../transaksi.php');

    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        set_flash('error', 'ID transaksi tidak valid.');
        redirect('../transaksi.php');
    }

    $stmt = $pdo->prepare('DELETE FROM transaksi WHERE id = :id');
    $stmt->execute(['id' => $id]);

    db_sync_if_cloud();
    set_flash('success', 'Data transaksi berhasil dihapus.');
    redirect('../transaksi.php');
}

set_flash('error', 'Aksi transaksi tidak dikenali.');
redirect('../transaksi.php');
