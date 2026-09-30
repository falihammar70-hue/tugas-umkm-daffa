<?php
// profil.php - Profil & Riwayat Transaksi Pelanggan
require_once __DIR__ . '/config/koneksi.php';
/** @var mysqli $koneksi */
global $koneksi;
// Wajib Login
requireLogin('login.php');

$user_id = (int)$_SESSION['user_id'];
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Handle Update Profil Pelanggan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profil'])) {
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $hp = trim($_POST['hp'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $password_baru = $_POST['password_baru'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

    if (empty($nama) || empty($username) || empty($hp) || empty($alamat)) {
        $flash_error = "Nama, username, nomor handphone, dan alamat wajib diisi!";
    } else {
        // Cek keunikan username (selain user saat ini)
        $safe_username = mysqli_real_escape_string($koneksi, $username);
        $cek_username = mysqli_query($koneksi, "SELECT id FROM tb_user WHERE username = '$safe_username' AND id != $user_id LIMIT 1");

        if ($cek_username && mysqli_num_rows($cek_username) > 0) {
            $flash_error = "Username '$username' sudah digunakan oleh pengguna lain.";
        } else {
            $safe_nama = mysqli_real_escape_string($koneksi, $nama);
            $safe_hp = mysqli_real_escape_string($koneksi, $hp);
            $safe_alamat = mysqli_real_escape_string($koneksi, $alamat);

            // Cek apakah password diganti
            if (!empty($password_baru)) {
                if (strlen($password_baru) < 6) {
                    $flash_error = "Kata sandi baru minimal harus 6 karakter!";
                } elseif ($password_baru !== $konfirmasi_password) {
                    $flash_error = "Konfirmasi kata sandi baru tidak cocok!";
                } else {
                    $hash = md5($password_baru);
                    $update_sql = "UPDATE tb_user SET nama='$safe_nama', username='$safe_username', hp='$safe_hp', alamat='$safe_alamat', password='$hash' WHERE id=$user_id";
                }
            } else {
                $update_sql = "UPDATE tb_user SET nama='$safe_nama', username='$safe_username', hp='$safe_hp', alamat='$safe_alamat' WHERE id=$user_id";
            }

            if (!empty($update_sql)) {
                if (mysqli_query($koneksi, $update_sql)) {
                    $_SESSION['nama'] = $nama;
                    $_SESSION['username'] = $username;
                    $flash_success = "Data profil Anda berhasil diperbarui!";
                } else {
                    $flash_error = "Gagal memperbarui profil: " . mysqli_error($koneksi);
                }
            }
        }
    }
}

// Mengambil data user terbaru dari DB
$query_user = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id = $user_id LIMIT 1");
$user_data = mysqli_fetch_assoc($query_user);

// Mengambil riwayat transaksi user
$query_transaksi = "SELECT t.*, 
                           (SELECT COUNT(*) FROM tb_detail WHERE id_transaksi = t.id_transaksi) as total_item
                    FROM tb_transaksi t 
                    WHERE t.id_pelanggan = $user_id 
                    ORDER BY t.id_transaksi DESC";
$res_transaksi = mysqli_query($koneksi, $query_transaksi);
$transaksi_list = [];
if ($res_transaksi) {
    while ($t = mysqli_fetch_assoc($res_transaksi)) {
        $transaksi_list[] = $t;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil & Riwayat Pesanan - UMKM Hebat</title>
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
            <span class="text-gray-800 font-semibold">Akun Saya</span>
        </div>

        <!-- Flash Messages -->
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

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Sidebar Informasi Pelanggan -->
            <div class="lg:col-span-4 bg-white rounded-3xl p-6 border border-gray-200 shadow-sm text-center">
                <div class="w-24 h-24 mx-auto bg-gradient-to-tr from-emerald-600 to-teal-400 rounded-full flex items-center justify-center text-white text-3xl font-black shadow-lg shadow-emerald-200 mb-4">
                    <?= strtoupper(substr($user_data['nama'] ?? 'U', 0, 1)) ?>
                </div>
                <h2 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($user_data['nama']) ?></h2>
                <p class="text-xs text-emerald-600 font-medium">@<?= htmlspecialchars($user_data['username']) ?> &bull; <?= ucfirst($user_data['role']) ?></p>

                <div class="mt-6 pt-6 border-t border-gray-100 text-left space-y-3 text-xs">
                    <div class="flex items-center text-gray-600 gap-3">
                        <i class="fa-solid fa-envelope w-4 text-emerald-600"></i>
                        <span class="truncate"><?= htmlspecialchars($user_data['email']) ?></span>
                    </div>
                    <div class="flex items-center text-gray-600 gap-3">
                        <i class="fa-solid fa-phone w-4 text-emerald-600"></i>
                        <span><?= htmlspecialchars($user_data['hp']) ?></span>
                    </div>
                    <div class="flex items-start text-gray-600 gap-3">
                        <i class="fa-solid fa-location-dot w-4 text-emerald-600 mt-0.5"></i>
                        <span class="leading-relaxed"><?= htmlspecialchars($user_data['alamat']) ?></span>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-100">
                    <div class="bg-emerald-50 rounded-2xl p-4 text-center">
                        <span class="text-xs text-emerald-700 font-medium block">Total Transaksi Selesai</span>
                        <span class="text-2xl font-black text-emerald-800"><?= count($transaksi_list) ?> Pesanan</span>
                    </div>
                </div>
            </div>

            <!-- Konten Utama: Tabs Edit Profil & Riwayat Pesanan -->
            <div class="lg:col-span-8 space-y-8">
                <!-- Form Edit Data Profil -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 pb-3 border-b border-gray-100 flex items-center gap-2">
                        <i class="fa-solid fa-user-pen text-emerald-600"></i> Ubah Data Profil
                    </h3>

                    <form action="profil.php" method="POST" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                                <input type="text" name="nama" required value="<?= htmlspecialchars($user_data['nama']) ?>"
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Username</label>
                                <input type="text" name="username" required value="<?= htmlspecialchars($user_data['username']) ?>"
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Email (Tetap)</label>
                                <input type="email" disabled value="<?= htmlspecialchars($user_data['email']) ?>"
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-100 text-gray-500 text-sm cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nomor Handphone / WA</label>
                                <input type="tel" name="hp" required value="<?= htmlspecialchars($user_data['hp']) ?>"
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Alamat Pengiriman</label>
                            <textarea name="alamat" rows="2" required 
                                      class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"><?= htmlspecialchars($user_data['alamat']) ?></textarea>
                        </div>

                        <!-- Ganti Password Opsional -->
                        <div class="pt-4 border-t border-gray-100">
                            <span class="text-xs font-bold text-gray-700 block mb-2">Ganti Kata Sandi (Kosongkan bila tidak ingin diubah):</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <input type="password" name="password_baru" minlength="6" placeholder="Kata Sandi Baru"
                                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                                </div>
                                <div>
                                    <input type="password" name="konfirmasi_password" minlength="6" placeholder="Konfirmasi Sandi Baru"
                                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" name="update_profil"
                                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-200 transition">
                                <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Perubahan Profil
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Riwayat Transaksi -->
                <div id="riwayat" class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 pb-3 border-b border-gray-100 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-emerald-600"></i> Riwayat Transaksi & Pembelian
                    </h3>

                    <?php if (empty($transaksi_list)): ?>
                        <div class="text-center py-8 text-gray-400">
                            <i class="fa-regular fa-folder-open text-4xl mb-2"></i>
                            <p class="text-sm">Anda belum memiliki riwayat transaksi pembelian.</p>
                            <a href="index.php#katalog" class="mt-3 inline-block text-xs font-bold text-emerald-600 hover:underline">
                                Mulai belanja sekarang &rarr;
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="bg-gray-50 text-gray-600 uppercase tracking-wider font-semibold border-b border-gray-200">
                                        <th class="py-3 px-4">No. Invoice</th>
                                        <th class="py-3 px-4">Tanggal</th>
                                        <th class="py-3 px-4">Total Belanja</th>
                                        <th class="py-3 px-4 text-center">Status</th>
                                        <th class="py-3 px-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php foreach ($transaksi_list as $trx): ?>
                                        <tr class="hover:bg-gray-50/80 transition">
                                            <td class="py-3.5 px-4 font-bold text-gray-900">
                                                #TRX-<?= str_pad($trx['id_transaksi'], 5, '0', STR_PAD_LEFT) ?>
                                            </td>
                                            <td class="py-3.5 px-4 text-gray-600">
                                                <?= date('d M Y', strtotime($trx['tanggal'])) ?>
                                            </td>
                                            <td class="py-3.5 px-4 font-bold text-emerald-700">
                                                <?= formatRupiah($trx['total_harga']) ?>
                                            </td>
                                            <td class="py-3.5 px-4 text-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                                    <i class="fa-solid fa-check-circle mr-1 text-[9px]"></i> Selesai
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-4 text-center">
                                                <a href="invoice.php?id=<?= $trx['id_transaksi'] ?>" 
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold rounded-lg transition border border-emerald-200 shadow-xs" title="Cetak Struk">
                                                    <i class="fa-solid fa-print"></i> Cetak Invoice
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>

