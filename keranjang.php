<?php
// keranjang.php - Keranjang Belanja Pelanggan
require_once __DIR__ . '/config/koneksi.php';
/** @var mysqli $koneksi */
global $koneksi;
// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// Handle Aksi Keranjang (Update Qty, Hapus, Kosongkan)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    $id_produk = (int)($_POST['id_produk'] ?? 0);

    if ($aksi === 'update') {
        $qty = max(1, (int)($_POST['jumlah'] ?? 1));
        
        // Cek stok produk
        $cek_stok = mysqli_query($koneksi, "SELECT stok, nama FROM tb_produk WHERE id = $id_produk");
        if ($cek_stok && $prod = mysqli_fetch_assoc($cek_stok)) {
            if ($qty > (int)$prod['stok']) {
                $_SESSION['flash_error'] = "Jumlah melebihi stok yang tersedia ({$prod['stok']} pcs).";
                $_SESSION['keranjang'][$id_produk] = (int)$prod['stok'];
            } else {
                $_SESSION['keranjang'][$id_produk] = $qty;
                $_SESSION['flash_success'] = "Kuantitas berhasil diperbarui.";
            }
        }
    } elseif ($aksi === 'tambah') {
        $cek_stok = mysqli_query($koneksi, "SELECT stok, nama FROM tb_produk WHERE id = $id_produk");
        if ($cek_stok && $prod = mysqli_fetch_assoc($cek_stok)) {
            $cur = $_SESSION['keranjang'][$id_produk] ?? 0;
            if ($cur + 1 > (int)$prod['stok']) {
                $_SESSION['flash_error'] = "Stok untuk '{$prod['nama']}' tidak mencukupi.";
            } else {
                $_SESSION['keranjang'][$id_produk] = $cur + 1;
            }
        }
    } elseif ($aksi === 'kurang') {
        if (isset($_SESSION['keranjang'][$id_produk])) {
            if ($_SESSION['keranjang'][$id_produk] > 1) {
                $_SESSION['keranjang'][$id_produk] -= 1;
            } else {
                unset($_SESSION['keranjang'][$id_produk]);
            }
        }
    } elseif ($aksi === 'hapus') {
        if (isset($_SESSION['keranjang'][$id_produk])) {
            unset($_SESSION['keranjang'][$id_produk]);
            $_SESSION['flash_success'] = "Produk berhasil dihapus dari keranjang.";
        }
    } elseif ($aksi === 'kosongkan') {
        $_SESSION['keranjang'] = [];
        $_SESSION['flash_success'] = "Keranjang belanja berhasil dikosongkan.";
    }

    header("Location: keranjang.php");
    exit;
}

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Ambil data produk di keranjang dari database
$item_keranjang = [];
$subtotal_keseluruhan = 0;

