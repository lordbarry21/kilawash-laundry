<?php
/**
 * ============================================================================
 * FILE: database/seed_reset.php
 * FUNGSI: Script CLI untuk mengimpor ulang `database/schema.sql` ke MySQL
 *         dalam 1 perintah: `php database/seed_reset.php`
 * ============================================================================
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Script ini hanya boleh dijalankan lewat terminal (CLI).');
}

require_once __DIR__ . '/../config/database.php';

$schemaFile = __DIR__ . '/schema.sql';
if (!is_file($schemaFile)) {
    fwrite(STDERR, "File schema.sql tidak ditemukan.\n");
    exit(1);
}

$sql = file_get_contents($schemaFile);
if ($sql === false) {
    fwrite(STDERR, "Gagal membaca database/schema.sql.\n");
    exit(1);
}

try {
    // Konek ke server MySQL tanpa dbname terlebih dahulu agar bisa CREATE DATABASE
    $dsn = sprintf('mysql:host=%s;port=%s;charset=utf8mb4', DB_HOST, DB_PORT);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $pdo->exec($sql);
    echo "BERHASIL: Database '" . DB_NAME . "' berhasil dibuat ulang beserta tabel relasi dan data seed.\n";
    echo "Akun demo:\n";
    echo "  - Admin : admin / admin123\n";
    echo "  - Kasir : kasir / kasir123\n";
} catch (PDOException $e) {
    fwrite(STDERR, "GAGAL: " . $e->getMessage() . "\n");
    fwrite(STDERR, "Pastikan layanan MySQL di Laragon atau XAMPP sudah menyala.\n");
    exit(1);
}
