<?php
// admin/dashboard.php - Halaman Utama Admin
require_once __DIR__ . '/../config/koneksi.php';
/** @var mysqli $koneksi */
global $koneksi;
$page_title = 'Ringkasan Dashboard';
require_once __DIR__ . '/layout_header.php';

// 1. Statistik
$stat_produk = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM tb_produk"))[0] ?? 0;
$stat_transaksi = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM tb_transaksi"))[0] ?? 0;
$stat_pelanggan = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM tb_user WHERE role = 'pelanggan'"))[0] ?? 0;
$stat_omset = mysqli_fetch_row(mysqli_query($koneksi, "SELECT SUM(total_harga) FROM tb_transaksi"))[0] ?? 0;

// 2. Transaksi Terbaru (5 transaksi terakhir)
$query_latest_trx = "SELECT t.*, u.nama as nama_pelanggan, u.hp
                     FROM tb_transaksi t 
                     JOIN tb_user u ON t.id_pelanggan = u.id 
                     ORDER BY t.id_transaksi DESC LIMIT 5";
$res_latest_trx = mysqli_query($koneksi, $query_latest_trx);
$latest_trx = [];
if ($res_latest_trx) {
    while ($r = mysqli_fetch_assoc($res_latest_trx)) {
        $latest_trx[] = $r;
    }
}

// 3. Produk dengan Stok Menipis (<= 15 pcs)
$query_low_stock = "SELECT p.*, k.nama_kategori AS kategori FROM tb_produk p JOIN tb_kategori k ON k.id_kategori = p.id_kategori WHERE p.stok <= 15 ORDER BY p.stok ASC LIMIT 5";
$res_low_stock = mysqli_query($koneksi, $query_low_stock);
$low_stock_items = [];
if ($res_low_stock) {
    while ($r = mysqli_fetch_assoc($res_low_stock)) {
        $low_stock_items[] = $r;
    }
}
?>

<!-- Stat Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card 1: Total Pendapatan -->
    <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl flex-shrink-0">
            <i class="fa-solid fa-rupiah-sign"></i>
        </div>
        <div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Total Omset</span>
            <span class="text-xl font-black text-gray-900 leading-tight"><?= formatRupiah($stat_omset) ?></span>
            <span class="text-[11px] text-emerald-600 font-medium block mt-0.5"><i class="fa-solid fa-arrow-trend-up mr-1"></i> Dari total pesanan</span>
        </div>
    </div>

    <!-- Card 2: Total Transaksi -->
    <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl flex-shrink-0">
            <i class="fa-solid fa-receipt"></i>
        </div>
        <div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Total Transaksi</span>
            <span class="text-2xl font-black text-gray-900 leading-tight"><?= number_format($stat_transaksi) ?></span>
            <span class="text-[11px] text-blue-600 font-medium block mt-0.5">Pesanan berhasil</span>
        </div>
    </div>

    <!-- Card 3: Total Produk -->
    <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl flex-shrink-0">
            <i class="fa-solid fa-boxes-stacked"></i>
        </div>
        <div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Katalog Produk</span>
            <span class="text-2xl font-black text-gray-900 leading-tight"><?= number_format($stat_produk) ?></span>
            <span class="text-[11px] text-amber-600 font-medium block mt-0.5">Item aktif di etalase</span>
        </div>
    </div>

    <!-- Card 4: Total Pelanggan -->
    <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl flex-shrink-0">
            <i class="fa-solid fa-users"></i>
        </div>
        <div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Data Pelanggan</span>
            <span class="text-2xl font-black text-gray-900 leading-tight"><?= number_format($stat_pelanggan) ?></span>
            <span class="text-[11px] text-purple-600 font-medium block mt-0.5">Akun terdaftar</span>
        </div>
    </div>
</div>

<!-- Quick Action Shortcuts -->
<div class="flex flex-wrap gap-3 mb-8">
    <a href="produk.php?aksi=tambah" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-200 transition flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Tambah Produk Baru
    </a>
    <a href="transaksi.php" class="px-5 py-2.5 bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 rounded-xl text-xs font-bold shadow-sm transition flex items-center gap-2">
        <i class="fa-solid fa-file-invoice"></i> Rekap Seluruh Transaksi
    </a>
    <a href="pelanggan.php" class="px-5 py-2.5 bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 rounded-xl text-xs font-bold shadow-sm transition flex items-center gap-2">
        <i class="fa-solid fa-user-gear"></i> Kelola & Reset Password Pelanggan
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    <!-- Tabel 5 Transaksi Terakhir -->
    <div class="lg:col-span-8 bg-white rounded-3xl p-6 border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-emerald-600"></i> Transaksi Terbaru
            </h3>
            <a href="transaksi.php" class="text-xs font-semibold text-emerald-600 hover:underline">
                Lihat Semua &rarr;
            </a>
        </div>

        <?php if (empty($latest_trx)): ?>
            <div class="text-center py-8 text-gray-400 text-xs">Belum ada data transaksi.</div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-gray-400 uppercase tracking-wider font-semibold border-b border-gray-100 pb-2">
                            <th class="py-2.5 px-3">No. Invoice</th>
                            <th class="py-2.5 px-3">Pelanggan</th>
                            <th class="py-2.5 px-3">Tanggal</th>
                            <th class="py-2.5 px-3 text-right">Total</th>
                            <th class="py-2.5 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($latest_trx as $trx): ?>
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="py-3 px-3 font-bold text-gray-900">
                                    #TRX-<?= str_pad($trx['id_transaksi'], 5, '0', STR_PAD_LEFT) ?>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="font-semibold text-gray-800 block"><?= htmlspecialchars($trx['nama_pelanggan']) ?></span>
                                    <span class="text-[10px] text-gray-400"><?= htmlspecialchars($trx['hp']) ?></span>
                                </td>
                                <td class="py-3 px-3 text-gray-600">
                                    <?= date('d M Y', strtotime($trx['tanggal'])) ?>
                                </td>
                                <td class="py-3 px-3 text-right font-bold text-emerald-700">
                                    <?= formatRupiah($trx['total_harga']) ?>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <a href="../invoice.php?id=<?= $trx['id_transaksi'] ?>" target="_blank"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-[11px] font-semibold border border-emerald-200">
                                        <i class="fa-solid fa-print text-[10px]"></i> Cetak Struk
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Peringatan Stok Menipis -->
    <div class="lg:col-span-4 bg-white rounded-3xl p-6 border border-gray-200 shadow-sm">
        <h3 class="font-bold text-gray-900 text-sm pb-4 mb-4 border-b border-gray-100 flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation text-amber-500"></i> Peringatan Stok Menipis
        </h3>

        <?php if (empty($low_stock_items)): ?>
            <div class="text-center py-6 text-gray-400 text-xs">Semua stok produk dalam kondisi aman (&gt; 15 pcs).</div>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($low_stock_items as $low): ?>
                    <div class="p-3 rounded-2xl bg-amber-50/70 border border-amber-200 flex items-center justify-between">
                        <div class="overflow-hidden pr-2">
                            <h4 class="font-bold text-gray-800 text-xs truncate"><?= htmlspecialchars($low['nama']) ?></h4>
                            <span class="text-[11px] text-gray-500"><?= htmlspecialchars($low['kategori']) ?></span>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="px-2.5 py-1 bg-amber-500 text-white font-extrabold text-xs rounded-lg shadow-xs">
                                Sisa <?= $low['stok'] ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="pt-2 text-center">
                    <a href="produk.php" class="text-xs font-bold text-emerald-600 hover:underline">
                        Perbarui Stok di Manajemen Produk &rarr;
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/layout_footer.php'; ?>

