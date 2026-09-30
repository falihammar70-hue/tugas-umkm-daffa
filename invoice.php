<?php
// invoice.php - Cetak Invoice & Struk Transaksi
require_once __DIR__ . '/config/koneksi.php';
/** @var mysqli $koneksi */
global $koneksi;
// Wajib Login
requireLogin('login.php');

$id_transaksi = (int)($_GET['id'] ?? 0);

if ($id_transaksi <= 0) {
    die("ID Transaksi tidak valid.");
}

// Ambil Data Transaksi & Pelanggan
$query_trx = "SELECT t.*, u.nama, u.email, u.hp, u.alamat, u.username
              FROM tb_transaksi t 
              JOIN tb_user u ON t.id_pelanggan = u.id 
              WHERE t.id_transaksi = $id_transaksi LIMIT 1";
$res_trx = mysqli_query($koneksi, $query_trx);

if (!$res_trx || mysqli_num_rows($res_trx) === 0) {
    die("Transaksi tidak ditemukan.");
}

$trx = mysqli_fetch_assoc($res_trx);

// Keamanan: Hanya admin atau pemilik transaksi yang boleh melihat invoice ini
if (!isAdmin() && (int)$_SESSION['user_id'] !== (int)$trx['id_pelanggan']) {
    die("Akses ditolak. Anda tidak memiliki izin untuk melihat invoice ini.");
}

// Ambil Rincian Detail Produk dari tb_detail
$query_detail = "SELECT d.*, p.nama as nama_produk, p.harga as harga_produk, k.nama_kategori AS kategori
                 FROM tb_detail d 
                 JOIN tb_produk p ON d.id_produk = p.id 
                 JOIN tb_kategori k ON k.id_kategori = p.id_kategori
                 WHERE d.id_transaksi = $id_transaksi";
$res_detail = mysqli_query($koneksi, $query_detail);

