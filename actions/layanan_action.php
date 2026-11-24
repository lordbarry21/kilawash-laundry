<?php
/**
 * ============================================================================
 * FILE: actions/layanan_action.php
 * FUNGSI: Endpoint pemrosesan POST untuk CRUD tabel `layanan`:
 *         - action = 'create' -> Tambah jenis layanan baru
 *         - action = 'update' -> Ubah nama, harga/kg, estimasi, dan deskripsi
 *         - action = 'delete' -> Hapus jenis layanan (dilindungi FOREIGN KEY)
 * HAK AKSES: Khusus role `admin` (RBAC backend protection).
 * ============================================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

// 1. Wajib POST, Validasi CSRF, dan Wajib Role Admin
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../layanan.php');
}

require_role('admin', '../layanan.php');
verify_csrf('../layanan.php');

$pdo    = db();
$action = (string) ($_POST['action'] ?? '');

// ----------------------------------------------------------------------------
// 2. CREATE — Tambah Jenis Layanan Baru
// ----------------------------------------------------------------------------
if ($action === 'create') {
    $nama      = trim((string) ($_POST['nama_layanan'] ?? ''));
    $harga     = (int) ($_POST['harga_per_kg'] ?? 0);
    $estimasi  = trim((string) ($_POST['estimasi_waktu'] ?? ''));
    $deskripsi = trim((string) ($_POST['deskripsi'] ?? ''));

    if ($nama === '' || $harga < 1000 || $estimasi === '') {
        set_flash('error', 'Lengkapi nama layanan, tarif minimal Rp 1.000/kg, dan estimasi waktu.');
        redirect('../layanan.php');
    }

    try {
        $stmt = $pdo->prepare(
            'INSERT INTO layanan (nama_layanan, harga_per_kg, estimasi_waktu, deskripsi)
             VALUES (:nama, :harga, :estimasi, :deskripsi)'
        );
        $stmt->execute([
            'nama'      => $nama,
            'harga'     => $harga,
            'estimasi'  => $estimasi,
            'deskripsi' => $deskripsi !== '' ? $deskripsi : null,
        ]);

        set_flash('success', "Layanan \"{$nama}\" (" . rupiah($harga) . "/kg) berhasil ditambahkan.");
    } catch (PDOException $e) {
        if (($e->errorInfo[1] ?? 0) === 1062 || str_contains($e->getMessage(), 'UNIQUE')) {
            set_flash('error', "Nama layanan \"{$nama}\" sudah terdaftar. Gunakan nama yang berbeda.");
        } else {
            set_flash('error', 'Gagal menambahkan layanan: ' . $e->getMessage());
        }
    }

    redirect('../layanan.php');
}

// ----------------------------------------------------------------------------
// 3. UPDATE — Ubah Data Jenis Layanan
// ----------------------------------------------------------------------------
if ($action === 'update') {
    $id        = (int) ($_POST['id'] ?? 0);
    $nama      = trim((string) ($_POST['nama_layanan'] ?? ''));
    $harga     = (int) ($_POST['harga_per_kg'] ?? 0);
    $estimasi  = trim((string) ($_POST['estimasi_waktu'] ?? ''));
    $deskripsi = trim((string) ($_POST['deskripsi'] ?? ''));

    if ($id <= 0 || $nama === '' || $harga < 1000 || $estimasi === '') {
        set_flash('error', 'Data layanan tidak valid. Periksa kembali kolom yang diisi.');
        redirect('../layanan.php');
    }

    try {
        $stmt = $pdo->prepare(
            'UPDATE layanan
             SET nama_layanan = :nama,
                 harga_per_kg = :harga,
                 estimasi_waktu = :estimasi,
                 deskripsi = :deskripsi
             WHERE id = :id'
        );
        $stmt->execute([
            'id'        => $id,
            'nama'      => $nama,
            'harga'     => $harga,
            'estimasi'  => $estimasi,
            'deskripsi' => $deskripsi !== '' ? $deskripsi : null,
        ]);

        set_flash('success', "Data layanan \"{$nama}\" berhasil diperbarui.");
    } catch (PDOException $e) {
        if (($e->errorInfo[1] ?? 0) === 1062 || str_contains($e->getMessage(), 'UNIQUE')) {
            set_flash('error', "Nama layanan \"{$nama}\" sudah digunakan layanan lain.");
        } else {
            set_flash('error', 'Gagal mengubah data layanan: ' . $e->getMessage());
        }
    }

    redirect('../layanan.php');
}

// ----------------------------------------------------------------------------
// 4. DELETE — Hapus Jenis Layanan (Dengan Proteksi Relasi Foreign Key)
// ----------------------------------------------------------------------------
if ($action === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        set_flash('error', 'ID layanan tidak valid.');
        redirect('../layanan.php');
    }

    try {
        $stmt = $pdo->prepare('DELETE FROM layanan WHERE id = :id');
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() > 0) {
            set_flash('success', 'Data layanan berhasil dihapus.');
        } else {
            set_flash('info', 'Data layanan tidak ditemukan.');
        }
    } catch (PDOException $e) {
        // Error code 1451 (MySQL) atau FOREIGN KEY constraint failed (SQLite)
        if (($e->errorInfo[1] ?? 0) === 1451 || str_contains($e->getMessage(), 'FOREIGN KEY')) {
            set_flash(
                'error',
                'Layanan tidak dapat dihapus karena masih berelasi dengan riwayat transaksi laundry.'
            );
        } else {
            set_flash('error', 'Gagal menghapus layanan: ' . $e->getMessage());
        }
    }

    redirect('../layanan.php');
}

set_flash('error', 'Aksi layanan tidak dikenali.');
redirect('../layanan.php');
