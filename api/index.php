<?php
/**
 * ============================================================================
 * FILE: api/index.php
 * FUNGSI: Entrypoint Laravel untuk deployment serverless Vercel (`vercel-php@0.7.4`).
 *         - Menyiapkan /tmp/storage yang writable untuk Blade views, cache, logs.
 *         - Menggunakan Firebase Realtime Database (`laundry-asas/laravel_db.json`)
 *           sebagai sumber data utama (cloud persistence) tanpa hardcode data permanen.
 * ============================================================================
 */

declare(strict_types=1);

$firebaseUrl = rtrim((string) (getenv('FIREBASE_DB_URL') ?: 'https://laundry-asas-default-rtdb.asia-southeast1.firebasedatabase.app'), '/');
$sqlitePath  = '/tmp/db_laundry_express.sqlite';

// 1. Siapkan direktori writable di /tmp untuk Laravel serverless
foreach ([
    '/tmp/storage/app/public',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
] as $dir) {
    if (! is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// 2. Konfigurasi env serverless (cookie session + array cache + SQLite synced with Firebase RTDB)
$serverlessEnv = [
    'APP_NAME'             => 'KilaWash',
    'APP_ENV'              => 'production',
    'APP_DEBUG'            => 'false',
    'APP_KEY'              => getenv('APP_KEY') ?: 'base64:DxbTzwVyPbLT2sJs8qIfdNbRr+UQfV76tere9Sy+fhc=',
    'APP_URL'              => 'https://' . ($_SERVER['HTTP_HOST'] ?? 'laundry.barrydev.icu'),
    'FIREBASE_DB_URL'      => $firebaseUrl,
    'DB_CONNECTION'        => getenv('DB_HOST') ? 'mysql' : 'sqlite',
    'DB_DATABASE'          => getenv('DB_HOST') ? (string) getenv('DB_DATABASE') : $sqlitePath,
    'SESSION_DRIVER'       => 'cookie',
    'SESSION_SECURE_COOKIE'=> 'true',
    'CACHE_STORE'          => 'array',
    'LOG_CHANNEL'          => 'stderr',
    'VIEW_COMPILED_PATH'   => '/tmp/storage/framework/views',
    'APP_SERVICES_CACHE'   => '/tmp/bootstrap/cache/services.php',
    'APP_PACKAGES_CACHE'   => '/tmp/bootstrap/cache/packages.php',
    'APP_CONFIG_CACHE'     => '/tmp/bootstrap/cache/config.php',
    'APP_ROUTES_CACHE'     => '/tmp/bootstrap/cache/routes-v7.php',
    'APP_EVENTS_CACHE'     => '/tmp/bootstrap/cache/events.php',
];

foreach ($serverlessEnv as $k => $v) {
    putenv("{$k}={$v}");
    $_ENV[$k]    = $v;
    $_SERVER[$k] = $v;
}

// 3. Helper REST Firebase Realtime Database
$fbRequest = static function (string $method, string $path, ?array $payload = null) use ($firebaseUrl): ?array {
    $url  = $firebaseUrl . '/' . ltrim($path, '/');
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
        $raw  = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($raw === false || $code < 200 || $code >= 300) {
            return null;
        }
        $decoded = json_decode((string) $raw, true);
        return is_array($decoded) ? $decoded : null;
    }

    return null;
};

// 4. Hydrate SQLite langsung dari Firebase Realtime Database (Single Source of Truth)
$useFirebaseSqlite = ! getenv('DB_HOST');
if ($useFirebaseSqlite) {
    if (! is_file($sqlitePath)) {
        touch($sqlitePath);
    }

    $pdo = new PDO('sqlite:' . $sqlitePath, null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec('PRAGMA foreign_keys = OFF;');
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            email_verified_at DATETIME NULL,
            password TEXT NOT NULL,
            remember_token TEXT NULL,
            is_admin INTEGER NOT NULL DEFAULT 0,
            role TEXT NOT NULL DEFAULT 'customer',
            avatar TEXT NULL,
            created_at DATETIME NULL,
            updated_at DATETIME NULL
        );
        CREATE TABLE IF NOT EXISTS services (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            service_name TEXT NOT NULL,
            category TEXT NOT NULL,
            price_per_unit INTEGER NOT NULL,
            unit_type TEXT NOT NULL,
            description TEXT NULL,
            created_at DATETIME NULL,
            updated_at DATETIME NULL
        );
        CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            customer_name TEXT NOT NULL,
            whatsapp_number TEXT NOT NULL,
            total_price INTEGER NOT NULL,
            status TEXT NOT NULL DEFAULT 'Antrean',
            created_at DATETIME NULL,
            updated_at DATETIME NULL
        );
        CREATE TABLE IF NOT EXISTS order_details (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id INTEGER NOT NULL,
            service_id INTEGER NOT NULL,
            qty_or_weight INTEGER NOT NULL,
            subtotal INTEGER NOT NULL,
            created_at DATETIME NULL,
            updated_at DATETIME NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
        );
    ");

    $snapshot = $fbRequest('GET', 'laravel_db.json');
    $pdo->exec('DELETE FROM order_details; DELETE FROM orders; DELETE FROM services; DELETE FROM users;');

    if (is_array($snapshot)) {
        $uStmt = $pdo->prepare('INSERT INTO users (id, name, email, email_verified_at, password, remember_token, is_admin, role, avatar, created_at, updated_at) VALUES (:id,:name,:email,:email_verified_at,:password,:remember_token,:is_admin,:role,:avatar,:created_at,:updated_at)');
        foreach ((array) ($snapshot['users'] ?? []) as $r) {
            if (! is_array($r)) continue;
            $uStmt->execute([
                'id' => (int) ($r['id'] ?? 0),
                'name' => (string) ($r['name'] ?? ''),
                'email' => (string) ($r['email'] ?? ''),
                'email_verified_at' => $r['email_verified_at'] ?? null,
                'password' => (string) ($r['password'] ?? ''),
                'remember_token' => $r['remember_token'] ?? null,
                'is_admin' => (int) ($r['is_admin'] ?? 0),
                'role' => (string) ($r['role'] ?? 'customer'),
                'avatar' => $r['avatar'] ?? null,
                'created_at' => $r['created_at'] ?? date('Y-m-d H:i:s'),
                'updated_at' => $r['updated_at'] ?? date('Y-m-d H:i:s'),
            ]);
        }

        $sStmt = $pdo->prepare('INSERT INTO services (id, service_name, category, price_per_unit, unit_type, description, created_at, updated_at) VALUES (:id,:service_name,:category,:price_per_unit,:unit_type,:description,:created_at,:updated_at)');
        foreach ((array) ($snapshot['services'] ?? []) as $r) {
            if (! is_array($r)) continue;
            $sStmt->execute([
                'id' => (int) ($r['id'] ?? 0),
                'service_name' => (string) ($r['service_name'] ?? ''),
                'category' => (string) ($r['category'] ?? 'Kiloan'),
                'price_per_unit' => (int) ($r['price_per_unit'] ?? 0),
                'unit_type' => (string) ($r['unit_type'] ?? 'Kg'),
                'description' => $r['description'] ?? null,
                'created_at' => $r['created_at'] ?? date('Y-m-d H:i:s'),
                'updated_at' => $r['updated_at'] ?? date('Y-m-d H:i:s'),
            ]);
        }

        $oStmt = $pdo->prepare('INSERT INTO orders (id, customer_name, whatsapp_number, total_price, status, created_at, updated_at) VALUES (:id,:customer_name,:whatsapp_number,:total_price,:status,:created_at,:updated_at)');
        foreach ((array) ($snapshot['orders'] ?? []) as $r) {
            if (! is_array($r)) continue;
            $oStmt->execute([
                'id' => (int) ($r['id'] ?? 0),
                'customer_name' => (string) ($r['customer_name'] ?? ''),
                'whatsapp_number' => (string) ($r['whatsapp_number'] ?? ''),
                'total_price' => (int) ($r['total_price'] ?? 0),
                'status' => (string) ($r['status'] ?? 'Antrean'),
                'created_at' => $r['created_at'] ?? date('Y-m-d H:i:s'),
                'updated_at' => $r['updated_at'] ?? date('Y-m-d H:i:s'),
            ]);
        }

        $dStmt = $pdo->prepare('INSERT INTO order_details (id, order_id, service_id, qty_or_weight, subtotal, created_at, updated_at) VALUES (:id,:order_id,:service_id,:qty_or_weight,:subtotal,:created_at,:updated_at)');
        foreach ((array) ($snapshot['order_details'] ?? []) as $r) {
            if (! is_array($r)) continue;
            $dStmt->execute([
                'id' => (int) ($r['id'] ?? 0),
                'order_id' => (int) ($r['order_id'] ?? 0),
                'service_id' => (int) ($r['service_id'] ?? 0),
                'qty_or_weight' => (int) ($r['qty_or_weight'] ?? 1),
                'subtotal' => (int) ($r['subtotal'] ?? 0),
                'created_at' => $r['created_at'] ?? date('Y-m-d H:i:s'),
                'updated_at' => $r['updated_at'] ?? date('Y-m-d H:i:s'),
            ]);
        }
    }
    $pdo->exec('PRAGMA foreign_keys = ON;');

    // 5. Setelah aksi tulis (POST/PUT/PATCH/DELETE), simpan otomatis perubahan ke Firebase RTDB
    if (in_array(strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
        register_shutdown_function(static function () use ($sqlitePath, $fbRequest): void {
            try {
                $p = new PDO('sqlite:' . $sqlitePath, null, null, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
                $fbRequest('PUT', 'laravel_db.json', [
                    'updated_at'    => date('Y-m-d H:i:s'),
                    'users'         => $p->query('SELECT * FROM users ORDER BY id')->fetchAll(),
                    'services'      => $p->query('SELECT * FROM services ORDER BY id')->fetchAll(),
                    'orders'        => $p->query('SELECT * FROM orders ORDER BY id')->fetchAll(),
                    'order_details' => $p->query('SELECT * FROM order_details ORDER BY id')->fetchAll(),
                ]);
            } catch (Throwable) {
                // Jangan ganggu respons HTTP jika sync gagal
            }
        });
    }
}

// 6. Boot Laravel secara eksplisit dengan writable storagePath di /tmp/storage
define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->useStoragePath('/tmp/storage');

$app->handleRequest(\Illuminate\Http\Request::capture());
