<?php
/**
 * ============================================================================
 * FILE: actions/profil_action.php
 * FUNGSI: Endpoint pemrosesan POST untuk Pengaturan Akun pengguna:
 *         - action = 'update_profile'  -> Ubah nama, username, dan foto profil
 *         - action = 'update_password' -> Verifikasi sandi lama & simpan hash baru
 * ============================================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../profil.php');
}

require_login();
verify_csrf('../profil.php');

$pdo    = db();
$user   = current_user();
$userId = (int) ($user['id'] ?? 0);
$action = (string) ($_POST['action'] ?? '');

// ----------------------------------------------------------------------------
// 1. UPDATE PROFILE (Nama, Username, dan Foto Profil)
// ----------------------------------------------------------------------------
if ($action === 'update_profile') {
    $nama     = trim((string) ($_POST['nama'] ?? ''));
    $username = trim((string) ($_POST['username'] ?? ''));

    if ($nama === '' || !preg_match('/^[a-zA-Z0-9._-]{3,50}$/', $username)) {
        set_flash('error', 'Nama wajib diisi dan username harus 3–50 karakter tanpa spasi.');
        redirect('../profil.php');
    }

    $avatarData = $user['avatar'] ?? null;

    // Hapus foto profil jika tombol Hapus Foto ditekan
    if (isset($_POST['remove_avatar']) && $_POST['remove_avatar'] === '1') {
        $avatarData = null;
    } elseif (isset($_FILES['avatar']) && ($_FILES['avatar']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['avatar'];
        if (($file['error'] ?? 0) !== UPLOAD_ERR_OK) {
            set_flash('error', 'Gagal mengunggah foto profil. Coba file lain.');
            redirect('../profil.php');
        }

        if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
            set_flash('error', 'Ukuran foto maksimal 2 MB.');
            redirect('../profil.php');
        }

        $tmpPath = (string) ($file['tmp_name'] ?? '');
        $mime    = mime_content_type($tmpPath) ?: '';
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($mime, $allowed, true)) {
            set_flash('error', 'Format foto harus JPG, PNG, atau WebP.');
            redirect('../profil.php');
        }

        $raw = file_get_contents($tmpPath);
        if ($raw === false) {
            set_flash('error', 'Gagal membaca file gambar.');
            redirect('../profil.php');
        }

        $avatarData = 'data:' . $mime . ';base64,' . base64_encode($raw);
    }

    try {
        $stmt = $pdo->prepare(
            'UPDATE users
             SET nama = :nama, username = :username, avatar = :avatar
             WHERE id = :id'
        );
        $stmt->execute([
            'nama'     => $nama,
            'username' => $username,
            'avatar'   => $avatarData,
            'id'       => $userId,
        ]);

        db_sync_if_cloud();
        refresh_current_user_session($userId);
        set_flash('success', 'Profil akun berhasil diperbarui.');
    } catch (PDOException $e) {
        if (($e->errorInfo[1] ?? 0) === 1062 || str_contains($e->getMessage(), 'UNIQUE')) {
            set_flash('error', "Username \"{$username}\" sudah dipakai akun lain.");
        } else {
            set_flash('error', 'Gagal memperbarui profil: ' . $e->getMessage());
        }
    }

    redirect('../profil.php');
}

// ----------------------------------------------------------------------------
// 2. UPDATE PASSWORD (Cek Sandi Lama & Simpan Hash Bcrypt Baru)
// ----------------------------------------------------------------------------
if ($action === 'update_password') {
    $currentPass = (string) ($_POST['current_password'] ?? '');
    $newPass     = (string) ($_POST['new_password'] ?? '');
    $confirmPass = (string) ($_POST['confirm_password'] ?? '');

    if ($currentPass === '' || strlen($newPass) < 6) {
        set_flash('error', 'Masukkan kata sandi lama dan kata sandi baru minimal 6 karakter.');
        redirect('../profil.php');
    }

    if ($newPass !== $confirmPass) {
        set_flash('error', 'Konfirmasi kata sandi baru tidak cocok.');
        redirect('../profil.php');
    }

    $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $userId]);
    $hash = $stmt->fetchColumn();

    if (!is_string($hash) || !password_verify($currentPass, $hash)) {
        set_flash('error', 'Kata sandi saat ini salah. Perubahan dibatalkan.');
        redirect('../profil.php');
    }

    $newHash = password_hash($newPass, PASSWORD_BCRYPT);
    $stmtUpd = $pdo->prepare('UPDATE users SET password_hash = :hash WHERE id = :id');
    $stmtUpd->execute([
        'hash' => $newHash,
        'id'   => $userId,
    ]);

    db_sync_if_cloud();
    set_flash('success', 'Kata sandi berhasil diperbarui.');
    redirect('../profil.php');
}

set_flash('error', 'Aksi profil tidak dikenali.');
redirect('../profil.php');
