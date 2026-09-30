<?php
// admin/produk.php - CRUD Manajemen Produk
require_once __DIR__ . '/../config/koneksi.php';
/** @var mysqli $koneksi */
global $koneksi;
$page_title = 'Manajemen Produk';
requireAdmin('../login.php');

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$target_dir = __DIR__ . '/../assets/images/';
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// 1. Handle Tambah Produk Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_produk'])) {
    $nama = trim($_POST['nama'] ?? '');
    $harga = (int)($_POST['harga'] ?? 0);
    $stok = (int)($_POST['stok'] ?? 0);
    $id_kategori = (int)($_POST['id_kategori'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $nama_foto = null;

    if (empty($nama) || $harga <= 0 || $id_kategori <= 0) {
        $_SESSION['flash_error'] = "Nama produk, harga, dan kategori wajib diisi!";
    } else {
        // Upload Foto jika ada
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['foto']['tmp_name'];
            $file_name = $_FILES['foto']['name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($file_ext, $allowed_ext)) {
                $_SESSION['flash_error'] = "Format foto tidak valid! Gunakan format JPG, JPEG, PNG, atau WEBP.";
                header("Location: produk.php");
                exit;
            }

            $nama_foto = 'prod_' . time() . '_' . rand(100, 999) . '.' . $file_ext;
            move_uploaded_file($file_tmp, $target_dir . $nama_foto);
        }

        $stmt = mysqli_prepare($koneksi, 'INSERT INTO tb_produk (nama, harga, stok, poto, id_kategori, deskripsi) VALUES (?, ?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'siisis', $nama, $harga, $stok, $nama_foto, $id_kategori, $deskripsi);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['flash_success'] = "Produk '$nama' berhasil ditambahkan ke katalog!";
        } else {
            $_SESSION['flash_error'] = "Gagal menambah produk: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    }
    header("Location: produk.php");
    exit;
}

// 2. Handle Edit Produk
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_produk'])) {
    $id = (int)($_POST['id'] ?? 0);
    $nama = trim($_POST['nama'] ?? '');
    $harga = (int)($_POST['harga'] ?? 0);
    $stok = (int)($_POST['stok'] ?? 0);
    $id_kategori = (int)($_POST['id_kategori'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $foto_lama = $_POST['foto_lama'] ?? '';

    if ($id <= 0 || empty($nama) || $harga <= 0 || $id_kategori <= 0) {
        $_SESSION['flash_error'] = "Data perubahan produk tidak valid!";
    } else {
        $nama_foto_update = $foto_lama;

        // Cek jika ada unggahan foto baru
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['foto']['tmp_name'];
            $file_name = $_FILES['foto']['name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($file_ext, $allowed_ext)) {
                $nama_foto_update = 'prod_' . time() . '_' . rand(100, 999) . '.' . $file_ext;
                if (move_uploaded_file($file_tmp, $target_dir . $nama_foto_update)) {
                    // Hapus foto lama jika ada dan bukan dummy bawaan
                    if (!empty($foto_lama) && file_exists($target_dir . $foto_lama) && str_starts_with($foto_lama, 'prod_')) {
                        unlink($target_dir . $foto_lama);
                    }
                }
            }
        }

        $stmt = mysqli_prepare($koneksi, 'UPDATE tb_produk SET nama = ?, harga = ?, stok = ?, poto = ?, id_kategori = ?, deskripsi = ? WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'siisisi', $nama, $harga, $stok, $nama_foto_update, $id_kategori, $deskripsi, $id);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['flash_success'] = "Data produk '$nama' berhasil diperbarui!";
        } else {
            $_SESSION['flash_error'] = "Gagal memperbarui produk: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    }
    header("Location: produk.php");
    exit;
}

// 3. Handle Hapus Produk
if (isset($_GET['aksi']) && $_GET['aksi'] === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        $get_prod = mysqli_query($koneksi, "SELECT poto, nama FROM tb_produk WHERE id=$id LIMIT 1");
        if ($get_prod && $p = mysqli_fetch_assoc($get_prod)) {
            if (!empty($p['poto']) && file_exists($target_dir . $p['poto']) && str_starts_with($p['poto'], 'prod_')) {
                unlink($target_dir . $p['poto']);
            }
            mysqli_query($koneksi, "DELETE FROM tb_produk WHERE id=$id");
            $_SESSION['flash_success'] = "Produk '{$p['nama']}' berhasil dihapus dari katalog!";
        }
    }
    header("Location: produk.php");
    exit;
}

require_once __DIR__ . '/layout_header.php';

// 4. Filter & Search
$cari = trim($_GET['cari'] ?? '');
$kategori_filter = (int)($_GET['id_kategori'] ?? 0);

$where = ["1=1"];
if (!empty($cari)) {
    $s_cari = mysqli_real_escape_string($koneksi, $cari);
    $where[] = "(p.nama LIKE '%$s_cari%' OR p.deskripsi LIKE '%$s_cari%')";
}
if ($kategori_filter > 0) {
    $where[] = "p.id_kategori = $kategori_filter";
}
$where_str = implode(' AND ', $where);

$query_all = "SELECT p.*, k.nama_kategori AS kategori FROM tb_produk p JOIN tb_kategori k ON k.id_kategori = p.id_kategori WHERE $where_str ORDER BY p.id DESC";
$res_all = mysqli_query($koneksi, $query_all);
$daftar_produk = [];
if ($res_all) {
    while ($row = mysqli_fetch_assoc($res_all)) {
        $daftar_produk[] = $row;
    }
}

// Daftar kategori untuk filter dan formulir produk
$daftar_kat = [];
$res_kat = mysqli_query($koneksi, "SELECT id_kategori, nama_kategori FROM tb_kategori ORDER BY nama_kategori ASC");
if ($res_kat) {
    while ($rk = mysqli_fetch_assoc($res_kat)) {
        $daftar_kat[] = $rk;
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

<!-- Top Action & Filter Bar -->
<div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-lg font-bold text-gray-900">Katalog Produk UMKM</h2>
        <p class="text-xs text-gray-500">Kelola informasi produk, stok barang, harga, dan foto produk</p>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <!-- Form Search -->
        <form action="produk.php" method="GET" class="flex items-center gap-2">
            <input type="search" name="cari" value="<?= htmlspecialchars($cari) ?>" placeholder="Cari nama produk..."
                   class="px-3.5 py-2 border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <select name="id_kategori" onchange="this.form.requestSubmit()" class="px-3.5 py-2 border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                <option value="">Semua Kategori</option>
                <?php foreach ($daftar_kat as $k): ?>
                    <option value="<?= (int)$k['id_kategori'] ?>" <?= $kategori_filter === (int)$k['id_kategori'] ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kategori']) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($cari) || $kategori_filter > 0): ?>
                <a href="produk.php" class="p-2 text-gray-400 hover:text-rose-600" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            <?php endif; ?>
        </form>

        <button onclick="openAddModal()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-200 transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Produk
        </button>
    </div>
</div>

<!-- Tabel Produk -->
<div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
    <?php if (empty($daftar_produk)): ?>
        <div class="p-12 text-center text-gray-400 text-xs">
            <i class="fa-solid fa-box-open text-4xl mb-2 text-gray-300"></i>
            <p>Tidak ada data produk yang ditemukan.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 uppercase font-bold border-b border-gray-200 tracking-wider">
                        <th class="py-3 px-4">Foto</th>
                        <th class="py-3 px-4">Nama Produk</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-right">Harga</th>
                        <th class="py-3 px-4 text-center">Stok</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($daftar_produk as $prod): ?>
                        <?php
                            $foto_produk = trim((string)($prod['poto'] ?? ''));
                            $img_src = preg_match('/^https?:\/\//i', $foto_produk)
                                ? $foto_produk
                                : (!empty($foto_produk) && file_exists($target_dir . $foto_produk)
                                    ? '../assets/images/' . $foto_produk
                                    : 'https://placehold.co/120x120/059669/ffffff?text=' . urlencode($prod['nama']));
                        ?>
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="py-3 px-4">
                                <img src="<?= htmlspecialchars($img_src) ?>" alt="Foto" class="w-12 h-12 rounded-xl object-cover border border-gray-200 shadow-xs">
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-gray-900 text-sm"><?= htmlspecialchars($prod['nama']) ?></div>
                                <div class="text-[11px] text-gray-400 max-w-xs truncate"><?= htmlspecialchars($prod['deskripsi'] ?? '-') ?></div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 bg-gray-100 text-gray-700 font-semibold rounded-lg text-[11px]">
                                    <?= htmlspecialchars($prod['kategori']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-emerald-700">
                                <?= formatRupiah($prod['harga']) ?>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <?php if ($prod['stok'] <= 5): ?>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        <?= $prod['stok'] ?> (Kritis)
                                    </span>
                                <?php else: ?>
                                    <span class="font-bold text-gray-800"><?= $prod['stok'] ?> pcs</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <button onclick='openEditModal(<?= json_encode($prod) ?>, "<?= htmlspecialchars($img_src) ?>")'
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit Produk">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <a href="produk.php?aksi=hapus&id=<?= $prod['id'] ?>" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus produk \'<?= htmlspecialchars(addslashes($prod['nama'])) ?>\'?');"
                                       class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus Produk">
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

<!-- Modal Tambah Produk -->
<div id="modalTambah" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-gray-100">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
            <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-emerald-600"></i> Tambah Produk Baru
            </h3>
            <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <form action="produk.php" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            <div>
                <label class="block font-bold text-gray-700 uppercase mb-1">Nama Produk</label>
                <input type="text" name="nama" required placeholder="Contoh: Kripik Pisang Madu 200g"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Harga (Rp)</label>
                    <input type="number" name="harga" required min="100" placeholder="25000"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Stok Awal</label>
                    <input type="number" name="stok" required min="0" value="10"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Kategori</label>
                    <select name="id_kategori" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="">Pilih kategori</option>
                        <?php foreach ($daftar_kat as $k): ?>
                            <option value="<?= (int)$k['id_kategori'] ?>"><?= htmlspecialchars($k['nama_kategori']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Upload Foto</label>
                    <input type="file" name="foto" accept="image/*" onchange="previewFoto(this, 'previewTambah')"
                           class="w-full py-1.5 text-gray-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>
            </div>

            <div id="previewTambahWrap" class="hidden">
                <label class="block font-bold text-gray-700 uppercase mb-1">Preview Foto</label>
                <img id="previewTambah" src="" alt="Preview" class="w-24 h-24 object-cover rounded-xl border border-gray-200">
            </div>

            <div>
                <label class="block font-bold text-gray-700 uppercase mb-1">Deskripsi Produk</label>
                <textarea name="deskripsi" rows="3" placeholder="Jelaskan keunggulan produk UMKM ini..."
                          class="w-full px-3.5 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200">Batal</button>
                <button type="submit" name="tambah_produk" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-md shadow-emerald-200">Simpan Produk</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Produk -->
<div id="modalEdit" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-gray-100">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
            <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-blue-600"></i> Edit Data Produk
            </h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <form action="produk.php" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            <input type="hidden" name="id" id="editId">
            <input type="hidden" name="foto_lama" id="editFotoLama">

            <div>
                <label class="block font-bold text-gray-700 uppercase mb-1">Nama Produk</label>
                <input type="text" name="nama" id="editNama" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Harga (Rp)</label>
                    <input type="number" name="harga" id="editHarga" required min="100"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Stok Barang</label>
                    <input type="number" name="stok" id="editStok" required min="0"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Kategori</label>
                    <select name="id_kategori" id="editKategori" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <?php foreach ($daftar_kat as $k): ?>
                            <option value="<?= (int)$k['id_kategori'] ?>"><?= htmlspecialchars($k['nama_kategori']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Ganti Foto (Opsional)</label>
                    <input type="file" name="foto" accept="image/*" onchange="previewFoto(this, 'previewEdit')"
                           class="w-full py-1.5 text-gray-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
            </div>

            <div id="previewEditWrap" class="hidden">
                <label class="block font-bold text-gray-700 uppercase mb-1">Preview Foto</label>
                <img id="previewEdit" src="" alt="Preview" class="w-24 h-24 object-cover rounded-xl border border-gray-200">
            </div>

            <div>
                <label class="block font-bold text-gray-700 uppercase mb-1">Deskripsi Produk</label>
                <textarea name="deskripsi" id="editDeskripsi" rows="3"
                          class="w-full px-3.5 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200">Batal</button>
                <button type="submit" name="edit_produk" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-md shadow-blue-200">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('modalTambah').classList.remove('hidden');
}
function closeAddModal() {
    document.getElementById('modalTambah').classList.add('hidden');
    document.getElementById('previewTambahWrap').classList.add('hidden');
}

function openEditModal(prod, imgSrc) {
    document.getElementById('editId').value = prod.id;
    document.getElementById('editFotoLama').value = prod.poto || '';
    document.getElementById('editNama').value = prod.nama;
    document.getElementById('editHarga').value = prod.harga;
    document.getElementById('editStok').value = prod.stok;
    document.getElementById('editKategori').value = prod.id_kategori;
    document.getElementById('editDeskripsi').value = prod.deskripsi || '';

    var previewImg = document.getElementById('previewEdit');
    var previewWrap = document.getElementById('previewEditWrap');
    if (imgSrc) {
        previewImg.src = imgSrc;
        previewWrap.classList.remove('hidden');
    } else {
        previewWrap.classList.add('hidden');
    }

    document.getElementById('modalEdit').classList.remove('hidden');
}
function closeEditModal() {
    document.getElementById('modalEdit').classList.add('hidden');
}

function previewFoto(input, targetId) {
    var wrap = document.getElementById(targetId + 'Wrap');
    var img = document.getElementById(targetId);
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            img.src = e.target.result;
            wrap.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once __DIR__ . '/layout_footer.php'; ?>