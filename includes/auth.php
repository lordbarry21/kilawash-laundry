<?php
/**
 * ============================================================================
 * FILE: includes/auth.php
 * FUNGSI: Manajemen Autentikasi, Sinkronisasi Profil Live dari Database,
 *         Hak Akses Role (Admin/Kasir), dan Proteksi CSRF.
 *         Menggunakan kombinasi PHP Session + HMAC Signed HttpOnly Cookie
 *         agar 100% stabil baik di localhost (Laragon/XAMPP) maupun Vercel.
 * ============================================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/helpers.php';

define('AUTH_COOKIE_NAME', 'kilawash_auth');
define('CSRF_COOKIE_NAME', 'kilawash_csrf');
define('AUTH_SECRET_KEY', getenv('LAUNDRY_APP_SECRET') ?: 'kilawash-asas-rpl-2026-hmac-secret-key');

// 1. Mulai Session PHP Standar
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    @session_start();
}

// Helper internal: Buat & Verifikasi Signed Auth Cookie (HMAC-SHA256)
function set_auth_cookie(int $userId): void
{
    $expires = time() + (60 * 60 * 12); // Berlaku 12 jam
    $data    = $userId . '|' . $expires;
    $sig     = hash_hmac('sha256', $data, AUTH_SECRET_KEY);
    $token   = base64_encode($data . '|' . $sig);

    setcookie(AUTH_COOKIE_NAME, $token, [
        'expires'  => $expires,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    $_COOKIE[AUTH_COOKIE_NAME] = $token;
}

function read_auth_cookie_user_id(): ?int
{
    $raw = $_COOKIE[AUTH_COOKIE_NAME] ?? '';
    if (!is_string($raw) || $raw === '') {
        return null;
    }

    $decoded = base64_decode($raw, true);
    if (!is_string($decoded)) {
        return null;
    }

    $parts = explode('|', $decoded);
    if (count($parts) !== 3) {
        return null;
    }

    [$userIdStr, $expiresStr, $sig] = $parts;
    $expected = hash_hmac('sha256', $userIdStr . '|' . $expiresStr, AUTH_SECRET_KEY);

    if (!hash_equals($expected, $sig) || (int) $expiresStr < time()) {
        return null;
    }

    $userId = (int) $userIdStr;
    return $userId > 0 ? $userId : null;
}

// 2. Ambil Data User Live dari Database (selalu sinkron jika nama/foto berubah!)
function current_user(bool $forceRefresh = false): ?array
{
    static $cachedUser = false;

    if (!$forceRefresh && $cachedUser !== false) {
        return $cachedUser;
    }

    $userId = isset($_SESSION['user']['id']) ? (int) $_SESSION['user']['id'] : read_auth_cookie_user_id();
    if (!$userId) {
        $cachedUser = null;
        return null;
    }

    try {
        $stmt = db()->prepare(
            'SELECT id, nama, username, role, avatar FROM users WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $userId]);
        $row = $stmt->fetch();
    } catch (PDOException) {
        $cachedUser = null;
        return null;
    }

    if (!$row) {
        $cachedUser = null;
        return null;
    }

    $user = [
        'id'       => (int) $row['id'],
        'nama'     => $row['nama'],
        'username' => $row['username'],
        'role'     => $row['role'],
        'avatar'   => $row['avatar'] ?? null,
    ];

    $_SESSION['user'] = $user;
    $cachedUser = $user;
    return $user;
}

function refresh_current_user_session(int $userId): void
{
    set_auth_cookie($userId);
    $_SESSION['user']['id'] = $userId;
    current_user(true);
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function is_admin(): bool
{
    return (current_user()['role'] ?? '') === 'admin';
}

function is_kasir(): bool
{
    return (current_user()['role'] ?? '') === 'kasir';
}

// 3. Proses Login (Verifikasi Username & Password Hash Bcrypt)
function attempt_login(string $username, string $password): bool
{
    $pdo = db();
    $stmt = $pdo->prepare(
        'SELECT id, nama, username, password_hash, role, avatar
         FROM users
         WHERE username = :username
         LIMIT 1'
    );
    $stmt->execute(['username' => trim($username)]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return false;
    }

    @session_regenerate_id(true);
    $userId = (int) $user['id'];
    $_SESSION['user'] = [
        'id'       => $userId,
        'nama'     => $user['nama'],
        'username' => $user['username'],
        'role'     => $user['role'],
        'avatar'   => $user['avatar'] ?? null,
    ];
    set_auth_cookie($userId);
    current_user(true);

    return true;
}

// 4. Logout & Bersihkan Sesi + Signed Cookie
function logout_user(): void
{
    $_SESSION = [];
    setcookie(AUTH_COOKIE_NAME, '', [
        'expires'  => time() - 42000,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    unset($_COOKIE[AUTH_COOKIE_NAME]);

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    @session_destroy();
}

// 5. Penjaga Halaman (Wajib Login)
function require_login(): void
{
    if (!is_logged_in()) {
        set_flash('info', 'Silakan masuk terlebih dahulu untuk mengakses sistem.');
        redirect('login.php');
    }
}

// 6. Penjaga Hak Akses Role (RBAC Backend Protection)
function require_role(string $role, string $fallbackUrl = 'index.php'): void
{
    require_login();
    if ((current_user()['role'] ?? '') !== $role) {
        set_flash('error', 'Akses ditolak. Fitur ini khusus untuk akun ' . ucfirst($role) . '.');
        redirect($fallbackUrl);
    }
}

// 7. Proteksi CSRF Token (Double-Submit Cookie + Session agar bekerja di Vercel Serverless)
function csrf_token(): string
{
    if (!empty($_SESSION['csrf_token']) && is_string($_SESSION['csrf_token'])) {
        return $_SESSION['csrf_token'];
    }

    if (!empty($_COOKIE[CSRF_COOKIE_NAME]) && is_string($_COOKIE[CSRF_COOKIE_NAME])) {
        $_SESSION['csrf_token'] = $_COOKIE[CSRF_COOKIE_NAME];
        return $_COOKIE[CSRF_COOKIE_NAME];
    }

    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    setcookie(CSRF_COOKIE_NAME, $token, [
        'expires'  => time() + (60 * 60 * 12),
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    $_COOKIE[CSRF_COOKIE_NAME] = $token;

    return $token;
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(string $fallbackUrl = 'index.php'): void
{
    $posted   = $_POST['csrf_token'] ?? '';
    $expected = csrf_token();

    if (!is_string($posted) || $posted === '' || !hash_equals($expected, $posted)) {
        set_flash('error', 'Token keamanan form tidak valid. Silakan ulangi aksi Anda.');
        redirect($fallbackUrl);
    }
}