if (!empty($_SESSION['keranjang'])) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['keranjang'])));
    $query = "SELECT p.*, k.nama_kategori AS kategori FROM tb_produk p JOIN tb_kategori k ON k.id_kategori = p.id_kategori WHERE p.id IN ($ids)";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row['id'];
            $qty = $_SESSION['keranjang'][$id] ?? 0;
            if ($qty > 0) {
                $subtotal = $row['harga'] * $qty;
                $subtotal_keseluruhan += $subtotal;
                $row['qty'] = $qty;
                $row['subtotal'] = $subtotal;
                $item_keranjang[] = $row;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - UMKM Hebat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen flex flex-col">

    <!-- Navbar -->
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full">
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs text-gray-500 mb-6">
            <a href="index.php" class="hover:text-emerald-600">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-gray-800 font-semibold">Keranjang Belanja</span>
        </div>

        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 mb-6 flex items-center gap-3">
            <i class="fa-solid fa-cart-shopping text-emerald-600"></i> Keranjang Belanja Anda
        </h1>

        <!-- Flash Alert -->
        <?php if ($flash_success): ?>
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                <span><?= htmlspecialchars($flash_success) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($flash_error): ?>
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-medium flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg"></i>
                <span><?= htmlspecialchars($flash_error) ?></span>
            </div>
        <?php endif; ?>

        <?php if (empty($item_keranjang)): ?>
            <!-- Keranjang Kosong State -->
            <div class="bg-white rounded-3xl p-12 text-center border border-gray-200 shadow-sm max-w-xl mx-auto my-12">
                <div class="w-24 h-24 mx-auto bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center text-4xl mb-4">
                    <i class="fa-solid fa-cart-arrow-down"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800 mb-2">Keranjang Belanja Masih Kosong</h2>
                <p class="text-sm text-gray-500 mb-8">
                    Sepertinya Anda belum memilih produk UMKM untuk dibeli. Yuk, jelajahi produk lokal berkualitas sekarang juga!
                </p>
                <a href="index.php#katalog" class="inline-flex items-center gap-2 px-6 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-200 transition">
                    <i class="fa-solid fa-store"></i> Mulai Berbelanja
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Daftar Item Keranjang -->
                <div class="lg:col-span-8 bg-white rounded-3xl p-6 border border-gray-200 shadow-sm space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                        <span class="text-sm font-bold text-gray-700">Daftar Produk (<?= count($item_keranjang) ?> jenis barang)</span>
                        <form action="keranjang.php" method="POST" onsubmit="return confirm('Kosongkan semua barang dari keranjang belanja?');">
                            <input type="hidden" name="aksi" value="kosongkan">
                            <button type="submit" class="text-xs text-rose-600 hover:text-rose-700 font-semibold flex items-center gap-1.5 hover:underline">
                                <i class="fa-regular fa-trash-can"></i> Kosongkan Keranjang
                            </button>
                        </form>
                    </div>

                    <!-- List Items -->
                    <div class="divide-y divide-gray-100">
                        <?php foreach ($item_keranjang as $item): ?>
                            <?php
                                $foto_produk = trim((string)($item['poto'] ?? ''));
                                $foto_path = preg_match('/^https?:\/\//i', $foto_produk)
                                    ? $foto_produk
                                    : (!empty($foto_produk) && file_exists(__DIR__ . '/assets/images/' . $foto_produk)
                                        ? 'assets/images/' . $foto_produk
                                        : 'https://placehold.co/150x150/059669/ffffff?text=' . urlencode($item['nama']));
                            ?>
                            <div class="py-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <img src="<?= htmlspecialchars($foto_path) ?>" alt="<?= htmlspecialchars($item['nama']) ?>" 
                                         class="w-20 h-20 rounded-2xl object-cover border border-gray-100 shadow-sm flex-shrink-0">
                                    <div>
                                        <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">
                                            <?= htmlspecialchars($item['kategori']) ?>
                                        </span>
                                        <h3 class="font-bold text-gray-900 text-sm mt-1">
                                            <?= htmlspecialchars($item['nama']) ?>
                                        </h3>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Harga: <span class="font-semibold text-gray-700"><?= formatRupiah($item['harga']) ?></span>
                                            <span class="mx-1 text-gray-300">|</span>
                                            Stok: <?= $item['stok'] ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto">
                                    <!-- Counter Qty Form -->
                                    <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden bg-gray-50">
                                        <!-- Tombol Kurang -->
                                        <form action="keranjang.php" method="POST">
                                            <input type="hidden" name="aksi" value="kurang">
                                            <input type="hidden" name="id_produk" value="<?= $item['id'] ?>">
                                            <button type="submit" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-200 transition">
                                                <i class="fa-solid fa-minus text-xs"></i>
                                            </button>
                                        </form>

                                        <span class="w-10 text-center font-bold text-sm text-gray-800">
                                            <?= $item['qty'] ?>
                                        </span>

                                        <!-- Tombol Tambah -->
                                        <form action="keranjang.php" method="POST">
                                            <input type="hidden" name="aksi" value="tambah">
                                            <input type="hidden" name="id_produk" value="<?= $item['id'] ?>">
                                            <button type="submit" <?= $item['qty'] >= $item['stok'] ? 'disabled' : '' ?> 
                                                    class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-200 disabled:opacity-40 disabled:cursor-not-allowed transition">
                                                <i class="fa-solid fa-plus text-xs"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Subtotal Item -->
                                    <div class="text-right min-w-[100px]">
                                        <div class="text-xs text-gray-400">Total</div>
                                        <div class="font-extrabold text-sm text-emerald-700">
                                            <?= formatRupiah($item['subtotal']) ?>
                                        </div>
                                    </div>

                                    <!-- Tombol Hapus -->
                                    <form action="keranjang.php" method="POST">
                                        <input type="hidden" name="aksi" value="hapus">
                                        <input type="hidden" name="id_produk" value="<?= $item['id'] ?>">
                                        <button type="submit" class="p-2 text-gray-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition" title="Hapus Produk">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <a href="index.php#katalog" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 flex items-center gap-2">
                            <i class="fa-solid fa-arrow-left"></i> Lanjutkan Belanja Produk Lainnya
                        </a>
                    </div>
                </div>

                <!-- Ringkasan Pesanan & Form Checkout -->
                <div class="lg:col-span-4 bg-white rounded-3xl p-6 border border-gray-200 shadow-sm space-y-6 sticky top-24">
                    <h3 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3 flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-emerald-600"></i> Ringkasan Belanja
                    </h3>

                    <form action="checkout.php" method="POST" class="space-y-4">
                        <!-- Pilihan Metode Pengiriman -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Metode Pengiriman</label>
                            <select name="pengiriman" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-white">
                                <option value="Kurir Toko (Rp 0 - Khusus Area Sekitar)">Kurir Toko UMKM (Gratis Ongkir Lokal)</option>
                                <option value="JNE Reguler">JNE Express Reguler</option>
                                <option value="SiCepat Kilat">SiCepat Kilat Ekspedisi</option>
                                <option value="Ambil di Tempat">Ambil Sendiri di Toko UMKM</option>
                            </select>
                        </div>

                        <!-- Pilihan Metode Pembayaran -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Metode Pembayaran</label>
                            <select name="pembayaran" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-white">
                                <option value="Transfer Bank BCA">Transfer Bank BCA (Virtual Account)</option>
                                <option value="Transfer Bank BRI">Transfer Bank BRI</option>
                                <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                                <option value="QRIS Semua E-Wallet">QRIS (GoPay, OVO, DANA, ShopeePay)</option>
                                <option value="COD / Bayar di Tempat">Bayar di Tempat (COD)</option>
                            </select>
                        </div>

                        <!-- Catatan Pembeli -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Catatan Pesanan (Opsional)</label>
                            <textarea name="catatan" rows="2" placeholder="Contoh: Tolong packing bubble wrap lebih tebal" 
                                      class="w-full px-3.5 py-2 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
                        </div>

                        <hr class="border-gray-100 my-4">

                        <!-- Kalkulasi -->
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between text-gray-500">
                                <span>Subtotal Produk</span>
                                <span class="font-semibold text-gray-700"><?= formatRupiah($subtotal_keseluruhan) ?></span>
                            </div>
                            <div class="flex justify-between text-gray-500">
                                <span>Biaya Layanan & Pajak</span>
                                <span class="font-semibold text-emerald-600">Rp 0 (Gratis)</span>
                            </div>
                            <div class="flex justify-between text-gray-900 font-extrabold text-base pt-3 border-t border-gray-100">
                                <span>Total Pembayaran</span>
                                <span class="text-emerald-700"><?= formatRupiah($subtotal_keseluruhan) ?></span>
                            </div>
                        </div>

                        <?php if (isLoggedIn()): ?>
                            <button type="submit" 
                                    class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-200 transition transform hover:-translate-y-0.5 active:scale-98 flex items-center justify-center gap-2 mt-4">
                                <i class="fa-solid fa-lock"></i> Lanjutkan ke Checkout
                            </button>
                        <?php else: ?>
                            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs mt-4">
                                <p class="font-semibold mb-1"><i class="fa-solid fa-circle-info mr-1"></i> Perhatian:</p>
                                Anda perlu login sebagai pelanggan untuk menyelesaikan pesanan ini.
                            </div>
                            <a href="login.php" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-200 transition flex items-center justify-center gap-2 mt-2">
                                <i class="fa-solid fa-right-to-bracket"></i> Masuk untuk Checkout
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>

