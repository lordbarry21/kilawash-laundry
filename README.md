# KilaWash — Sistem Informasi Manajemen Laundry Berbasis Web

Aplikasi Manajemen Laundry Berbasis Web untuk tugas **ASAS XII RPL** (PHP Native PDO + MySQL), dilengkapi hak akses **Admin & Kasir**, CRUD Data Layanan, Pencatatan & Rekap Transaksi dengan perhitungan tarif otomatis, Pengaturan Profil Akun, serta Laporan Keuangan berbasis relasi SQL `JOIN`.

- **Pengembang:** Bari Hartanto Achmad (`XII RPL 1`)
- **Demo Produksi:** [https://laundry.barrydev.icu](https://laundry.barrydev.icu)

---

## Akun Uji Coba

| Role | Username | Password | Hak Akses |
|---|---|---|---|
| **Admin** | `admin` | `admin123` | Akses penuh: Dashboard, CRUD Data Layanan, Transaksi & Hapus Order, Laporan Keuangan, Pengaturan Akun |
| **Kasir** | `kasir` | `kasir123` | Operasional: Dashboard, Data Layanan (read-only), Catat Transaksi, Update Status (`Masuk → Proses → Selesai`), Cetak Struk, Pengaturan Akun |

---

## Fitur Utama

1. **Autentikasi & Hak Akses Role (RBAC)** — Halaman Login (`login.php`), Registrasi (`register.php`), Logout (`logout.php`), verifikasi password `bcrypt`, proteksi CSRF, serta pembatasan role Admin vs Kasir di UI maupun backend (`require_role('admin')`).
2. **Pengaturan Akun (`profil.php`)** — Ubah foto profil, nama tampilan, username, dan kata sandi secara langsung.
3. **CRUD Data Layanan (`layanan.php`)** — Tambah, ubah, hapus, dan lihat layanan (`Reguler` Rp 7.000/kg 3–4 hari, `Ekspress` Rp 12.000/kg 1–2 hari, `Superkilat` Rp 25.000/kg 5 jam selesai) dengan proteksi relasi `FOREIGN KEY (ON DELETE RESTRICT)`.
4. **Transaksi Laundry & Struk (`transaksi.php`, `struk.php`)** — Pencatatan order baru terhubung ke tabel `layanan`, live kalkulator + hitung ulang tarif otomatis di server (`berat_kg × harga_per_kg`), tombol 1-klik update status pengerjaan (`Masuk → Proses → Selesai`), filter pencarian, dan cetak nota.
5. **Laporan Keuangan (`laporan.php`)** — Query relasional `INNER JOIN` + `GROUP BY` untuk rekap omzet per jenis layanan, filter periode/status/layanan, serta cetak laporan (`@media print`).

---

## Skema Database Relasional (`database/schema.sql`)

Aplikasi menggunakan 3 tabel InnoDB berelasi:
- **`users`**: Data akun Admin & Kasir (`id`, `nama`, `username`, `password_hash`, `role`, `avatar`).
- **`layanan`**: Master tarif layanan cuci (`id`, `nama_layanan`, `harga_per_kg`, `estimasi_waktu`, `deskripsi`).
- **`transaksi`**: Pencatatan order laundry dengan foreign key:
  - `fk_transaksi_layanan`: `transaksi.layanan_id → layanan.id` (`ON UPDATE CASCADE ON DELETE RESTRICT`)
  - `fk_transaksi_user`: `transaksi.user_id → users.id` (`ON UPDATE CASCADE ON DELETE RESTRICT`)

---

## Struktur Folder

```text
├── config/          # Konfigurasi aplikasi (app.php) & koneksi PDO (database.php)
├── database/        # Skema SQL relasional (schema.sql) & CLI seeder (seed_reset.php)
├── includes/        # Autentikasi/RBAC (auth.php), helpers, & shell layout UI
├── actions/         # Endpoint proses POST (layanan, transaksi, profil)
├── assets/          # CSS modular (tokens, components, layout) & JS modern (validation, app)
├── api/             # Front controller khusus deployment serverless Vercel (router.php)
├── docs/            # Panduan presentasi & Log Penggunaan AI
└── *.php            # Halaman utama (login, register, index, layanan, transaksi, laporan, struk, profil)
```

---

## Cara Menjalankan di Lokal (Laragon / XAMPP)

1. Nyalakan layanan **MySQL** (port `3306`, user `root`, password kosong — sesuaikan di `config/database.php` jika berbeda).
2. Import skema database:
   ```bash
   php database/seed_reset.php
   ```
   *(atau import file `database/schema.sql` lewat phpMyAdmin)*
3. Jalankan server PHP lokal:
   ```bash
   php -S 127.0.0.1:8000
   ```
4. Buka `http://127.0.0.1:8000/login.php`.
