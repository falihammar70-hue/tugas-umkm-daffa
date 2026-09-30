# Website UMKM Hebat - E-Commerce PHP Native & MySQL

Aplikasi website toko online e-commerce untuk pemberdayaan Usaha Mikro, Kecil, dan Menengah (UMKM) berbasis **PHP Native** murni (tanpa framework) dan **MySQL**, dengan antarmuka modern, bersih, dan responsif menggunakan **Tailwind CSS** dan **FontAwesome Icons**.

---

## 🌟 Fitur Utama

### 1. Autentikasi & Pengguna Multi-Role
- **Login & Register**: Sistem login untuk **Admin** dan **Pelanggan** dengan enkripsi **MD5** pada kolom password `tb_user`.
- **Proteksi Halaman**: Middleware/helper fungsi `requireLogin()`, `requireAdmin()`, dan proteksi sesi yang aman dari akses tidak sah.
- **Profil Pelanggan**: Edit informasi diri (nama, username, nomor handphone, alamat pengiriman) serta opsi ganti kata sandi.

### 2. Frontend Pelanggan & Belanja
- **Landing Page & Katalog**: Banner promo hero modern, tab filter kategori dinamis, dan fitur pencarian produk real-time.
- **Modal Detail Produk (Quick View)**: Menampilkan spesifikasi, stok, deskripsi lengkap, dan form pembelian langsung.
- **Keranjang Belanja (PHP Session)**: Tambah kuantitas, kurangi, hapus produk per-item, serta kosongkan keranjang belanja.
- **Pilihan Pengiriman & Pembayaran**: Mendukung aneka ekspedisi (Kurir Toko, JNE, SiCepat) dan metode bayar (BCA, BRI, Mandiri, QRIS, COD).
- **Checkout Otomatis**: Memvalidasi ketersediaan stok produk di database, otomatis memotong stok barang, membuat record transaksi & detail transaksi, lalu mengosongkan keranjang.
- **Cetak Invoice / Struk Pembelian**: Struk digital rapi dengan tombol cetak langsung menggunakan `window.print()` dan format ramah cetak media.

### 3. Panel Dashboard Admin
- **Sidebar & Topbar Responsif**: Navigasi intuitif untuk mengelola seluruh aspek toko.
- **Statistik Penjualan**: Ringkasan total produk, total pesanan, total pelanggan, total omset pendapatan, dan peringatan stok menipis (&le; 15 pcs).
- **Manajemen Produk (CRUD)**: Tambah produk baru, edit info/stok/harga, hapus produk, serta fungsionalitas unggah foto produk ke folder `assets/images/`.
- **Manajemen Transaksi**: Melihat seluruh pesanan masuk, cetak ulang invoice/struk, dan hapus transaksi.
- **Manajemen Pelanggan & Reset Password**: Daftar akun pelanggan terdaftar dan fitur **Reset Password** sekali klik ke password default (`123456`) terenkripsi jika pelanggan lupa kata sandi.

---

## 📁 Struktur Direktori

```text
/umkm
├── config/
│   └── koneksi.php              # Konfigurasi database mysqli & helper autentikasi sesi
├── database.sql                 # Skrip SQL pembuatan tabel dan data dummy
├── assets/
│   └── images/                  # Foto produk katalog dan hasil upload foto
├── includes/
│   ├── navbar.php               # Navigasi atas pelanggan & badge keranjang belanja
│   └── footer.php               # Footer responsif dengan link & info toko
├── index.php                    # Halaman beranda & katalog etalase produk
├── login.php                    # Halaman login Admin & Pelanggan
├── register.php                 # Halaman pendaftaran akun pelanggan baru
├── logout.php                   # Pembersihan sesi dan logout
├── keranjang.php                # Halaman keranjang belanja (PHP Session)
├── checkout.php                 # Logika validasi stok, pemotongan stok & insert order
├── profil.php                   # Halaman profil pelanggan & tabel riwayat pesanan
├── invoice.php                  # Cetak struk/nota pembelian dengan window.print()
└── admin/
    ├── layout_header.php        # Header & Sidebar navigasi admin
    ├── layout_footer.php        # Footer penutup layout admin
    ├── dashboard.php            # Statistik & ringkasan aktivitas toko
    ├── produk.php               # CRUD Produk & upload gambar
    ├── transaksi.php            # Data riwayat transaksi pelanggan
    └── pelanggan.php            # Daftar pelanggan & fitur reset password
```

---

## 🚀 Panduan Instalasi & Menjalankan

### Langkah 1: Persiapan Database
1. Pastikan layanan MySQL / MariaDB (misalnya melalui XAMPP / MAMP / Homebrew) sudah berjalan.
2. Buka phpMyAdmin (biasanya di `http://localhost/phpmyadmin`) atau terminal MySQL.
3. Import file `database.sql` yang berada di root project ini ke MySQL:
   ```bash
   mysql -u root -p < database.sql
   ```
   *Skrip ini akan otomatis membuat database `db_toko` dan tabel-tabel serta 3 dummy data per tabel.*

Untuk database `db_toko` yang sudah berisi data sebelum fitur kategori, jalankan `migrasi_kategori.sql` satu kali melalui phpMyAdmin atau MySQL. Migrasi ini membuat tabel kategori, memindahkan kategori produk yang sudah ada, lalu memasang foreign key.

### Langkah 2: Konfigurasi Koneksi (Jika Diperlukan)
Buka file `config/koneksi.php` jika user/password MySQL Anda berbeda dari default:
```php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'db_toko';
```

### Langkah 3: Jalankan Web Server
Anda dapat meletakkannya di folder `htdocs` XAMPP, atau menjalankan PHP Built-in server langsung dari direktori project:
```bash
cd /Users/nurhidayat/Desktop/Develop/global/umkm
php -S localhost:8000
```
Buka browser dan akses: `http://localhost:8000`

---

## 🔑 Akun Bawaan untuk Uji Coba (Demo Testing)

| Peran (Role) | Username / Email | Password | Hak Akses & URL Awal |
| :--- | :--- | :--- | :--- |
| **Admin** | `daffa1` | `daffa33` | Dashboard Admin (`/admin/dashboard.php`) |
| **Pelanggan** | `budi` atau `budi@gmail.com` | `pelanggan123` | Katalog & Belanja (`/index.php`) |
| **Pelanggan 2** | `siti` atau `siti@gmail.com` | `pelanggan123` | Riwayat Transaksi (`/profil.php`) |

*Catatan: Jika Admin menggunakan fitur **Reset Password** pada pelanggan, password pelanggan tersebut akan berubah menjadi `123456`.*

