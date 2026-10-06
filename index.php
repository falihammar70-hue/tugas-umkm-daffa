<?php
// index.php - Landing Page & Katalog Produk Kebaya Ayu Pengantin
require_once __DIR__ . '/config/koneksi.php';
/** @var mysqli $koneksi */
global $koneksi;
// Flash message handler
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Handle Penambahan ke Keranjang via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_keranjang'])) {
    $id_produk = (int)($_POST['id_produk'] ?? 0);
    $jumlah = max(1, (int)($_POST['jumlah'] ?? 1));

    // Cek ketersediaan produk di database
    $query = "SELECT p.*, k.nama_kategori AS kategori FROM tb_produk p JOIN tb_kategori k ON k.id_kategori = p.id_kategori WHERE p.id = $id_produk";
    $result = mysqli_query($koneksi, $query);

    if ($result && $produk = mysqli_fetch_assoc($result)) {
        $stok_tersedia = (int)$produk['stok'];
        $jumlah_saat_ini = $_SESSION['keranjang'][$id_produk] ?? 0;

        if (($jumlah_saat_ini + $jumlah) > $stok_tersedia) {
            $_SESSION['flash_error'] = "Mohon maaf, stok produk '{$produk['nama']}' hanya tersisa {$stok_tersedia}.";
        } else {
            if (!isset($_SESSION['keranjang'])) {
                $_SESSION['keranjang'] = [];
            }
            $_SESSION['keranjang'][$id_produk] = $jumlah_saat_ini + $jumlah;
            $_SESSION['flash_success'] = "Berhasil menambahkan '{$produk['nama']}' ke keranjang!";
        }
    } else {
        $_SESSION['flash_error'] = "Produk tidak ditemukan!";
    }

    header("Location: index.php#katalog");
    exit;
}

// Filter Kategori & Pencarian
$kategori_dipilih = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
$kata_kunci = isset($_GET['cari']) ? trim($_GET['cari']) : '';

// Mengambil daftar kategori yang dikelola admin
$kategori_query = "SELECT nama_kategori AS kategori FROM tb_kategori ORDER BY nama_kategori ASC";
$kategori_res = mysqli_query($koneksi, $kategori_query);
$daftar_kategori = [];
if ($kategori_res) {
    while ($row = mysqli_fetch_assoc($kategori_res)) {
        $daftar_kategori[] = $row['kategori'];
    }
}

// Query Produk dengan filter
$where_clauses = ["1=1"];
if (!empty($kategori_dipilih)) {
    $kat_safe = mysqli_real_escape_string($koneksi, $kategori_dipilih);
    $where_clauses[] = "k.nama_kategori = '$kat_safe'";
}
if (!empty($kata_kunci)) {
    $keyword_safe = mysqli_real_escape_string($koneksi, $kata_kunci);
    $where_clauses[] = "(nama LIKE '%$keyword_safe%' OR deskripsi LIKE '%$keyword_safe%')";
}

