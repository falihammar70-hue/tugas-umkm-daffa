<?php
// admin/pelanggan.php - Manajemen Data Pelanggan & Reset Password
require_once __DIR__ . '/../config/koneksi.php';
/** @var mysqli $koneksi */
global $koneksi;
$page_title = 'Data Pelanggan';
require_once __DIR__ . '/layout_header.php';

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// 1. Handle Reset Password Pelanggan
if (isset($_GET['aksi']) && $_GET['aksi'] === 'reset_password') {
    $id_pelanggan = (int)($_GET['id'] ?? 0);

    if ($id_pelanggan > 0) {
        // Ambil data pelanggan
        $query_pelanggan = mysqli_query($koneksi, "SELECT nama, username FROM tb_user WHERE id = $id_pelanggan AND role = 'pelanggan' LIMIT 1");
        if ($query_pelanggan && $pelanggan = mysqli_fetch_assoc($query_pelanggan)) {
            // Password default baru: 123456
            $default_pass = '123456';
            $hash_baru = md5($default_pass);

            $update_sql = "UPDATE tb_user SET password = '$hash_baru' WHERE id = $id_pelanggan";
            if (mysqli_query($koneksi, $update_sql)) {
                $_SESSION['flash_success'] = "Password untuk pelanggan '{$pelanggan['nama']}' (@{$pelanggan['username']}) berhasil direset menjadi: '$default_pass'.";
            } else {
                $_SESSION['flash_error'] = "Gagal mereset password: " . mysqli_error($koneksi);
            }
        } else {
            $_SESSION['flash_error'] = "Data pelanggan tidak ditemukan atau bukan role pelanggan.";
        }
    }
    header("Location: pelanggan.php");
    exit;
}

// 2. Handle Hapus Akun Pelanggan
if (isset($_GET['aksi']) && $_GET['aksi'] === 'hapus') {
    $id_pelanggan = (int)($_GET['id'] ?? 0);

    if ($id_pelanggan > 0) {
        $del_sql = "DELETE FROM tb_user WHERE id = $id_pelanggan AND role = 'pelanggan'";
        if (mysqli_query($koneksi, $del_sql)) {
            $_SESSION['flash_success'] = "Akun pelanggan berhasil dihapus dari sistem.";
        } else {
            $_SESSION['flash_error'] = "Gagal menghapus pelanggan: " . mysqli_error($koneksi);
        }
    }
    header("Location: pelanggan.php");
    exit;
}

// 3. Pencarian Pelanggan
$cari = trim($_GET['cari'] ?? '');
$where = ["role = 'pelanggan'"];

if (!empty($cari)) {
    $s_cari = mysqli_real_escape_string($koneksi, $cari);
    $where[] = "(nama LIKE '%$s_cari%' OR username LIKE '%$s_cari%' OR email LIKE '%$s_cari%' OR hp LIKE '%$s_cari%')";
}
$where_str = implode(' AND ', $where);

// Ambil daftar pelanggan beserta total belanja & transaksi
$sql = "SELECT u.*, 
               (SELECT COUNT(*) FROM tb_transaksi WHERE id_pelanggan = u.id) as total_trx,
               (SELECT COALESCE(SUM(total_harga), 0) FROM tb_transaksi WHERE id_pelanggan = u.id) as total_belanja
        FROM tb_user u 
        WHERE $where_str 
        ORDER BY u.id DESC";
$result = mysqli_query($koneksi, $sql);
$pelanggan_list = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $pelanggan_list[] = $row;
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
        <h2 class="text-lg font-bold text-gray-900">Daftar Akun Pelanggan</h2>
        <p class="text-xs text-gray-500">Kelola pengguna terdaftar serta opsi reset kata sandi ke bawaan</p>
    </div>

    <!-- Form Search -->
    <form action="pelanggan.php" method="GET" class="flex items-center gap-2">
        <input type="text" name="cari" value="<?= htmlspecialchars($cari) ?>" placeholder="Cari nama, email, hp..."
               class="px-3.5 py-2 border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
        <button type="submit" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold">
            <i class="fa-solid fa-magnifying-glass"></i> Cari
        </button>
        <?php if (!empty($cari)): ?>
            <a href="pelanggan.php" class="p-2 text-gray-400 hover:text-rose-600" title="Reset Pencarian">
                <i class="fa-solid fa-rotate-left"></i>
            </a>
        <?php endif; ?>
    </form>
