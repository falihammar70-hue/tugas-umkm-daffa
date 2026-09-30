<?php
require_once __DIR__ . '/../config/koneksi.php';
global $koneksi;
$page_title = 'Manajemen Kategori';
requireAdmin('../login.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    $nama = trim($_POST['nama_kategori'] ?? '');
    $id = (int)($_POST['id_kategori'] ?? 0);

    if ($aksi === 'simpan' && $nama !== '') {
        $stmt = mysqli_prepare($koneksi, 'INSERT INTO tb_kategori (nama_kategori) VALUES (?)');
        mysqli_stmt_bind_param($stmt, 's', $nama);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['flash_success'] = 'Kategori berhasil ditambahkan.';
        } else {
            $_SESSION['flash_error'] = mysqli_errno($koneksi) === 1062 ? 'Nama kategori sudah digunakan.' : 'Kategori gagal ditambahkan.';
        }
        mysqli_stmt_close($stmt);
    } elseif ($aksi === 'ubah' && $id > 0 && $nama !== '') {
        $stmt = mysqli_prepare($koneksi, 'UPDATE tb_kategori SET nama_kategori = ? WHERE id_kategori = ?');
        mysqli_stmt_bind_param($stmt, 'si', $nama, $id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['flash_success'] = 'Kategori berhasil diperbarui.';
        } else {
            $_SESSION['flash_error'] = mysqli_errno($koneksi) === 1062 ? 'Nama kategori sudah digunakan.' : 'Kategori gagal diperbarui.';
        }
        mysqli_stmt_close($stmt);
    } elseif ($aksi === 'hapus' && $id > 0) {
        $stmt = mysqli_prepare($koneksi, 'SELECT COUNT(*) FROM tb_produk WHERE id_kategori = ?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $jumlah_produk);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($jumlah_produk > 0) {
            $_SESSION['flash_error'] = 'Kategori masih digunakan oleh produk dan tidak dapat dihapus.';
        } else {
            $stmt = mysqli_prepare($koneksi, 'DELETE FROM tb_kategori WHERE id_kategori = ?');
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $_SESSION['flash_success'] = 'Kategori berhasil dihapus.';
        }
    } else {
        $_SESSION['flash_error'] = 'Nama kategori wajib diisi.';
    }

    header('Location: kategori.php');
    exit;
}

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
$kategori_result = mysqli_query($koneksi, 'SELECT k.id_kategori, k.nama_kategori, COUNT(p.id) AS jumlah_produk FROM tb_kategori k LEFT JOIN tb_produk p ON p.id_kategori = k.id_kategori GROUP BY k.id_kategori ORDER BY k.nama_kategori');
$daftar_kategori = $kategori_result ? mysqli_fetch_all($kategori_result, MYSQLI_ASSOC) : [];
require_once __DIR__ . '/layout_header.php';
?>

<?php if ($flash_success): ?>
    <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800"><?= htmlspecialchars($flash_success) ?></div>
<?php endif; ?>
<?php if ($flash_error): ?>
    <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800"><?= htmlspecialchars($flash_error) ?></div>
<?php endif; ?>

<section class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
    <h2 class="mb-4 text-base font-bold text-gray-900">Tambah Kategori</h2>
    <form method="post" class="flex flex-col gap-3 sm:flex-row">
        <input type="hidden" name="aksi" value="simpan">
        <input type="text" name="nama_kategori" maxlength="255" required placeholder="Nama kategori"
               class="min-w-0 flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
        <button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700" type="submit">
            <i class="fa-solid fa-plus mr-1"></i> Tambah
        </button>
    </form>
</section>

<section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr><th class="px-5 py-3">Nama Kategori</th><th class="px-5 py-3">Jumlah Produk</th><th class="px-5 py-3 text-right">Aksi</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($daftar_kategori as $kategori): ?>
                    <tr>
                        <td class="px-5 py-3">
                            <form method="post" class="flex gap-2">
                                <input type="hidden" name="aksi" value="ubah">
                                <input type="hidden" name="id_kategori" value="<?= (int)$kategori['id_kategori'] ?>">
                                <input name="nama_kategori" maxlength="255" required value="<?= htmlspecialchars($kategori['nama_kategori']) ?>"
                                       class="min-w-0 flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                <button class="rounded-lg border border-gray-300 px-3 text-gray-600 hover:bg-gray-50" title="Simpan nama kategori" aria-label="Simpan nama kategori">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-3 text-gray-600"><?= (int)$kategori['jumlah_produk'] ?> produk</td>
                        <td class="px-5 py-3 text-right">
                            <form method="post" class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                                <input type="hidden" name="aksi" value="hapus">
                                <input type="hidden" name="id_kategori" value="<?= (int)$kategori['id_kategori'] ?>">
                                <button class="rounded-lg px-3 py-2 text-rose-600 hover:bg-rose-50" title="Hapus kategori" aria-label="Hapus kategori">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$daftar_kategori): ?>
                    <tr><td colspan="3" class="px-5 py-8 text-center text-gray-500">Belum ada kategori.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/layout_footer.php'; ?>