<?php
/**
 * ============================================================================
 * FILE: config/app.php
 * FUNGSI: Konfigurasi umum aplikasi (nama brand, zona waktu, dan status baku).
 *         Ubah APP_NAME di sini untuk mengganti nama laundry di seluruh web.
 * ============================================================================
 */

declare(strict_types=1);

// 1. Identitas Aplikasi
define('APP_NAME', 'KilaWash');
define('APP_SUBTITLE', 'Sistem Informasi Manajemen Laundry');
define('APP_ADDRESS', 'Jl. Melati Raya, Kecamatan Sukmajaya');

// 2. Zona Waktu Indonesia Barat
date_default_timezone_set('Asia/Jakarta');

// 3. Daftar Status Transaksi Baku (sesuai ENUM di tabel `transaksi`)
define('TRANSAKSI_STATUSES', ['Masuk', 'Proses', 'Selesai']);