</div>

<!-- Info Box Reset Password -->
<div class="mb-6 bg-blue-50 border border-blue-200 rounded-2xl p-4 text-xs text-blue-900 flex items-center gap-3">
    <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-sm flex-shrink-0">
        <i class="fa-solid fa-shield-halved"></i>
    </div>
    <div>
        <span class="font-bold">Informasi Fitur Reset Password:</span>
        <p class="text-blue-800 mt-0.5">
            Menekan tombol <strong>'Reset Password'</strong> akan mengembalikan kata sandi pelanggan menjadi password default aman: <code>123456</code>. Pelanggan dapat menggantinya kembali melalui halaman profil.
        </p>
    </div>
</div>

<!-- Tabel Pelanggan -->
<div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
    <?php if (empty($pelanggan_list)): ?>
        <div class="p-12 text-center text-gray-400 text-xs">
            <i class="fa-solid fa-users-slash text-4xl mb-2 text-gray-300"></i>
            <p>Tidak ada data pelanggan yang cocok dengan pencarian.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 uppercase font-bold border-b border-gray-200 tracking-wider">
                        <th class="py-3.5 px-4">Nama Pelanggan</th>
                        <th class="py-3.5 px-4">Kontak (Email / HP)</th>
                        <th class="py-3.5 px-4">Alamat Pengiriman</th>
                        <th class="py-3.5 px-4 text-center">Total Pesanan</th>
                        <th class="py-3.5 px-4 text-right">Total Belanja</th>
                        <th class="py-3.5 px-4 text-center">Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($pelanggan_list as $pel): ?>
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-xs">
                                        <?= strtoupper(substr($pel['nama'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <span class="font-bold text-gray-900 text-sm block"><?= htmlspecialchars($pel['nama']) ?></span>
                                        <span class="text-[11px] text-gray-400 font-mono">@<?= htmlspecialchars($pel['username']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-gray-600">
                                <div><i class="fa-regular fa-envelope text-gray-400 mr-1"></i><?= htmlspecialchars($pel['email']) ?></div>
                                <div class="mt-0.5"><i class="fa-solid fa-phone text-emerald-600 text-[10px] mr-1"></i><?= htmlspecialchars($pel['hp']) ?></div>
                            </td>
                            <td class="py-3.5 px-4 text-gray-500 max-w-xs truncate leading-relaxed">
                                <?= htmlspecialchars($pel['alamat']) ?>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700">
                                    <?= $pel['total_trx'] ?> Transaksi
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-emerald-700">
                                <?= formatRupiah($pel['total_belanja']) ?>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Tombol Reset Password -->
                                    <a href="pelanggan.php?aksi=reset_password&id=<?= $pel['id'] ?>" 
                                       onclick="return confirm('Reset password untuk pelanggan \'<?= htmlspecialchars(addslashes($pel['nama'])) ?>\' menjadi default \'123456\'?');"
                                       class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded-lg text-xs font-semibold transition" title="Reset Password ke 123456">
                                        <i class="fa-solid fa-key mr-1"></i> Reset Sandi
                                    </a>
                                    <!-- Tombol Hapus Pelanggan -->
                                    <a href="pelanggan.php?aksi=hapus&id=<?= $pel['id'] ?>" 
                                       onclick="return confirm('Hapus pelanggan \'<?= htmlspecialchars(addslashes($pel['nama'])) ?>\' beserta seluruh riwayat transaksinya?');"
                                       class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus Akun">
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

