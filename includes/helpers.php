<?php
/**
 * ============================================================================
 * FILE: includes/helpers.php
 * FUNGSI: Fungsi bantuan global untuk tampilan, format angka/tanggal,
 *         notifikasi flash message, keamanan XSS, dan generator kode order.
 * ============================================================================
 */

declare(strict_types=1);

// 1. Escape Output HTML (Anti-XSS)
function e(?string $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// 2. Format Mata Uang Rupiah (Contoh: Rp 12.000)
function rupiah(int|float|string $amount): string
{
    return 'Rp ' . number_format((float) $amount, 0, ',', '.');
}

// 3. Format Berat Kilogram (Contoh: 2,5 kg atau 3 kg)
function format_kg(int|float|string $berat): string
{
    $val = (float) $berat;
    $formatted = fmod($val, 1.0) === 0.0
        ? number_format($val, 0, ',', '.')
        : rtrim(rtrim(number_format($val, 2, ',', '.'), '0'), ',');

    return $formatted . ' kg';
}

// 4. Format Tanggal & Jam Indonesia (Contoh: 24 Nov 2026, 09:15)
function format_tanggal(?string $datetime, bool $withTime = true): string
{
    if (!$datetime) {
        return '—';
    }

    $timestamp = strtotime($datetime);
    if ($timestamp === false) {
        return e($datetime);
    }

    $bulan = [
        1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
        'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
    ];

    $hari = date('d', $timestamp);
    $bln  = $bulan[(int) date('n', $timestamp)] ?? date('M', $timestamp);
    $thn  = date('Y', $timestamp);

    if (!$withTime) {
        return "{$hari} {$bln} {$thn}";
    }

    return "{$hari} {$bln} {$thn}, " . date('H:i', $timestamp);
}

// 5. Kelas CSS Badge untuk Status Transaksi
function status_badge_class(string $status): string
{
    return match ($status) {
        'Masuk'   => 'badge--masuk',
        'Proses'  => 'badge--proses',
        'Selesai' => 'badge--selesai',
        default   => 'badge--neutral',
    };
}

// 6. Flash Message (Berbasis Session + Cookie agar stabil di lokal maupun Vercel)
function set_flash(string $type, string $message): void
{
    $payload = [
        'type'    => $type, // 'success' | 'error' | 'info'
        'message' => $message,
    ];
    $_SESSION['flash'] = $payload;
    setcookie('kilawash_flash', base64_encode((string) json_encode($payload)), [
        'expires'  => time() + 60,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function get_flash(): ?array
{
    $flash = null;

    if (isset($_SESSION['flash']) && is_array($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
    } elseif (!empty($_COOKIE['kilawash_flash'])) {
        $decoded = json_decode((string) base64_decode((string) $_COOKIE['kilawash_flash']), true);
        if (is_array($decoded) && isset($decoded['type'], $decoded['message'])) {
            $flash = $decoded;
        }
    }

    if (isset($_COOKIE['kilawash_flash'])) {
        setcookie('kilawash_flash', '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    return $flash;
}

// 7. Redirect Aman
function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

// 8. Generator Kode Transaksi Unik: LND-YYYYMMDD-001
function generate_kode_transaksi(PDO $pdo): string
{
    $prefix = 'LND-' . date('Ymd') . '-';
    $stmt = $pdo->prepare(
        'SELECT kode_transaksi FROM transaksi
         WHERE kode_transaksi LIKE :prefix
         ORDER BY id DESC
         LIMIT 1'
    );
    $stmt->execute(['prefix' => $prefix . '%']);
    $lastKode = $stmt->fetchColumn();

    $nextNumber = 1;
    if (is_string($lastKode)) {
        $parts = explode('-', $lastKode);
        $lastSeq = (int) end($parts);
        $nextNumber = $lastSeq + 1;
    }

    return $prefix . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
}
