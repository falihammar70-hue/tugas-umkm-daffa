<?php
/**
 * Konfigurasi Database & Helper Sistem UMKM
 * Menggunakan ekstensi mysqli PHP Native
 */

// Konfigurasi Database
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'db_toko';

// Menghubungkan ke database
global $koneksi;
$koneksi = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
$GLOBALS['koneksi'] = $koneksi;

// Penanganan error koneksi yang aman
if (!$koneksi) {
    $db_error = mysqli_connect_error();
    // Jika script dijalankan lewat CLI atau web, berikan pesan informatif
    if (php_sapi_name() !== 'cli') {
        die("
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 25px; border: 1px solid #f87171; background: #fef2f2; border-radius: 10px; color: #991b1b;'>
            <h2 style='margin-top:0;'>⚠️ Gagal Terhubung ke Database</h2>
            <p>Pastikan MySQL server (seperti XAMPP / MariaDB) sudah aktif dan database <strong>`db_toko`</strong> sudah di-import.</p>
            <p><strong>Pesan Kesalahan:</strong> <code>" . htmlspecialchars($db_error) . "</code></p>
            <hr style='border: none; border-top: 1px solid #fca5a5; margin: 15px 0;'>
            <p style='font-size: 13px; color: #b91c1c;'>Langkah: Buka phpMyAdmin, buat database <strong>db_toko</strong>, lalu import file <strong>database.sql</strong>.</p>
        </div>
        ");
    }
}

// Inisialisasi Session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Cek apakah pengguna sudah login
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Cek apakah user yang login memiliki role admin
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Cek apakah user yang login memiliki role pelanggan
 */
function isPelanggan() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'pelanggan';
}

/**
 * Mengambil data pengguna yang sedang login dari session
 */
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    return [
        'id' => $_SESSION['user_id'],
        'nama' => $_SESSION['nama'] ?? '',
        'username' => $_SESSION['username'] ?? '',
        'email' => $_SESSION['email'] ?? '',
        'role' => $_SESSION['role'] ?? ''
    ];
}

/**
 * Proteksi Halaman: Wajib Login
 */
function requireLogin($redirect = 'login.php') {
    if (!isLoggedIn()) {
        $_SESSION['flash_error'] = "Silakan login terlebih dahulu untuk mengakses halaman ini.";
        header("Location: $redirect");
        exit;
    }
}

/**
 * Proteksi Halaman Admin
 */
function requireAdmin($redirect = '../login.php') {
    if (!isAdmin()) {
        $_SESSION['flash_error'] = "Akses ditolak! Halaman ini hanya untuk Administrator.";
        header("Location: $redirect");
        exit;
    }
}

/**
 * Format angka ke format mata uang Rupiah (Rp)
 */
function formatRupiah($angka) {
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

/**
 * Menghitung total kuantitas item dalam keranjang session
 */
function getCartCount() {
    $total = 0;
    if (isset($_SESSION['keranjang']) && is_array($_SESSION['keranjang'])) {
        foreach ($_SESSION['keranjang'] as $qty) {
            $total += (int)$qty;
        }
    }
    return $total;
}

/**
 * Bersihkan input string dari XSS
 */
function sanitize($koneksi, $data) {
    return mysqli_real_escape_string($koneksi, trim($data));
}

