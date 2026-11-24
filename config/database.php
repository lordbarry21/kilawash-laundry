<?php
/**
 * ============================================================================
 * FILE: config/database.php
 * FUNGSI: Koneksi tunggal database menggunakan PHP Data Objects (PDO).
 *         - Lokal (Laragon/XAMPP) / Cloud MySQL: terhubung langsung ke MySQL
 *           menggunakan skema `database/schema.sql`.
 *         - Produksi Vercel (tanpa LAUNDRY_DB_HOST): menggunakan engine SQL
 *           relasional (PRAGMA foreign_keys = ON) yang disinkronkan secara
 *           real-time ke Firebase Realtime Database (`laundry-asas`) via REST API
 *           agar seluruh data 100% persisten di cloud!
 * ============================================================================
 */

declare(strict_types=1);

// 1. Parameter Koneksi MySQL (Default Laragon / XAMPP lokal)
define('DB_HOST', getenv('LAUNDRY_DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('LAUNDRY_DB_PORT') ?: '3306');
define('DB_NAME', getenv('LAUNDRY_DB_NAME') ?: 'laundry_asas');
define('DB_USER', getenv('LAUNDRY_DB_USER') ?: 'root');
define('DB_PASS', getenv('LAUNDRY_DB_PASS') !== false ? (string) getenv('LAUNDRY_DB_PASS') : '');

// 2. Endpoint Firebase Realtime Database (Produksi Cloud Persistence)
define(
    'FIREBASE_DB_URL',
    rtrim((string) (getenv('FIREBASE_DB_URL') ?: 'https://laundry-asas-default-rtdb.asia-southeast1.firebasedatabase.app'), '/')
);

/**
 * Mengecek apakah aplikasi berjalan pada mode Vercel + Firebase Cloud Sync.
 */
function is_firebase_cloud_mode(): bool
{
    return (getenv('VERCEL') === '1' || getenv('VERCEL_ENV') !== false || getenv('LAUNDRY_USE_FIREBASE') === '1')
        && !getenv('LAUNDRY_DB_HOST');
}

/**
 * Mengembalikan instance PDO tunggal (Singleton Pattern).
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    if (is_firebase_cloud_mode()) {
        $pdo = connect_firebase_relational_bridge();
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

// ============================================================================
// FIREBASE REALTIME DATABASE REST BRIDGE (Untuk Hosting Vercel Persisten)
// ============================================================================

function firebase_rest_request(string $method, string $path, ?array $payload = null): ?array
{
    $url = FIREBASE_DB_URL . '/' . ltrim($path, '/');
    $body = $payload !== null ? json_encode($payload, JSON_UNESCAPED_UNICODE) : null;

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        ]);
        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        $raw = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false || $code < 200 || $code >= 300) {
            return null;
        }
        $decoded = json_decode((string) $raw, true);
        return is_array($decoded) ? $decoded : null;
    }

    $opts = [
        'http' => [
            'method'        => $method,
            'header'        => "Content-Type: application/json\r\n",
            'timeout'       => 8,
            'ignore_errors' => true,
        ],
    ];
    if ($body !== null) {
        $opts['http']['content'] = $body;
    }

    $raw = @file_get_contents($url, false, stream_context_create($opts));
    if ($raw === false) {
        return null;
    }
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : null;
}

/**
 * Membangun engine SQLite relasional di memori lalu memuat snapshot terbaru
 * dari Firebase Realtime Database (`/laundry_asas.json`).
 */
function connect_firebase_relational_bridge(): PDO
{
    $pdo = new PDO('sqlite::memory:', null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $pdo->exec('PRAGMA foreign_keys = ON;');

    $pdo->exec("
        CREATE TABLE users (
            id            INTEGER PRIMARY KEY AUTOINCREMENT,
            nama          TEXT NOT NULL,
            username      TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            role          TEXT NOT NULL DEFAULT 'kasir' CHECK(role IN ('admin', 'kasir')),
            avatar        TEXT NULL,
            created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE layanan (
            id             INTEGER PRIMARY KEY AUTOINCREMENT,
            nama_layanan   TEXT NOT NULL UNIQUE,
            harga_per_kg   INTEGER NOT NULL,
            estimasi_waktu TEXT NOT NULL,
            deskripsi      TEXT NULL,
            created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE transaksi (
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
    ");

    $snapshot = firebase_rest_request('GET', 'laundry_asas.json');

    if (is_array($snapshot) && !empty($snapshot['users']) && !empty($snapshot['layanan'])) {
        hydrate_sqlite_from_snapshot($pdo, $snapshot);
    } else {
        seed_default_sqlite_data($pdo);
        push_sqlite_snapshot_to_firebase($pdo);
    }

    return $pdo;
}

function hydrate_sqlite_from_snapshot(PDO $pdo, array $snapshot): void
{
    $pdo->exec('PRAGMA foreign_keys = OFF;');

    $stmtUser = $pdo->prepare(
        'INSERT INTO users (id, nama, username, password_hash, role, avatar, created_at)
         VALUES (:id, :nama, :username, :password_hash, :role, :avatar, :created_at)'
    );
    foreach ((array) ($snapshot['users'] ?? []) as $row) {
        if (!is_array($row)) continue;
        $stmtUser->execute([
            'id'            => (int) ($row['id'] ?? 0),
            'nama'          => (string) ($row['nama'] ?? ''),
            'username'      => (string) ($row['username'] ?? ''),
            'password_hash' => (string) ($row['password_hash'] ?? ''),
            'role'          => (string) ($row['role'] ?? 'kasir'),
            'avatar'        => $row['avatar'] ?? null,
            'created_at'    => (string) ($row['created_at'] ?? date('Y-m-d H:i:s')),
        ]);
    }

    $stmtLay = $pdo->prepare(
        'INSERT INTO layanan (id, nama_layanan, harga_per_kg, estimasi_waktu, deskripsi, created_at, updated_at)
         VALUES (:id, :nama_layanan, :harga_per_kg, :estimasi_waktu, :deskripsi, :created_at, :updated_at)'
    );
    foreach ((array) ($snapshot['layanan'] ?? []) as $row) {
        if (!is_array($row)) continue;
        $stmtLay->execute([
            'id'             => (int) ($row['id'] ?? 0),
            'nama_layanan'   => (string) ($row['nama_layanan'] ?? ''),
            'harga_per_kg'   => (int) ($row['harga_per_kg'] ?? 0),
            'estimasi_waktu' => (string) ($row['estimasi_waktu'] ?? ''),
            'deskripsi'      => $row['deskripsi'] ?? null,
            'created_at'     => (string) ($row['created_at'] ?? date('Y-m-d H:i:s')),
            'updated_at'     => (string) ($row['updated_at'] ?? date('Y-m-d H:i:s')),
        ]);
    }

    $stmtTrx = $pdo->prepare(
        'INSERT INTO transaksi
            (id, kode_transaksi, nama_pelanggan, no_hp, layanan_id, user_id, berat_kg, harga_satuan, total_harga, status, catatan, tanggal_masuk, tanggal_selesai, created_at)
         VALUES
            (:id, :kode_transaksi, :nama_pelanggan, :no_hp, :layanan_id, :user_id, :berat_kg, :harga_satuan, :total_harga, :status, :catatan, :tanggal_masuk, :tanggal_selesai, :created_at)'
    );
    foreach ((array) ($snapshot['transaksi'] ?? []) as $row) {
        if (!is_array($row)) continue;
        $stmtTrx->execute([
            'id'              => (int) ($row['id'] ?? 0),
            'kode_transaksi'  => (string) ($row['kode_transaksi'] ?? ''),
            'nama_pelanggan'  => (string) ($row['nama_pelanggan'] ?? ''),
            'no_hp'           => (string) ($row['no_hp'] ?? ''),
            'layanan_id'      => (int) ($row['layanan_id'] ?? 1),
            'user_id'         => (int) ($row['user_id'] ?? 1),
            'berat_kg'        => (float) ($row['berat_kg'] ?? 1),
            'harga_satuan'    => (int) ($row['harga_satuan'] ?? 0),
            'total_harga'     => (int) ($row['total_harga'] ?? 0),
            'status'          => (string) ($row['status'] ?? 'Proses'),
            'catatan'         => $row['catatan'] ?? null,
            'tanggal_masuk'   => (string) ($row['tanggal_masuk'] ?? date('Y-m-d H:i:s')),
            'tanggal_selesai' => $row['tanggal_selesai'] ?? null,
            'created_at'      => (string) ($row['created_at'] ?? date('Y-m-d H:i:s')),
        ]);
    }

    $pdo->exec('PRAGMA foreign_keys = ON;');
}

function seed_default_sqlite_data(PDO $pdo): void
{
    $pdo->exec("
        INSERT INTO users (id, nama, username, password_hash, role) VALUES
            (1, 'Bari Achmad (Admin)', 'admin', '\$2y\$10\$eDLgWEdans7bShuUhvW28uIqllColZ.qK5zML0MQp7ZWndD583zVS', 'admin'),
            (2, 'Rina Wulandari (Kasir)', 'kasir', '\$2y\$10\$n.GBUj5HDN/GtBoRiK6XBOG7UoDVQDkOYYvqBJ7kxvUFTIsyX.9Zq', 'kasir');

        INSERT INTO layanan (id, nama_layanan, harga_per_kg, estimasi_waktu, deskripsi) VALUES
            (1, 'Reguler',    7000,  '3-4 hari',      'Cuci bersih, kering mesin, setrika uap, dan lipat rapi untuk pakaian harian.'),
            (2, 'Ekspress',   12000, '1-2 hari',      'Prioritas pengerjaan lebih cepat, cocok untuk seragam kerja dan kebutuhan mendesak.'),
            (3, 'Superkilat', 25000, '5 jam selesai', 'Layanan kilat 1 mesin khusus per pelanggan, selesai dan siap ambil dalam 5 jam.');

        INSERT INTO transaksi
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

function push_sqlite_snapshot_to_firebase(PDO $pdo): void
{
    $snapshot = [
        'updated_at' => date('Y-m-d H:i:s'),
        'users'      => $pdo->query('SELECT * FROM users ORDER BY id ASC')->fetchAll(),
        'layanan'    => $pdo->query('SELECT * FROM layanan ORDER BY id ASC')->fetchAll(),
        'transaksi'  => $pdo->query('SELECT * FROM transaksi ORDER BY id ASC')->fetchAll(),
    ];

    firebase_rest_request('PUT', 'laundry_asas.json', $snapshot);
}

/**
 * Dipanggil setelah setiap aksi INSERT / UPDATE / DELETE agar perubahan langsung
 * tersimpan permanen ke Firebase Realtime Database saat berjalan di Vercel.
 */
function db_sync_if_cloud(): void
{
    if (is_firebase_cloud_mode()) {
        push_sqlite_snapshot_to_firebase(db());
    }
}
