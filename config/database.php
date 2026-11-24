<?php
/**
 * ============================================================================
 * FILE: config/database.php
 * FUNGSI: Koneksi tunggal database menggunakan PHP Data Objects (PDO).
 *         - Lokal (Laragon/XAMPP) / Cloud MySQL: otomatis terhubung ke MySQL
 *           menggunakan `database/schema.sql`.
 *         - Serverless Vercel (tanpa env DB_HOST): otomatis menyiapkan SQLite
 *           relasional (PRAGMA foreign_keys = ON) dengan skema & seed identik
 *           agar produksi langsung berjalan tanpa error.
 * ============================================================================
 */

declare(strict_types=1);

// 1. Parameter Koneksi MySQL (Default Laragon / XAMPP lokal)
define('DB_HOST', getenv('LAUNDRY_DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('LAUNDRY_DB_PORT') ?: '3306');
define('DB_NAME', getenv('LAUNDRY_DB_NAME') ?: 'laundry_asas');
define('DB_USER', getenv('LAUNDRY_DB_USER') ?: 'root');
define('DB_PASS', getenv('LAUNDRY_DB_PASS') !== false ? (string) getenv('LAUNDRY_DB_PASS') : '');

/**
 * Mengembalikan instance PDO tunggal (Singleton Pattern).
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    // Jika berjalan di Vercel Serverless tanpa Cloud MySQL eksternal, gunakan SQLite relasional di /tmp
    $isVercelWithoutMySQL = (getenv('VERCEL') === '1' || getenv('VERCEL_ENV') !== false)
        && !getenv('LAUNDRY_DB_HOST');

    if ($isVercelWithoutMySQL) {
        $pdo = connect_vercel_sqlite();
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        DB_HOST,
        DB_PORT,
        DB_NAME
    );

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo '<!doctype html><html lang="id"><head><meta charset="utf-8"><title>Koneksi Database Gagal</title>'
            . '<style>body{font-family:system-ui,sans-serif;background:#f7f9fa;color:#1f3038;padding:40px;line-height:1.6}'
            . '.box{max-width:620px;margin:0 auto;background:#fff;border:1px solid #d5dfe4;border-radius:12px;padding:24px}'
            . 'code{background:#ecf0f2;padding:2px 6px;border-radius:4px}</style></head><body>'
            . '<div class="box"><h1>Koneksi ke Database MySQL Gagal</h1>'
            . '<p>Pastikan layanan <strong>MySQL</strong> di Laragon atau XAMPP sudah menyala, lalu import file <code>database/schema.sql</code>.</p>'
            . '<p><strong>Detail:</strong> ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>'
            . '</div></body></html>';
        exit;
    }

    return $pdo;
}

/**
 * Inisialisasi otomatis database SQLite relasional untuk deployment Vercel Serverless
 * bila belum dihubungkan ke server MySQL cloud eksternal.
 */
function connect_vercel_sqlite(): PDO
{
    $dbPath = sys_get_temp_dir() . '/laundry_asas.sqlite';
    $needsSeed = !is_file($dbPath) || filesize($dbPath) === 0;

    $pdo = new PDO('sqlite:' . $dbPath, null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Aktifkan integritas FOREIGN KEY secara ketat seperti di MySQL InnoDB
    $pdo->exec('PRAGMA foreign_keys = ON;');

    if ($needsSeed) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id            INTEGER PRIMARY KEY AUTOINCREMENT,
                nama          TEXT NOT NULL,
                username      TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                role          TEXT NOT NULL DEFAULT 'kasir' CHECK(role IN ('admin', 'kasir')),
                avatar        TEXT NULL,
                created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS layanan (
                id             INTEGER PRIMARY KEY AUTOINCREMENT,
                nama_layanan   TEXT NOT NULL UNIQUE,
                harga_per_kg   INTEGER NOT NULL,
                estimasi_waktu TEXT NOT NULL,
                deskripsi      TEXT NULL,
                created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS transaksi (
                id              INTEGER PRIMARY KEY AUTOINCREMENT,
                kode_transaksi  TEXT NOT NULL UNIQUE,
                nama_pelanggan  TEXT NOT NULL,
                no_hp           TEXT NOT NULL,
                layanan_id      INTEGER NOT NULL,
                user_id         INTEGER NOT NULL,
                berat_kg        REAL NOT NULL,
                harga_satuan    INTEGER NOT NULL,
                total_harga     INTEGER NOT NULL,
                status          TEXT NOT NULL DEFAULT 'Proses' CHECK(status IN ('Masuk', 'Proses', 'Selesai')),
                catatan         TEXT NULL,
                tanggal_masuk   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                tanggal_selesai DATETIME NULL,
                created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (layanan_id) REFERENCES layanan(id) ON UPDATE CASCADE ON DELETE RESTRICT,
                FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT
            );

            INSERT OR IGNORE INTO users (id, nama, username, password_hash, role) VALUES
                (1, 'Bari Achmad (Admin)', 'admin', '\$2y\$10\$eDLgWEdans7bShuUhvW28uIqllColZ.qK5zML0MQp7ZWndD583zVS', 'admin'),
                (2, 'Rina Wulandari (Kasir)', 'kasir', '\$2y\$10\$n.GBUj5HDN/GtBoRiK6XBOG7UoDVQDkOYYvqBJ7kxvUFTIsyX.9Zq', 'kasir');

            INSERT OR IGNORE INTO layanan (id, nama_layanan, harga_per_kg, estimasi_waktu, deskripsi) VALUES
                (1, 'Reguler',    7000,  '3-4 hari',      'Cuci bersih, kering mesin, setrika uap, dan lipat rapi untuk pakaian harian.'),
                (2, 'Ekspress',   12000, '1-2 hari',      'Prioritas pengerjaan lebih cepat, cocok untuk seragam kerja dan kebutuhan mendesak.'),
                (3, 'Superkilat', 25000, '5 jam selesai', 'Layanan kilat 1 mesin khusus per pelanggan, selesai dan siap ambil dalam 5 jam.');

            INSERT OR IGNORE INTO transaksi
                (kode_transaksi, nama_pelanggan, no_hp, layanan_id, user_id, berat_kg, harga_satuan, total_harga, status, catatan, tanggal_masuk, tanggal_selesai)
            VALUES
                ('LND-20261121-001', 'Andi Saputra',   '081234500101', 1, 2, 4.00, 7000,  28000, 'Selesai', 'Pisahkan kemeja putih.',            datetime('now', '-3 days', '+7 hours'),  datetime('now', '-1 day', '+7 hours')),
                ('LND-20261122-001', 'Siti Rahmawati', '081355600202', 2, 2, 3.50, 12000, 42000, 'Selesai', 'Pewangi segar ringan.',             datetime('now', '-2 days', '+7 hours'),  datetime('now', '-12 hours', '+7 hours')),
                ('LND-20261123-001', 'Dimas Prasetyo', '085677800303', 3, 1, 2.00, 25000, 50000, 'Selesai', 'Ambil sore jam 17.00.',             datetime('now', '-1 day', '+7 hours'),   datetime('now', '-18 hours', '+7 hours')),
                ('LND-20261124-001', 'Nadia Kusuma',   '082199000404', 2, 2, 2.50, 12000, 30000, 'Proses',  'Setrika lipat rapi untuk dinas.',   datetime('now', '-4 hours', '+7 hours'), NULL),
                ('LND-20261124-002', 'Fajar Nugroho',  '087811200505', 1, 2, 5.00, 7000,  35000, 'Proses',  'Tanpa pelembut tambahan.',          datetime('now', '-2 hours', '+7 hours'), NULL),
                ('LND-20261124-003', 'Rizka Amalia',   '081933400606', 3, 1, 1.50, 25000, 37500, 'Masuk',   'Kemeja wawancara, utamakan cepat.', datetime('now', '-30 minutes', '+7 hours'), NULL);
        ");
    }

    return $pdo;
}