$items = [];
$total_kalkulasi = 0;
if ($res_detail) {
    while ($row = mysqli_fetch_assoc($res_detail)) {
        $sub = $row['harga_produk'] * $row['jumlah'];
        $total_kalkulasi += $sub;
        $row['subtotal'] = $sub;
        $items[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #TRX-<?= str_pad($trx['id_transaksi'], 5, '0', STR_PAD_LEFT) ?> - UMKM Hebat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
            }
            .invoice-box {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-8 px-4 font-sans text-gray-800">

    <!-- Action Bar (Hidden on Print) -->
    <div class="max-w-3xl mx-auto mb-6 flex flex-wrap items-center justify-between gap-4 no-print">
        <div class="flex items-center gap-2">
            <?php if (isAdmin()): ?>
                <a href="admin/transaksi.php" class="px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Admin Transaksi
                </a>
            <?php else: ?>
                <a href="profil.php#riwayat" class="px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Riwayat Pesanan
                </a>
            <?php endif; ?>
            <a href="index.php" class="px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-house"></i> Beranda
            </a>
        </div>

        <button onclick="window.print()" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-200 transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
        </button>
    </div>

    <!-- Invoice Paper Box -->
    <div class="invoice-box max-w-3xl mx-auto bg-white rounded-3xl p-8 sm:p-12 shadow-xl border border-gray-200">
        <!-- Invoice Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-8 border-b border-gray-200 gap-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-600 flex items-center justify-center text-white font-bold text-2xl shadow-md shadow-emerald-200">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-gray-900 tracking-tight">UMKM<span class="text-emerald-600">Hebat</span></h1>
                    <p class="text-xs text-gray-500">Sentra Produk Lokal Nusantara Berdaya</p>
                </div>
            </div>

            <div class="sm:text-right">
                <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-lg uppercase tracking-wider mb-1">
                    Lunas / Selesai
                </span>
                <div class="text-xl font-extrabold text-gray-900">
                    #TRX-<?= str_pad($trx['id_transaksi'], 5, '0', STR_PAD_LEFT) ?>
                </div>
                <div class="text-xs text-gray-500">
                    Tanggal: <?= date('d F Y', strtotime($trx['tanggal'])) ?>
                </div>
            </div>
        </div>

        <!-- Info Pemesan & Toko -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 py-8 text-xs border-b border-gray-100">
            <div>
                <h3 class="font-bold text-gray-400 uppercase tracking-wider mb-2">Informasi Pembeli:</h3>
                <p class="text-sm font-bold text-gray-900"><?= htmlspecialchars($trx['nama']) ?></p>
                <p class="text-gray-600 mt-1"><i class="fa-solid fa-phone text-emerald-600 mr-1.5"></i> <?= htmlspecialchars($trx['hp']) ?></p>
                <p class="text-gray-600"><i class="fa-solid fa-envelope text-emerald-600 mr-1.5"></i> <?= htmlspecialchars($trx['email']) ?></p>
                <p class="text-gray-600 mt-1 leading-relaxed"><i class="fa-solid fa-location-dot text-emerald-600 mr-1.5"></i> <?= htmlspecialchars($trx['alamat']) ?></p>
            </div>

            <div class="sm:text-right">
                <h3 class="font-bold text-gray-400 uppercase tracking-wider mb-2">Informasi Toko:</h3>
                <p class="text-sm font-bold text-gray-900">UMKM Hebat</p>
                <p class="text-gray-600 mt-1"><i class="fa-solid fa-phone text-emerald-600 mr-1.5"></i> +62 812 3456 7890</p>
                <p class="text-gray-600"><i class="fa-solid fa-envelope text-emerald-600 mr-1.5"></i> <a href="mailto:billing@umkmhebat.id" class="text-emerald-600 hover:underline">billing@umkmhebat.id</a></p>
            </div>
        </div>

        <!-- Tabel Rincian Produk -->
        <div class="overflow-x-uto mt-6">
            <h3 class="font-bold text-gray-400 uppercase tracking-wider mb-4 text-xs">Rincian Pesanan:</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 font-bold border-y border-gray-200">
                            <th class="py-3 px-3">No</th>
                            <th class="py-3 px-3">Nama Produk</th>
                            <th class="py-3 px-3">Kategori</th>
                            <th class="py-3 px-3 text-right">Harga Satuan</th>
                            <th class="py-3 px-3 text-center">Jumlah</th>
                            <th class="py-3 px-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $no = 1; foreach ($items as $it): ?>
                            <tr>
                                <td class="py-3 px-3 text-gray-500 font-medium"><?= $no++ ?></td>
                                <td class="py-3 px-3 font-bold text-gray-900">
                                    <?= htmlspecialchars($it['nama_produk']) ?>
                                </td>
                                <td class="py-3 px-3 text-gray-500">
                                    <span class="bg-gray-100 px-2 py-0.5 rounded text-[11px]"><?= htmlspecialchars($it['kategori']) ?></span>
                                </td>
                                <td class="py-3 px-3 text-right text-gray-700">
                                    <?= formatRupiah($it['harga_produk']) ?>
                                </td>
                                <td class="py-3 px-3 text-center font-bold text-gray-900">
                                    <?= $it['jumlah'] ?> pcs
                                </td>
                                <td class="py-3 px-3 text-right font-bold text-gray-900">
                                    <?= formatRupiah($it['subtotal']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Grand Total Calculation -->
            <div class="mt-6 border-t border-gray-200 pt-4 flex flex-col items-end text-xs">
                <div class="w-full sm:w-72 space-y-2">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal Barang:</span>
                        <span class="font-bold text-gray-800"><?= formatRupiah($total_kalkulasi) ?></span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Biaya Pengiriman & Pajak:</span>
                        <span class="font-bold text-emerald-600">Rp 0 (Gratis)</span>
                    </div>
                    <div class="flex justify-between text-base font-black text-gray-900 border-t border-gray-200 pt-3">
                        <span>Total Bayar:</span>
                        <span class="text-emerald-700"><?= formatRupiah($trx['total_harga']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Struk & Catatan -->
        <div class="mt-8 pt-8 border-t border-gray-100 text-center text-xs text-gray-500 space-y-2">
            <p class="font-semibold text-gray-700">Terima kasih atas kepercayaan dan dukungan Anda terhadap produk UMKM Indonesia!</p>
            <p>Struk ini merupakan bukti pembayaran yang sah dan diterbitkan secara digital oleh sistem UMKM Hebat.</p>
            <p class="text-[10px] text-gray-400">Dicetak pada: <?= date('d-m-Y H:i:s') ?></p>
        </div>
    </div>

</body>
</html>

