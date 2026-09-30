<?php
// admin/transaksi.php - Manajemen Transaksi Pelanggan
require_once __DIR__ . '/../config/koneksi.php';
/** @var mysqli $koneksi */
global $koneksi;
$page_title = 'Manajemen Transaksi';
require_once __DIR__ . '/layout_header.php';

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// 1. Handle Hapus Transaksi
if (isset($_GET['aksi']) && $_GET['aksi'] === 'hapus') {
    $id_transaksi = (int)($_GET['id'] ?? 0);

    if ($id_transaksi > 0) {
        $del_sql = "DELETE FROM tb_transaksi WHERE id_transaksi = $id_transaksi";
        if (mysqli_query($koneksi, $del_sql)) {
            $_SESSION['flash_success'] = "Data transaksi #TRX-" . str_pad($id_transaksi, 5, '0', STR_PAD_LEFT) . " berhasil dihapus!";
        } else {
            $_SESSION['flash_error'] = "Gagal menghapus transaksi: " . mysqli_error($koneksi);
        }
    }
    header("Location: transaksi.php");
    exit;
}

// 2. Filter & Pencarian
$cari = trim($_GET['cari'] ?? '');
$where = ["1=1"];

if (!empty($cari)) {
    $s_cari = mysqli_real_escape_string($koneksi, $cari);
    $where[] = "(u.nama LIKE '%$s_cari%' OR u.username LIKE '%$s_cari%' OR t.id_transaksi = '$s_cari')";
}
$where_str = implode(' AND ', $where);

// Ambil Transaksi Pelanggan
$sql = "SELECT t.*, u.nama as nama_pelanggan, u.email, u.hp, u.alamat,
               (SELECT COUNT(*) FROM tb_detail WHERE id_transaksi = t.id_transaksi) as total_item
        FROM tb_transaksi t 
        JOIN tb_user u ON t.id_pelanggan = u.id 
        WHERE $where_str
        ORDER BY t.id_transaksi DESC";
$result = mysqli_query($koneksi, $sql);
$transaksi_list = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $transaksi_list[] = $row;
    }
}
?>

<!-- Alert Feedback -->
<?php if ($flash_success): ?>
    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center gap-3 shadow-sm">
        <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
        <span><?= htmlspecialchars($flash_success) ?></span>
    </div>
<?php endif; ?>

<?php if ($flash_error): ?>
    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-medium flex items-center gap-3 shadow-sm">
        <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg"></i>
        <span><?= htmlspecialchars($flash_error) ?></span>
    </div>
<?php endif; ?>

<!-- Top Action & Search Bar -->
<div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h2 class="text-lg font-bold text-gray-900">Rekap Transaksi Penjualan</h2>
        <p class="text-xs text-gray-500">Seluruh pesanan pelanggan yang masuk ke sistem UMKM Hebat</p>
    </div>

    <!-- Form Search -->
    <form action="transaksi.php" method="GET" class="flex items-center gap-2">
        <input type="text" name="cari" value="<?= htmlspecialchars($cari) ?>" placeholder="Cari ID transaksi atau nama..."
               class="px-3.5 py-2 border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
        <button type="submit" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold">
            <i class="fa-solid fa-magnifying-glass"></i> Cari
        </button>
        <?php if (!empty($cari)): ?>
            <a href="transaksi.php" class="p-2 text-gray-400 hover:text-rose-600" title="Reset Pencarian">
                <i class="fa-solid fa-rotate-left"></i>
            </a>
        <?php endif; ?>
    </form>
</div>

<!-- Tabel Transaksi -->
<div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
    <?php if (empty($transaksi_list)): ?>
        <div class="p-12 text-center text-gray-400 text-xs">
            <i class="fa-solid fa-receipt text-4xl mb-2 text-gray-300"></i>
            <p>Tidak ada transaksi yang ditemukan.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 uppercase font-bold border-b border-gray-200 tracking-wider">
                        <th class="py-3.5 px-4">No. Invoice</th>
                        <th class="py-3.5 px-4">Data Pelanggan</th>
                        <th class="py-3.5 px-4">Tanggal Transaksi</th>
                        <th class="py-3.5 px-4 text-right">Total Pembayaran</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($transaksi_list as $trx): ?>
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="py-3.5 px-4 font-black text-gray-900">
                                #TRX-<?= str_pad($trx['id_transaksi'], 5, '0', STR_PAD_LEFT) ?>
                                <span class="block text-[10px] text-gray-400 font-normal"><?= $trx['total_item'] ?> produk dipesan</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-gray-900 text-sm block"><?= htmlspecialchars($trx['nama_pelanggan']) ?></span>
                                <span class="text-[11px] text-gray-500 block"><i class="fa-solid fa-phone text-[9px] text-emerald-600 mr-1"></i><?= htmlspecialchars($trx['hp']) ?></span>
                                <span class="text-[11px] text-gray-400 block truncate max-w-xs"><i class="fa-solid fa-location-dot text-[9px] text-emerald-600 mr-1"></i><?= htmlspecialchars($trx['alamat']) ?></span>
                            </td>
                            <td class="py-3.5 px-4 text-gray-600">
                                <div class="font-semibold text-gray-800"><?= date('d M Y', strtotime($trx['tanggal'])) ?></div>
                            </td>
                            <td class="py-3.5 px-4 text-right font-extrabold text-emerald-700 text-sm">
                                <?= formatRupiah($trx['total_harga']) ?>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    <i class="fa-solid fa-check-circle mr-1"></i> Lunas
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="../invoice.php?id=<?= $trx['id_transaksi'] ?>" target="_blank"
                                       class="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-semibold transition" title="Lihat & Cetak Invoice">
                                        <i class="fa-solid fa-print mr-1"></i> Cetak Invoice
                                    </a>
                                    <a href="transaksi.php?aksi=hapus&id=<?= $trx['id_transaksi'] ?>" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi #TRX-<?= str_pad($trx['id_transaksi'], 5, '0', STR_PAD_LEFT) ?>? Tindakan ini tidak dapat dibatalkan.');"
                                       class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus Transaksi">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/layout_footer.php'; ?>

