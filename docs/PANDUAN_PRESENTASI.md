# Panduan Presentasi & Peta Kode — KilaWash (Laravel 13 + Breeze)

Dokumen ini disiapkan khusus agar **Bari Hartanto Achmad (XII RPL 1)** bisa langsung menunjukkan posisi setiap bagian kode Laravel saat maju presentasi di depan penguji.

---

## 1. Persiapan 1 Menit Sebelum Maju

1. Pastikan layanan **MySQL** menyala (port `3306`, database `db_laundry_express`).
2. Jika ingin reset ulang database ke data awal bersih:
   ```powershell
   php artisan migrate:fresh --seed
   ```
3. Jalankan server Laravel:
   ```powershell
   php artisan serve
   ```
4. Buka browser di `http://127.0.0.1:8000` (atau versi online `https://kilawash-laundry.vercel.app`).

### Akun Demo Presentasi
| Role | Email | Password | Hak Akses |
|---|---|---|---|
| **Admin** | `admin@gmail.com` | `password123` | Gate `manage-laundry` + `operate-laundry`: Dashboard, CRUD Layanan penuh, Update Status, Struk, Laporan, Profil & Foto |
| **Kasir** | `kasir@gmail.com` | `password123` | Gate `operate-laundry`: Dashboard, Update Status, Struk, Laporan, Data Layanan (Read-Only) |
| **Pelanggan** | Daftar di `/register` atau pesan langsung di `/` | — | Katalog & Checkout pesanan kiloan/satuan tanpa perlu login |

---

## 2. Alur Demo 5 Menit (Sesuai Modul 1–4 + Rubrik ASAS)

1. **Katalog & Pemesanan Pelanggan (`/`)**:
   - Filter kategori (`Kiloan`, `Satuan`, `Express`), isi nama `Dina Putri` dan WhatsApp `081234567890`.
   - Pilih *Cuci Kering Setrika Reguler* `2 Kg` (`Rp 14.000`) + *Dry Cleaning Jas / Blazer* `1 Pcs` (`Rp 25.000`).
   - Klik **Pesan Sekarang** → modal `<dialog>` **Konfirmasi Pesanan** menampilkan total `Rp 39.000` → klik **Ya, Kirim Pesanan**.
2. **Login Admin (`/login`) & Dashboard Pesanan Masuk (`/dashboard`)**:
   - Klik quick-fill **Admin** (`admin@gmail.com` / `password123`) → masuk ke Dashboard (`Halo, Bari Achmad (Admin)`).
   - Ubah dropdown status pesanan `Antrean` → `Dicuci` → `Selesai`, lalu klik **Struk** untuk melihat nota cetak.
3. **CRUD Data Layanan (`/admin/services`)**:
   - Klik **+ Tambah Layanan** → tambah *Cuci Sepatu* (`Satuan`, `Pcs`, `15000`) → Simpan.
   - Klik **Edit** → ubah tarif ke `17000` → Update Data.
   - Klik **Hapus** pada *Cuci Sepatu* → terhapus bersih.
   - Coba **Hapus** layanan yang sudah dipakai di pesanan → sistem menolak rapi (proteksi `OrderDetail::where('service_id')->exists()`).
4. **Laporan Keuangan (`/admin/report`) & Pengaturan Profil (`/profile`)**:
   - Tunjukkan rekap omzet per layanan (`JOIN order_details + services` + `GROUP BY`) dan filter tanggal/status.
   - Buka menu dropdown user → **Pengaturan Akun & Foto** untuk ubah foto profil, nama, atau kata sandi.
5. **Bukti Perbedaan Role (Logout → Login Kasir) & Halaman ERD (`/erd`)**:
   - Login sebagai **Kasir** (`kasir@gmail.com` / `password123`), buka `/admin/services` → tampil mode **Baca Saja (Kasir)** tanpa tombol Tambah/Edit/Hapus.
   - Buka `/erd` untuk menunjukkan Diagram ERD interaktif + seluruh file Laravel Migrations tanpa terpotong.

---

## 3. Peta Cepat Lokasi Kode Laravel (Jika Penguji Bertanya)

| Pertanyaan Penguji | File yang Langsung Dibuka | Penjelasan Singkat |
|---|---|---|
| *"Mana file Migration & Foreign Key-nya?"* | `database/migrations/2026_11_24_031438_create_services_table.php` s.d. `031442_add_is_admin_to_users_table.php` | 4 migration: `services`, `orders`, `order_details` (`foreignId()->constrained()->onDelete('cascade')`), dan `is_admin` + `role` + `avatar` di `users`. |
| *"Mana relasi Eloquent antar Model?"* | `app/Models/Order.php` & `app/Models/OrderDetail.php` | `Order::orderDetails()` (`HasMany`), `OrderDetail::order()` & `OrderDetail::service()` (`BelongsTo`). |
| *"Mana Seeder data awal?"* | `database/seeders/ServiceSeeder.php` & `DatabaseSeeder.php` | Seed 6 layanan + akun Admin (`admin@gmail.com`) & Kasir (`kasir@gmail.com`) + 3 contoh pesanan. |
| *"Mana pembagian hak akses Admin vs Kasir?"* | `app/Providers/AppServiceProvider.php` & `routes/web.php` | `Gate::define('manage-laundry')` khusus Admin (`is_admin`) dan `Gate::define('operate-laundry')` untuk Admin & Kasir. |
| *"Mana CRUD Layanan & proteksi hapus?"* | `app/Http/Controllers/ServiceController.php` | `index/create/store/edit/update/destroy` dengan `DB::transaction` & `lockForUpdate()`. |
| *"Mana perhitungan total pesanan & update status?"* | `app/Http/Controllers/OrderController.php` | `store()` pakai `DB::transaction` + `sharedLock()`, `updateStatus()` pakai PATCH, `report()` pakai `JOIN + GROUP BY`. |
| *"Mana pengaturan profil & foto?"* | `app/Http/Controllers/ProfileController.php` | Upload foto profil JPG/PNG/WebP maks. 2 MB + edit nama, email, dan password. |
