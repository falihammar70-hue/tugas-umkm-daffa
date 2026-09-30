<?php
// checkout.php - Proses Checkout & Pembuatan Transaksi
require_once __DIR__ .  '/config/koneksi.php';
/** @var mysqli $koneksi */
global $koneksi;

// Pastikan user sudah login
if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = 'keranjang.php';
    $_SESSION['flash_error'] = "Silakan masuk ke akun Anda terlebih dahulu untuk menyelesaikan transaksi.";
    header("Location: login.php");
    exit;
}

// Pastikan keranjang tidak kosong
if (empty($_SESSION['keranjang'])) {
    $_SESSION['flash_error'] = "Keranjang belanja Anda masih kosong.";
    header("Location: index.php#katalog");
    exit;
}

// Pastikan form dikirim via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: keranjang.php");
    exit;
}

$id_pelanggan = (int)$_SESSION['user_id'];
$tanggal = date('Y-m-d');
$keranjang = $_SESSION['keranjang'];

// 1. Validasi Stok Seluruh Item Keranjang di Database
$total_harga = 0;
$items_to_process = [];

foreach ($keranjang as $id_prod => $qty) {
    $id_prod = (int)$id_prod;
    $qty = (int)$qty;

    if ($qty <= 0) continue;

    $query = "SELECT id, nama, harga, stok FROM tb_produk WHERE id = $id_prod";
    $result = mysqli_query($koneksi, $query);

    if (!$result || mysqli_num_rows($result) === 0) {
        $_SESSION['flash_error'] = "Salah satu produk dalam keranjang tidak ditemukan di katalog.";
        header("Location: keranjang.php");
        exit;
    }

    $prod = mysqli_fetch_assoc($result);

    if ($prod['stok'] < $qty) {
        $_SESSION['flash_error'] = "Stok untuk produk '{$prod['nama']}' tidak mencukupi (sisa: {$prod['stok']} pcs). Mohon sesuaikan jumlah pesanan Anda.";
        header("Location: keranjang.php");
        exit;
    }

    $subtotal = $prod['harga'] * $qty;
    $total_harga += $subtotal;

    $items_to_process[] = [
        'id' => $prod['id'],
        'nama' => $prod['nama'],
        'harga' => $prod['harga'],
        'qty' => $qty,
        'stok_sekarang' => $prod['stok']
    ];
}

if (empty($items_to_process)) {
    $_SESSION['flash_error'] = "Tidak ada produk valid yang dapat diproses.";
    header("Location: keranjang.php");
    exit;
}

// 2. Mulai Transaksi Database
mysqli_begin_transaction($koneksi);

try {
    // A. Simpan ke tb_transaksi
    $query_transaksi = "INSERT INTO tb_transaksi (id_pelanggan, tanggal, total_harga) 
                        VALUES ($id_pelanggan, '$tanggal', $total_harga)";
    if (!mysqli_query($koneksi, $query_transaksi)) {
        throw new Exception("Gagal membuat data transaksi: " . mysqli_error($koneksi));
    }

    $id_transaksi_baru = mysqli_insert_id($koneksi);

    // B. Simpan ke tb_detail & Potong Stok Produk
    foreach ($items_to_process as $item) {
        $id_produk = $item['id'];
        $jumlah = $item['qty'];
        $stok_baru = $item['stok_sekarang'] - $jumlah;

        // Insert tb_detail
        $query_detail = "INSERT INTO tb_detail (id_transaksi, id_produk, jumlah) 
                         VALUES ($id_transaksi_baru, $id_produk, $jumlah)";
        if (!mysqli_query($koneksi, $query_detail)) {
            throw new Exception("Gagal menyimpan rincian produk transaksi: " . mysqli_error($koneksi));
        }

        // Potong stok tb_produk
        $query_stok = "UPDATE tb_produk SET stok = $stok_baru WHERE id = $id_produk";
        if (!mysqli_query($koneksi, $query_stok)) {
            throw new Exception("Gagal memperbarui sisa stok produk: " . mysqli_error($koneksi));
        }
    }

    // Commit jika semua query berhasil
    mysqli_commit($koneksi);

    // Kosongkan keranjang belanja
    $_SESSION['keranjang'] = [];
    $_SESSION['flash_success'] = "Pesanan berhasil dibuat! Terima kasih telah mendukung produk UMKM kami.";

    // Redirect ke halaman invoice
    header("Location: invoice.php?id=" . $id_transaksi_baru);
    exit;

} catch(Exception $e) {
    // Rollback jika ada error
    mysqli_rollback($koneksi);
    $_SESSION['flash_error'] = "Terjadi kesalahan saat memproses transaksi: " . $e->getMessage();
    header("Location: keranjang.php");
    exit;
}

