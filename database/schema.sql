-- ============================================================================
-- FILE: database/schema.sql
-- DATABASE: Sistem Informasi Manajemen Laundry Berbasis Web (KilaWash)
-- CARA PAKAI: Import file ini lewat phpMyAdmin, HeidiSQL, atau jalankan
--             `php database/seed_reset.php` dari terminal.
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `laundry_asas`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `laundry_asas`;

-- Matikan sementara cek foreign key saat reset ulang tabel
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `transaksi`;
DROP TABLE IF EXISTS `layanan`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------------------------
-- 1. TABEL USERS (Hak Akses Role: Admin & Kasir)
-- ----------------------------------------------------------------------------
CREATE TABLE `users` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nama`          VARCHAR(100) NOT NULL,
  `username`      VARCHAR(50)  NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role`          ENUM('admin', 'kasir') NOT NULL DEFAULT 'kasir',
  `avatar`        MEDIUMTEXT NULL COMMENT 'Foto profil (Data URI base64 agar portabel di lokal & cloud)',
  `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. TABEL LAYANAN (Master Jenis Layanan Laundry)
-- ----------------------------------------------------------------------------
CREATE TABLE `layanan` (
  `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nama_layanan`   VARCHAR(80)  NOT NULL UNIQUE,
  `harga_per_kg`   INT UNSIGNED NOT NULL,
  `estimasi_waktu` VARCHAR(60)  NOT NULL,
  `deskripsi`      TEXT NULL,
  `created_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. TABEL TRANSAKSI (Berelasi ke `layanan` dan `users` melalui FOREIGN KEY)
-- ----------------------------------------------------------------------------
CREATE TABLE `transaksi` (
  `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `kode_transaksi`  VARCHAR(30)   NOT NULL UNIQUE,
  `nama_pelanggan`  VARCHAR(120)  NOT NULL,
  `no_hp`           VARCHAR(25)   NOT NULL,
  `layanan_id`      INT UNSIGNED  NOT NULL,
  `user_id`         INT UNSIGNED  NOT NULL,
  `berat_kg`        DECIMAL(6,2)  NOT NULL,
  `harga_satuan`    INT UNSIGNED  NOT NULL COMMENT 'Snapshot harga/kg saat transaksi dibuat',
  `total_harga`     INT UNSIGNED  NOT NULL COMMENT 'Hasil hitung: berat_kg * harga_satuan',
  `status`          ENUM('Masuk', 'Proses', 'Selesai') NOT NULL DEFAULT 'Proses',
  `catatan`         TEXT NULL,
  `tanggal_masuk`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tanggal_selesai` DATETIME NULL,
  `created_at`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  -- Index untuk performa JOIN dan filter laporan
  INDEX `idx_transaksi_layanan` (`layanan_id`),
  INDEX `idx_transaksi_user`    (`user_id`),
  INDEX `idx_transaksi_status`  (`status`),
  INDEX `idx_transaksi_tanggal` (`tanggal_masuk`),

  -- Relasi Wajib: Setiap transaksi merujuk ke 1 jenis layanan
  CONSTRAINT `fk_transaksi_layanan`
    FOREIGN KEY (`layanan_id`) REFERENCES `layanan` (`id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

  -- Relasi Petugas Pencatat (Admin / Kasir)
  CONSTRAINT `fk_transaksi_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- DATA AWAL (SEED DATA)
-- ============================================================================

-- 1. Akun Demo Default (Password di-hash Bcrypt: admin123 & kasir123)
INSERT INTO `users` (`id`, `nama`, `username`, `password_hash`, `role`) VALUES
  (1, 'Bari Achmad (Admin)', 'admin', '$2y$10$eDLgWEdans7bShuUhvW28uIqllColZ.qK5zML0MQp7ZWndD583zVS', 'admin'),
  (2, 'Rina Wulandari (Kasir)', 'kasir', '$2y$10$n.GBUj5HDN/GtBoRiK6XBOG7UoDVQDkOYYvqBJ7kxvUFTIsyX.9Zq', 'kasir');

-- 2. Layanan Standar (Sesuai contoh spesifikasi tugas ASAS)
INSERT INTO `layanan` (`id`, `nama_layanan`, `harga_per_kg`, `estimasi_waktu`, `deskripsi`) VALUES
  (1, 'Reguler',    7000,  '3-4 hari',      'Cuci bersih, kering mesin, setrika uap, dan lipat rapi untuk pakaian harian.'),
  (2, 'Ekspress',   12000, '1-2 hari',      'Prioritas pengerjaan lebih cepat, cocok untuk seragam kerja dan kebutuhan mendesak.'),
  (3, 'Superkilat', 25000, '5 jam selesai', 'Layanan kilat 1 mesin khusus per pelanggan, selesai dan siap ambil dalam 5 jam.');

-- 3. Contoh Transaksi Awal (Berbagai status untuk langsung terlihat di Dashboard & Laporan)
INSERT INTO `transaksi`
  (`kode_transaksi`, `nama_pelanggan`, `no_hp`, `layanan_id`, `user_id`, `berat_kg`, `harga_satuan`, `total_harga`, `status`, `catatan`, `tanggal_masuk`, `tanggal_selesai`)
VALUES
  ('LND-20261121-001', 'Andi Saputra',    '081234500101', 1, 2, 4.00,  7000,  28000, 'Selesai', 'Pisahkan kemeja putih.',              NOW() - INTERVAL 3 DAY, NOW() - INTERVAL 1 DAY),
  ('LND-20261122-001', 'Siti Rahmawati',  '081355600202', 2, 2, 3.50,  12000, 42000, 'Selesai', 'Pewangi lavender ringan.',            NOW() - INTERVAL 2 DAY, NOW() - INTERVAL 12 HOUR),
  ('LND-20261123-001', 'Dimas Prasetyo',  '085677800303', 3, 1, 2.00,  25000, 50000, 'Selesai', 'Ambil sore jam 17.00.',               NOW() - INTERVAL 1 DAY, NOW() - INTERVAL 18 HOUR),
  ('LND-20261124-001', 'Nadia Kusuma',    '082199000404', 2, 2, 2.50,  12000, 30000, 'Proses',  'Setrika lipat rapi untuk dinas.',     NOW() - INTERVAL 4 HOUR, NULL),
  ('LND-20261124-002', 'Fajar Nugroho',   '087811200505', 1, 2, 5.00,  7000,  35000, 'Proses',  'Tanpa pelembut tambahan.',            NOW() - INTERVAL 2 HOUR, NULL),
  ('LND-20261124-003', 'Rizka Amalia',    '081933400606', 3, 1, 1.50,  25000, 37500, 'Masuk',   'Kemeja wawancara, utamakan cepat.',   NOW() - INTERVAL 30 MINUTE, NULL);
