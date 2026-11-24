# Panduan Presentasi & Peta Kode — KilaWash (ASAS)

Dokumen ini disiapkan khusus agar Anda lancar, percaya diri, dan cepat menunjuk bagian kode saat maju presentasi di depan penguji/guru.

---

## 1. Persiapan 1 Menit Sebelum Maju

1. Pastikan layanan **MySQL** di Laragon atau XAMPP sudah menyala (port `3306`).
2. Jika database belum ada atau ingin reset ke data awal bersih, jalankan di terminal folder proyek:
   ```powershell
   php database/seed_reset.php
   ```
3. Jalankan server PHP lokal (atau buka lewat Virtual Host Laragon / `localhost/ASAS`):
   ```powershell
   php -S 127.0.0.1:8000
   ```
4. Buka di browser: `http://127.0.0.1:8000/login.php`

### Akun Demo Presentasi
| Role | Username | Password | Hak Akses |
|---|---|---|---|
| **Admin** | `admin` | `admin123` | Akses penuh: CRUD Data Layanan, Transaksi, Hapus Order, Laporan Keuangan |
| **Kasir** | `kasir` | `kasir123` | Operasional: Lihat Layanan (read-only), Catat Transaksi, Update Status, Cetak Struk |

---

## 2. Urutan Alur Demo (5 Menit Rapi)

1. **Halaman Login (`login.php`)**
   - Tunjukkan UI bersih, validasi form, toggle lihat kata sandi, dan klik akun **Admin** (`admin` / `admin123`).
2. **Dashboard Utama (`index.php`)**
   - Tunjukkan 4 KPI real-time, daftar tarif layanan aktif, dan 5 transaksi terbaru.
3. **CRUD Data Layanan (`layanan.php`)**
   - Tunjukkan data awal sesuai soal: **Reguler** (`Rp 7.000/kg`, 3–4 hari), **Ekspress** (`Rp 12.000/kg`, 1–2 hari), **Superkilat** (`Rp 25.000/kg`, 5 jam selesai).
   - Klik **Tambah Layanan** → buat misal *"Cuci Selimut"* `Rp 15.000/kg` → Simpan.
   - Klik **Ubah** pada layanan baru → ubah harga → Simpan Perubahan.
   - Klik **Hapus** pada layanan baru → konfirmasi di modal `<dialog>` → terhapus.
   - (Nilai plus!) Klik **Hapus** pada *Reguler* → tunjukkan pesan proteksi bahwa layanan yang sudah berelasi dengan transaksi **tidak bisa dihapus** (`ON DELETE RESTRICT`).
4. **Transaksi Laundry & Update Status (`transaksi.php` + `struk.php`)**
   - Klik **Catat Transaksi Baru** → isi Nama Pelanggan, No. HP, pilih **Ekspress** (`Rp 12.000/kg`), Berat `2.5` kg → tunjukkan pratinjau total otomatis **`Rp 30.000`**.
   - Simpan → transaksi muncul di tabel teratas dengan status **Proses**.
   - Klik tombol **Tandai Selesai** → status langsung berubah menjadi **Selesai** dan waktu selesai tercatat otomatis.
   - Klik **Struk** → tunjukkan halaman nota siap cetak (`window.print()`).
5. **Laporan Keuangan Relasional (`laporan.php`)**
   - Tunjukkan tabel **Rekap per Jenis Layanan** (`INNER JOIN` + `GROUP BY`) dan rincian order.
   - Coba filter berdasarkan jenis layanan atau status, lalu tunjukkan tombol **Cetak Laporan**.
6. **Pembuktian Hak Akses Role (Logout → Login Kasir)**
   - Klik **Keluar**, lalu masuk sebagai **Kasir** (`kasir` / `kasir123`).
   - Buka menu **Data Layanan** → tunjukkan mode baca saja (tidak ada tombol Tambah/Ubah/Hapus) dan jelaskan bahwa di backend `actions/layanan_action.php` juga dilindungi `require_role('admin')`.
   - Buka **Rekap Transaksi** → tunjukkan Kasir tetap bisa mencatat transaksi baru dan mengubah status pengerjaan.

---

## 3. Peta Cepat Lokasi Kode (Jika Penguji Bertanya)

| Pertanyaan Penguji | File yang Langsung Dibuka | Bagian / Penjelasan Singkat |
|---|---|---|
| *"Mana koneksi ke database MySQL?"* | `config/database.php` | Fungsi `db(): PDO` pakai `PDO::ATTR_EMULATE_PREPARES => false` (anti SQL Injection). |
| *"Mana struktur tabel dan relasi Foreign Key-nya?"* | `database/schema.sql` | 3 tabel (`users`, `layanan`, `transaksi`) dengan `CONSTRAINT fk_transaksi_layanan FOREIGN KEY (layanan_id) REFERENCES layanan(id)`. |
| *"Mana logika login & beda role Admin vs Kasir?"* | `includes/auth.php` | `attempt_login()` (`password_verify` bcrypt + `session_regenerate_id`), `is_admin()`, dan `require_role('admin')`. |
| *"Mana proses Tambah, Ubah, Hapus Layanan?"* | `actions/layanan_action.php` | 3 blok terpisah jelas: `create`, `update`, `delete` + tangkap error kode `1451` (Foreign Key). |
| *"Mana perhitungan tarif otomatis & update status?"* | `actions/transaksi_action.php` | Blok `create` mengambil `harga_per_kg` asli dari tabel `layanan` lalu menghitung `$beratKg * $hargaSatuan` di server; blok `update_status` mengisi `tanggal_selesai = NOW()`. |
| *"Mana query JOIN untuk laporan keuangan?"* | `laporan.php` | Query `INNER JOIN layanan l ON t.layanan_id = l.id` dan `GROUP BY l.id` untuk rekap omzet per layanan. |
| *"Mana kode tampilan responsif & modal modern?"* | `assets/css/components.css` & `assets/js/app.js` | Tabel pakai `@container (width < 680px)`, modal pakai `<dialog>` native (`closedby="any"` + fallback), validasi pakai `:user-invalid` + `aria-invalid` di `assets/js/validation.js`. |
