<?php
// includes/footer.php
$footer_categories = [];
$footer_category_result = isset($koneksi) ? mysqli_query($koneksi, 'SELECT nama_kategori FROM tb_kategori ORDER BY nama_kategori ASC') : false;
if ($footer_category_result) {
    while ($footer_category = mysqli_fetch_assoc($footer_category_result)) {
        $footer_categories[] = $footer_category['nama_kategori'];
    }
}
?>
<footer class="bg-gray-900 text-gray-300 pt-16 pb-8 mt-20 border-t border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 pb-12 border-b border-gray-800">
            <!-- Brand Info -->
            <div class="space-y-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-emerald-900/50">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>
                    <span class="text-xl font-extrabold text-white tracking-tight">UMKM<span class="text-emerald-500">Hebat</span></span>
                </div>
                <p class="text-sm text-gray-400 leading-relaxed">
                    Platform e-commerce resmi pemberdayaan Usaha Mikro, Kecil, dan Menengah (UMKM). Menyediakan aneka ragam produk lokal berkualitas asli nusantara.
                </p>
                <div class="flex space-x-3 pt-2">
                    <a href="#" class="w-9 h-9 rounded-lg bg-gray-800 hover:bg-emerald-600 text-gray-300 hover:text-white flex items-center justify-center transition">
                        <i class="fa-brands fa-facebook-f text-sm"></i>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-lg bg-gray-800 hover:bg-emerald-600 text-gray-300 hover:text-white flex items-center justify-center transition">
                        <i class="fa-brands fa-instagram text-sm"></i>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-lg bg-gray-800 hover:bg-emerald-600 text-gray-300 hover:text-white flex items-center justify-center transition">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-lg bg-gray-800 hover:bg-emerald-600 text-gray-300 hover:text-white flex items-center justify-center transition">
                        <i class="fa-brands fa-youtube text-sm"></i>
                    </a>
                </div>
            </div>

            <!-- Kategori Produk -->
            <div>
                <h4 class="text-white font-bold text-base mb-4 tracking-wide uppercase text-sm">Kategori Unggulan</h4>
                <ul class="space-y-2.5 text-sm">
                    <?php foreach ($footer_categories as $category): ?>
                        <li>
                            <a href="index.php?kategori=<?= rawurlencode($category) ?>#katalog" class="hover:text-emerald-400 transition flex items-center gap-2">
                                <i class="fa-solid fa-chevron-right text-xs text-gray-600"></i>
                                <?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <?php if (!$footer_categories): ?>
                        <li class="text-gray-500">Belum ada kategori.</li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Tautan Cepat -->
            <div>
                <h4 class="text-white font-bold text-base mb-4 tracking-wide uppercase text-sm">Tautan Cepat</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="index.php" class="hover:text-emerald-400 transition flex items-center gap-2"><i class="fa-solid fa-chevron-right text-xs text-gray-600"></i> Beranda Toko</a></li>
                    <li><a href="keranjang.php" class="hover:text-emerald-400 transition flex items-center gap-2"><i class="fa-solid fa-chevron-right text-xs text-gray-600"></i> Keranjang Belanja</a></li>
                    <li><a href="profil.php" class="hover:text-emerald-400 transition flex items-center gap-2"><i class="fa-solid fa-chevron-right text-xs text-gray-600"></i> Akun & Profil</a></li>
                    <li><a href="login.php" class="hover:text-emerald-400 transition flex items-center gap-2"><i class="fa-solid fa-chevron-right text-xs text-gray-600"></i> Login Admin & Mitra</a></li>
                </ul>
            </div>

            <!-- Kontak Toko -->
            <div>
                <h4 class="text-white font-bold text-base mb-4 tracking-wide uppercase text-sm">Kontak & Bantuan</h4>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-location-dot text-emerald-500 mt-1"></i>
                        <span>Desa Cempaka blok sigoleng kulon, jl. Raya Cempaka</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa-solid fa-phone text-emerald-500"></i>
                        <span>
                            <!-- whatsapp -->
                            <a href="https://wa.me/6287872731758" target="_blank" class="hover:text-emerald-400 transition">+6287872731758</a>
                        </span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa-solid fa-envelope text-emerald-500"></i>
                        <span>cs@ummipengantin.id</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa-solid fa-clock text-emerald-500"></i>
                        <span>Buka Setiap Hari: 08.00 - 21.00 WIB</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Copyright -->
        <div class="pt-8 flex flex-col sm:flex-row justify-between items-center text-xs text-gray-500 gap-4">
            <p>&copy; <?= date('Y') ?> <strong>UMKM Hebat</strong></p>
            <div class="flex items-center space-x-4">
                <span>Dibuat dengan <i class="fa-solid fa-heart text-rose-500 mx-0.5"></i> untuk Pemberdayaan UMKM Indonesia</span>
            </div>
        </div>
    </div>
</footer>
</body>
</html>

