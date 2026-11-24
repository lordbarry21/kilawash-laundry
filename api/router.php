<?php
/**
 * ============================================================================
 * FILE: api/router.php
 * FUNGSI: Front Controller khusus untuk deployment serverless Vercel
 *         (`vercel-php@0.7.4`). Memetakan URL ke halaman PHP di root proyek
 *         tanpa mengubah struktur folder lokal (XAMPP/Laragon tetap langsung jalan).
 * ============================================================================
 */

declare(strict_types=1);

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$normalized  = '/' . ltrim($requestPath, '/');

// Daftar rute PHP yang diizinkan (whitelist aman)
$routes = [
    '/'                           => __DIR__ . '/../index.php',
    '/index.php'                  => __DIR__ . '/../index.php',
    '/login.php'                  => __DIR__ . '/../login.php',
    '/register.php'               => __DIR__ . '/../register.php',
    '/logout.php'                 => __DIR__ . '/../logout.php',
    '/layanan.php'                => __DIR__ . '/../layanan.php',
    '/transaksi.php'              => __DIR__ . '/../transaksi.php',
    '/laporan.php'                => __DIR__ . '/../laporan.php',
    '/struk.php'                  => __DIR__ . '/../struk.php',
    '/profil.php'                 => __DIR__ . '/../profil.php',
    '/erd.php'                    => __DIR__ . '/../erd.php',
    '/erd'                        => __DIR__ . '/../erd.php',
    '/actions/layanan_action.php' => __DIR__ . '/../actions/layanan_action.php',
    '/actions/transaksi_action.php' => __DIR__ . '/../actions/transaksi_action.php',
    '/actions/profil_action.php'  => __DIR__ . '/../actions/profil_action.php',
];

if (isset($routes[$normalized]) && is_file($routes[$normalized])) {
    require $routes[$normalized];
    exit;
}

// Layani file statis di /assets jika tidak tertangkap CDN edge
if (str_starts_with($normalized, '/assets/')) {
    $file = realpath(__DIR__ . '/..' . $normalized);
    $assetsRoot = realpath(__DIR__ . '/../assets');
    if ($file && $assetsRoot && str_starts_with($file, $assetsRoot) && is_file($file)) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'css'  => 'text/css; charset=utf-8',
            'js'   => 'application/javascript; charset=utf-8',
            'svg'  => 'image/svg+xml',
            'png'  => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => 'application/octet-stream',
        };
        header('Content-Type: ' . $mime);
        header('Cache-Control: public, max-age=3600');
        readfile($file);
        exit;
    }
}

http_response_code(404);
echo '<!doctype html><html lang="id"><head><meta charset="utf-8"><title>404 — Halaman Tidak Ditemukan</title></head>'
    . '<body style="font-family:system-ui,sans-serif;padding:40px;color:#1f3038"><h1>404 — Halaman Tidak Ditemukan</h1>'
    . '<p><a href="/index.php">Kembali ke Dashboard</a></p></body></html>';