$where_sql = implode(" AND ", $where_clauses);
$sql_produk = "SELECT p.*, k.nama_kategori AS kategori FROM tb_produk p JOIN tb_kategori k ON k.id_kategori = p.id_kategori WHERE $where_sql ORDER BY p.id DESC";
$res_produk = mysqli_query($koneksi, $sql_produk);
$produk_list = [];
if ($res_produk) {
    while ($p = mysqli_fetch_assoc($res_produk)) {
        $produk_list[] = $p;
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebaya Ayu Pengantin - Sewa, Beli & Rias Kebaya Pengantin</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#7A1F3D',
                        secondary: '#4A0E24'
                    }
                }
            }
        }
    </script>
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen flex flex-col">

    <!-- Navbar -->
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <!-- Flash Notifications -->
    <div class="fixed top-4 right-4 z-[100] w-[calc(100%-2rem)] max-w-md">
        <?php if ($flash_success): ?>
            <div class="flash-notification flex items-center p-4 mb-4 text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 shadow-xl" role="status" aria-live="polite">
                <i class="fa-solid fa-circle-check text-xl mr-3 text-emerald-700"></i>
                <div class="text-sm font-medium flex-1"><?= htmlspecialchars(is_array($flash_success) ? implode(' ', $flash_success) : $flash_success, ENT_QUOTES, 'UTF-8') ?></div>
                <button type="button" aria-label="Tutup notifikasi" onclick="this.parentElement.remove()" class="ml-3 text-lg leading-none">&times;</button>
            </div>
        <?php endif; ?>

        <?php if ($flash_error): ?>
            <div class="flash-notification flex items-center p-4 mb-4 text-rose-800 rounded-xl bg-rose-50 border border-rose-200 shadow-xl" role="alert" aria-live="polite">
                <i class="fa-solid fa-triangle-exclamation text-xl mr-3 text-rose-600"></i>
                <div class="text-sm font-medium flex-1"><?= htmlspecialchars($flash_error, ENT_QUOTES, 'UTF-8') ?></div>
                <button type="button" aria-label="Tutup notifikasi" onclick="this.parentElement.remove()" class="ml-3 text-lg leading-none">&times;</button>
            </div>
        <?php endif; ?>
    </div>
    <script>
        document.querySelectorAll('.flash-notification').forEach((notice) => {
            setTimeout(() => notice.remove(), 8000);
        });
    </script>

    <!-- Hero Banner -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="relative overflow-hidden rounded-3xl shadow-xl bg-gradient-to-r from-rose-950 via-fuchsia-950 to-rose-900 text-white min-h-[380px] sm:min-h-[420px] flex items-center">
            <img src="https://images.unsplash.com/photo-1537633552985-df8429e8048b?auto=format&fit=crop&w=2200&q=85"
                 alt="Gaun pengantin untuk hari bahagia" class="absolute inset-0 h-full w-full object-cover object-center">
            <div class="absolute inset-0 bg-gradient-to-r from-rose-950/95 via-fuchsia-950/80 to-rose-900/35"></div>
            <!-- Background Decorative circles -->
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute top-10 left-1/3 w-60 h-60 bg-amber-300/10 rounded-full blur-xl pointer-events-none"></div>

            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center p-8 sm:p-12 w-full">
                <div class="lg:col-span-7 space-y-5">
                    <div class="inline-flex items-center gap-2 bg-rose-500/30 border border-rose-300/30 px-3.5 py-1.5 rounded-full text-xs font-semibold tracking-wide text-rose-100">
                        <i class="fa-solid fa-certificate text-amber-400"></i> Kebaya Custom & Rias Pengantin Profesional
                    </div>
                    <h1 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight">
                        Tampil Anggun di Hari Bahagia dengan <span class="text-rose-300 underline decoration-amber-400 underline-offset-8">Kebaya Pengantin</span>
                    </h1>
                    <p class="text-rose-100 text-base sm:text-lg max-w-xl font-normal leading-relaxed">
                        Sewa maupun beli kebaya pengantin dari kain premium, lengkap dengan jasa rias profesional untuk momen spesial Anda.
                    </p>
                    <div class="flex flex-wrap gap-3 pt-2">
                        <a href="#katalog" class="px-6 py-3.5 bg-amber-400 hover:bg-amber-300 text-gray-950 font-bold text-sm rounded-xl shadow-lg shadow-amber-500/30 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                            <i class="fa-solid fa-bag-shopping"></i> Lihat Koleksi Kebaya
                        </a>
                        <a href="#keunggulan" class="px-6 py-3.5 bg-white/10 hover:bg-white/20 text-white font-semibold text-sm rounded-xl backdrop-blur-sm border border-white/20 transition">
                            Pelajari Lebih Lanjut
                        </a>
                    </div>
                </div>

                <!-- Banner Feature Highlight Box -->
                <div class="lg:col-span-5 hidden lg:block">
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 p-6 rounded-2xl space-y-4 shadow-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-amber-400 text-gray-900 flex items-center justify-center font-bold text-xl shadow-md">
                                <i class="fa-solid fa-gem"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-white text-base">Kain & Bahan Premium</h3>
                                <p class="text-xs text-rose-100">Brokat, tile, dan sutra pilihan dengan jahitan rapi</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-rose-700 text-white flex items-center justify-center font-bold text-xl shadow-md">
                                <i class="fa-solid fa-truck-fast"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-white text-base">Sewa & Pengiriman Aman</h3>
                                <p class="text-xs text-rose-100">Dikemas rapi, siap kirim ke lokasi acara Anda</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-fuchsia-700 text-white flex items-center justify-center font-bold text-xl shadow-md">
                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-white text-base">Konsultasi & Fitting Personal</h3>
                                <p class="text-xs text-rose-100">Tim kami bantu pilih desain & warna yang pas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Keunggulan Section -->
    <section id="keunggulan" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-fuchsia-50 text-fuchsia-700 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-gem"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 text-sm mb-1">Kain Premium Pilihan</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">Kebaya dari bahan brokat, tile, dan sutra berkualitas tinggi.</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 text-sm mb-1">Harga Bersahabat</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">Paket sewa maupun beli kebaya dengan harga transparan.</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-ruler-combined"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 text-sm mb-1">Custom Desain & Ukuran</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">Kebaya bisa disesuaikan ukuran dan desain sesuai permintaan.</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 text-sm mb-1">Rias Pengantin Profesional</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">Tim MUA berpengalaman siap merias di hari spesial Anda.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Katalog Produk Section -->
    <section id="katalog" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-14 flex-1">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
            <div>
                <span class="text-xs font-bold text-rose-700 uppercase tracking-widest bg-rose-50 px-3 py-1 rounded-full border border-rose-200">
                    Katalog Unggulan
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-2">Pilihan Kebaya & Paket Rias Terbaik</h2>
                <p class="text-sm text-gray-500 mt-1">Temukan kebaya dan paket rias yang paling cocok untuk hari bahagia Anda.</p>
            </div>

            <!-- Form Pencarian -->
            <form action="index.php" method="GET" class="flex items-center gap-2 max-w-md w-full">
                <?php if (!empty($kategori_dipilih)): ?>
                    <input type="hidden" name="kategori" value="<?= htmlspecialchars($kategori_dipilih) ?>">
                <?php endif; ?>
                <div class="relative w-full">
                    <input type="text" name="cari" value="<?= htmlspecialchars($kata_kunci) ?>" 
                           placeholder="Cari kebaya, paket rias, atau aksesoris..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-rose-600 focus:border-rose-600 text-sm shadow-sm">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-gray-400 text-sm"></i>
                </div>
                <button type="submit" class="px-4 py-2.5 bg-rose-800 hover:bg-rose-900 text-white rounded-xl text-sm font-medium transition shadow-sm">
                    Cari
                </button>
                <?php if (!empty($kata_kunci) || !empty($kategori_dipilih)): ?>
                    <a href="index.php#katalog" class="p-2.5 text-gray-500 hover:text-rose-700 bg-white border border-gray-300 rounded-xl text-sm transition" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Filter Tab Kategori -->
        <div class="flex items-center gap-2 overflow-x-auto pb-4 scrollbar-none mb-6">
            <a href="index.php<?= !empty($kata_kunci) ? '?cari=' . urlencode($kata_kunci) : '' ?>#katalog" 
               class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition <?= empty($kategori_dipilih) ? 'bg-rose-800 text-white shadow-md shadow-rose-200' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' ?>">
                <i class="fa-solid fa-border-all mr-1.5"></i> Semua Kategori
            </a>
            <?php foreach ($daftar_kategori as $kat): ?>
                <a href="index.php?kategori=<?= urlencode($kat) ?><?= !empty($kata_kunci) ? '&cari=' . urlencode($kata_kunci) : '' ?>#katalog" 
                   class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition <?= $kategori_dipilih === $kat ? 'bg-rose-800 text-white shadow-md shadow-rose-200' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' ?>">
                    <?= htmlspecialchars($kat) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Grid Produk -->
        <?php if (empty($produk_list)): ?>
            <div class="bg-white rounded-3xl p-12 text-center border border-gray-200 shadow-sm max-w-lg mx-auto my-8">
                <div class="w-20 h-20 mx-auto bg-amber-50 rounded-full flex items-center justify-center text-amber-500 text-3xl mb-4">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Produk Tidak Ditemukan</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Tidak ada kebaya atau paket rias yang cocok dengan pencarian atau filter kategori yang dipilih.
                </p>
                <a href="index.php#katalog" class="inline-flex items-center gap-2 px-5 py-2.5 bg-rose-800 text-white text-sm font-semibold rounded-xl hover:bg-rose-900 transition">
                    <i class="fa-solid fa-arrows-rotate"></i> Reset Semua Filter
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php foreach ($produk_list as $item): ?>
                    <?php
                        $foto_produk = trim((string)($item['poto'] ?? ''));
                        $foto_path = preg_match('/^https?:\/\//i', $foto_produk)
                            ? $foto_produk
                            : (!empty($foto_produk) && file_exists(__DIR__ . '/assets/images/' . $foto_produk)
                                ? 'assets/images/' . $foto_produk
                                : 'https://placehold.co/600x450/7A1F3D/ffffff?text=' . urlencode($item['nama']));
                        $is_habis = (int)$item['stok'] <= 0;
                    ?>
                    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden group">
                        <!-- Foto & Tag Kategori -->
                        <div class="relative overflow-hidden bg-gray-100 aspect-video sm:aspect-square">
                            <img src="<?= htmlspecialchars($foto_path) ?>" alt="<?= htmlspecialchars($item['nama']) ?>" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            
                            <!-- Badge Kategori -->
                            <span class="absolute top-3 left-3 bg-black/60 backdrop-blur-md text-white text-[11px] font-medium px-2.5 py-1 rounded-lg">
                                <?= htmlspecialchars($item['kategori']) ?>
                            </span>

                            <!-- Stok Status -->
                            <?php if ($is_habis): ?>
                                <div class="absolute inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center">
                                    <span class="bg-rose-700 text-white text-xs font-bold px-3 py-1.5 rounded-xl uppercase tracking-wider shadow">
                                        Stok Habis
                                    </span>
                                </div>
                            <?php elseif ((int)$item['stok'] <= 5): ?>
                                <span class="absolute top-3 right-3 bg-amber-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-md shadow">
                                    Sisa <?= $item['stok'] ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 flex flex-col flex-1">
                            <div class="text-xs text-gray-400 font-medium mb-1 flex items-center justify-between">
                                <span>Kategori: <?= htmlspecialchars($item['kategori']) ?></span>
                                <span>Stok: <?= (int)$item['stok'] ?></span>
                            </div>

                            <h3 class="font-bold text-gray-900 text-base group-hover:text-rose-700 transition mb-2 leading-snug">
                                <?= htmlspecialchars($item['nama']) ?>
                            </h3>

                            <p class="text-xs text-gray-500 line-clamp-2 mb-4 leading-relaxed flex-1">
                                <?= htmlspecialchars($item['deskripsi'] ?? 'Tidak ada deskripsi produk.') ?>
                            </p>

                            <div class="pt-3 border-t border-gray-100 flex items-center justify-between mt-auto mb-4">
                                <div>
                                    <span class="text-[11px] text-gray-400 block leading-none">Harga Satuan</span>
                                    <span class="text-lg font-black text-rose-800 leading-tight">
                                        <?= formatRupiah($item['harga']) ?>
                                    </span>
                                </div>
                                <button type="button" 
                                        onclick="openDetailModal(<?= htmlspecialchars(json_encode($item)) ?>, '<?= htmlspecialchars($foto_path) ?>')"
                                        class="p-2 text-gray-400 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition text-sm" title="Lihat Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>

                            <!-- Tombol Beli / Tambah Keranjang -->
                            <form action="index.php" method="POST" class="w-full">
                                <input type="hidden" name="id_produk" value="<?= $item['id'] ?>">
                                <input type="hidden" name="jumlah" value="1">
                                <button type="submit" name="tambah_keranjang" 
                                        <?= $is_habis ? 'disabled' : '' ?>
                                        class="w-full py-2.5 px-4 rounded-xl font-semibold text-xs transition flex items-center justify-center gap-2 <?= $is_habis ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-rose-800 hover:bg-rose-900 text-white shadow-md shadow-rose-200 active:scale-95' ?>">
                                    <i class="fa-solid fa-cart-plus"></i> <?= $is_habis ? 'Habis' : '+ Keranjang' ?>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Modal Quick View Produk -->
    <div id="modalDetail" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full overflow-hidden shadow-2xl animate-fade-in relative">
            <button onclick="closeDetailModal()" class="absolute top-4 right-4 z-10 w-9 h-9 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <div class="grid grid-cols-1 md:grid-cols-2">
                <div class="bg-gray-100 flex items-center justify-center p-4">
                    <img id="modalFoto" src="" alt="Produk" class="w-full h-64 md:h-full object-cover rounded-2xl shadow-inner">
                </div>
                <div class="p-6 sm:p-8 flex flex-col justify-between">
                    <div>
                        <span id="modalKategori" class="bg-rose-100 text-rose-800 text-xs font-semibold px-2.5 py-1 rounded-md"></span>
                        <h3 id="modalNama" class="text-xl font-extrabold text-gray-900 mt-2"></h3>
                        <div class="mt-2 text-2xl font-black text-rose-800" id="modalHarga"></div>
                        <div class="text-xs text-gray-500 mt-1">Stok Tersedia: <span id="modalStok" class="font-bold text-gray-800"></span> pcs</div>
                        <hr class="my-4 border-gray-200">
                        <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Deskripsi Produk:</h4>
                        <p id="modalDeskripsi" class="text-sm text-gray-600 leading-relaxed max-h-36 overflow-y-auto pr-1"></p>
                    </div>

                    <form action="index.php" method="POST" class="mt-6 pt-4 border-t border-gray-100">
                        <input type="hidden" name="id_produk" id="modalInputId" value="">
                        <div class="flex items-center gap-3 mb-4">
                            <label class="text-xs font-semibold text-gray-600">Jumlah:</label>
                            <input type="number" name="jumlah" id="modalInputQty" value="1" min="1" max="99" 
                                   class="w-20 px-3 py-1.5 border border-gray-300 rounded-lg text-sm text-center font-bold focus:ring-2 focus:ring-rose-600 focus:outline-none">
                        </div>
                        <button type="submit" name="tambah_keranjang" id="modalSubmitBtn"
                                class="w-full py-3 bg-rose-800 hover:bg-rose-900 text-white rounded-xl text-sm font-bold shadow-lg shadow-rose-200 transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-cart-plus"></i> Masukkan ke Keranjang Belanja
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script>
    function openDetailModal(item, fotoUrl) {
        document.getElementById('modalFoto').src = fotoUrl;
        document.getElementById('modalNama').innerText = item.nama;
        document.getElementById('modalKategori').innerText = item.kategori;
        document.getElementById('modalHarga').innerText = 'Rp ' + Number(item.harga).toLocaleString('id-ID');
        document.getElementById('modalStok').innerText = item.stok;
        document.getElementById('modalDeskripsi').innerText = item.deskripsi || 'Tidak ada deskripsi.';
        document.getElementById('modalInputId').value = item.id;
        
        const qtyInput = document.getElementById('modalInputQty');
        const submitBtn = document.getElementById('modalSubmitBtn');
        qtyInput.max = item.stok;
        
        if (parseInt(item.stok) <= 0) {
            submitBtn.disabled = true;
            submitBtn.innerText = 'Stok Habis';
            submitBtn.className = 'w-full py-3 bg-gray-200 text-gray-400 rounded-xl text-sm font-bold cursor-not-allowed';
            qtyInput.value = 0;
            qtyInput.disabled = true;
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-solid fa-cart-plus"></i> Masukkan ke Keranjang Belanja';
            submitBtn.className = 'w-full py-3 bg-rose-800 hover:bg-rose-900 text-white rounded-xl text-sm font-bold shadow-lg shadow-rose-200 transition flex items-center justify-center gap-2';
            qtyInput.value = 1;
            qtyInput.disabled = false;
        }

        document.getElementById('modalDetail').classList.remove('hidden');
    }

    function closeDetailModal() {
        document.getElementById('modalDetail').classList.add('hidden');
    }

    window.onclick = function(event) {
        const modal = document.getElementById('modalDetail');
        if (event.target === modal) {
            closeDetailModal();
        }
    }
    </script>
</body>
</html>