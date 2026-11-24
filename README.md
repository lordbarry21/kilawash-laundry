# KilaWash — Sistem Pendaftaran & Manajemen Layanan Laundry (Laravel 13 + Breeze)

Aplikasi Web Manajemen Laundry berbasis **Laravel 13**, **Laravel Breeze (Blade, Tailwind CSS, Alpine.js, Vite)**, dan **MySQL** (`db_laundry_express`), disusun oleh **Bari Hartanto Achmad (`XII RPL 1`)**.

- **Demo Produksi (Vercel + Firebase RTDB):** [https://kilawash-laundry.vercel.app](https://kilawash-laundry.vercel.app)
- **Custom Domain:** [https://laundry.barrydev.icu](https://laundry.barrydev.icu)
- **ERD & Migrations Viewer (Publik):** [https://kilawash-laundry.vercel.app/erd](https://kilawash-laundry.vercel.app/erd)

---

## Akun Uji Coba

| Role | Email | Password | Hak Akses |
|---|---|---|---|
| **Admin** | `admin@gmail.com` | `password123` | Akses penuh (`Gate: manage-laundry`): Dashboard Pesanan Masuk, CRUD Master Layanan, Update Status, Laporan Keuangan, Struk, Pengaturan Profil & Foto |
| **Kasir** | `kasir@gmail.com` | `password123` | Operasional (`Gate: operate-laundry`): Dashboard Pesanan Masuk, Update Status, Cetak Struk, Laporan Keuangan, Lihat Layanan (Read-Only) |
| **Pelanggan** | Daftar di `/register` atau pesan langsung di `/` | Bebas | Pilih layanan kiloan/satuan/express, isi WhatsApp, lihat konfirmasi total, dan kirim pesanan |

---

## Struktur Database & Relasi (`database/migrations/`)

1. **`users`**: Autentikasi Breeze + tambahan `is_admin` (`boolean`), `role` (`admin`, `kasir`, `customer`), dan `avatar` (`mediumText`).
2. **`services`**: Master katalog tarif (`service_name`, `category` ENUM `Kiloan/Satuan/Express`, `price_per_unit`, `unit_type` ENUM `Kg/Pcs`, `description`).
3. **`orders`**: Pesanan pelanggan (`customer_name`, `whatsapp_number`, `total_price`, `status` ENUM `Antrean/Dicuci/Selesai`).
4. **`order_details`**: Rincian item pesanan dengan relasi:
   - `order_id` → `orders.id` (`ON DELETE CASCADE`)
   - `service_id` → `services.id` (`ON DELETE CASCADE`)

---

## Cara Menjalankan di Lokal (Laragon / XAMPP)

1. Nyalakan MySQL lalu buat database `db_laundry_express`.
2. Salin `.env.example` ke `.env` dan sesuaikan koneksi MySQL:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=db_laundry_express
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Jalankan instalasi & migrasi:
   ```bash
   composer install
   npm install && npm run build
   php artisan migrate --seed
   php artisan serve
   ```
4. Buka `http://127.0.0.1:8000` (Katalog Pelanggan), `/login` (Login Admin/Kasir), atau `/erd` (ERD & Migrations).
