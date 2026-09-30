<?php
// includes/navbar.php
$cart_count = getCartCount();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<style>
    html[data-theme="dark"] { color-scheme: dark; }
    html[data-theme="dark"] body { background-color: #101714 !important; color: #e5eee8 !important; }
    html[data-theme="dark"] .bg-white { background-color: #18211d !important; }
    html[data-theme="dark"] .bg-gray-50,
    html[data-theme="dark"] .bg-gray-100 { background-color: #202a25 !important; }
    html[data-theme="dark"] .bg-rose-50,
    html[data-theme="dark"] .bg-emerald-50,
    html[data-theme="dark"] .bg-amber-50,
    html[data-theme="dark"] .bg-blue-50 { background-color: #26332c !important; }
    html[data-theme="dark"] .text-gray-900,
    html[data-theme="dark"] .text-gray-800,
    html[data-theme="dark"] .text-gray-700 { color: #e5eee8 !important; }
    html[data-theme="dark"] .text-gray-600,
    html[data-theme="dark"] .text-gray-500,
    html[data-theme="dark"] .text-gray-400 { color: #a7b5ad !important; }
    html[data-theme="dark"] [class*="border-gray-"] { border-color: #2b3932 !important; }
    html[data-theme="dark"] input:not([type="checkbox"]),
    html[data-theme="dark"] textarea,
    html[data-theme="dark"] select { background-color: #101714; color: #e5eee8; border-color: #405148; }
</style>
<!-- Topbar Info -->
<div class="bg-gradient-to-r from-emerald-700 to-teal-800 text-white text-xs py-2 px-4">
    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
        <div class="flex items-center gap-4">
            <span><i class="fa-solid fa-phone mr-1"></i> +6287872731758</span>
            <span class="hidden sm:inline">|</span>
            <span class="hidden sm:inline"><i class="fa-solid fa-envelope mr-1"></i> info@ummipengantin.id</span>
            <span class="hidden md:inline">|</span>
            <span class="hidden md:inline"><i class="fa-solid fa-location-dot mr-1"></i> cirebon, jawa barat</span>
        </div>
        <div class="flex items-center gap-3">
            <span class="bg-emerald-600/60 px-2 py-0.5 rounded text-[11px] font-medium tracking-wide">
                <i class="fa-solid fa-store mr-1"></i> 100% Produk Lokal Berdaya
            </span>
        </div>
    </div>
</div>

<!-- Main Navbar -->
<nav class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-18 py-3">
            <!-- Brand Logo -->
            <div class="flex items-center gap-3">
                <a href="index.php" class="flex items-center gap-2.5 group">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-bold text-xl shadow-md shadow-emerald-200 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>
                    <div>
                        <div class="font-extrabold text-xl text-gray-800 tracking-tight leading-none group-hover:text-emerald-700 transition">
                            UMKM<span class="text-emerald-600">Hebat</span>
                        </div>
                        <div class="text-[11px] text-gray-500 font-medium tracking-wide">Pasar Digital UMKM</div>
                    </div>
                </a>
            </div>

            <!-- Navigation Links (Desktop) -->
            <div class="hidden md:flex items-center space-x-6">
                <a href="index.php" class="<?= $current_page == 'index.php' ? 'text-emerald-600 font-semibold' : 'text-gray-600 hover:text-emerald-600' ?> transition">
                    <i class="fa-solid fa-house mr-1 text-sm"></i> Beranda
                </a>
                <a href="index.php#katalog" class="text-gray-600 hover:text-emerald-600 transition">
                    <i class="fa-solid fa-grid-2 mr-1 text-sm"></i> Katalog Produk
                </a>
                <a href="keranjang.php" class="<?= $current_page == 'keranjang.php' ? 'text-emerald-600 font-semibold' : 'text-gray-600 hover:text-emerald-600' ?> transition">
                    <i class="fa-solid fa-cart-shopping mr-1 text-sm"></i> Keranjang
                </a>
            </div>

            <!-- Right Actions: Cart & Auth -->
            <div class="flex items-center gap-3">
                <button type="button" id="themeToggle" aria-label="Aktifkan mode gelap" aria-pressed="false"
                        title="Aktifkan mode gelap" class="p-2.5 text-gray-600 hover:text-emerald-600 hover:bg-gray-50 rounded-xl transition flex items-center justify-center">
                    <i class="fa-solid fa-moon text-lg" aria-hidden="true"></i>
                </button>
                <!-- Cart Button -->
                <a href="keranjang.php" class="relative p-2.5 text-gray-600 hover:text-emerald-600 hover:bg-gray-50 rounded-xl transition flex items-center justify-center">
                    <i class="fa-solid fa-cart-shopping text-xl"></i>
                    <?php if ($cart_count > 0): ?>
                        <span class="absolute -top-1 -right-1 bg-rose-500 text-white text-[11px] font-bold h-5 w-5 rounded-full flex items-center justify-center shadow-md animate-pulse">
                            <?= $cart_count > 99 ? '99+' : $cart_count ?>
                        </span>
                    <?php endif; ?>
                </a>

                <!-- User Auth Controls -->
                <?php if (isLoggedIn()): ?>
                    <div class="relative" id="userMenuDropdown">
                        <button type="button" onclick="toggleDropdown()" class="flex items-center gap-2 py-1.5 px-3 rounded-xl border border-gray-200 hover:border-emerald-300 hover:bg-emerald-50/50 transition">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-sm">
                                <?= strtoupper(substr($_SESSION['nama'] ?? 'U', 0, 1)) ?>
                            </div>
                            <div class="text-left hidden sm:block">
                                <div class="text-xs font-semibold text-gray-800 leading-tight">
                                    <?= htmlspecialchars(explode(' ', $_SESSION['nama'] ?? 'User')[0]) ?>
                                </div>
                                <span class="text-[10px] text-emerald-600 font-medium capitalize">
                                    <?= htmlspecialchars($_SESSION['role'] ?? 'Pelanggan') ?>
                                </span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-gray-400 text-xs ml-1"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="dropdownMenu" class="hidden absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50 animate-fade-in">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-xs text-gray-500">Masuk sebagai:</p>
                                <p class="text-sm font-bold text-gray-800 truncate"><?= htmlspecialchars($_SESSION['nama'] ?? '') ?></p>
                                <p class="text-xs text-gray-400 truncate"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></p>
                            </div>

                            <?php if (isAdmin()): ?>
                                <a href="admin/dashboard.php" class="flex items-center gap-2 px-4 py-2.5 text-sm text-emerald-700 font-medium hover:bg-emerald-50 transition">
                                    <i class="fa-solid fa-gauge-high text-emerald-600 w-4"></i> Dashboard Admin
                                </a>
                            <?php else: ?>
                                <a href="profil.php" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <i class="fa-regular fa-user text-gray-400 w-4"></i> Profil Saya
                                </a>
                                <a href="profil.php#riwayat" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <i class="fa-solid fa-clock-rotate-left text-gray-400 w-4"></i> Riwayat Pesanan
                                </a>
                            <?php endif; ?>

                            <hr class="my-1 border-gray-100">
                            <a href="logout.php" onclick="return confirm('Apakah Anda yakin ingin logout?');" class="flex items-center gap-2 px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50 transition">
                                <i class="fa-solid fa-arrow-right-from-bracket text-rose-500 w-4"></i> Logout
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="hidden sm:flex items-center gap-2">
                        <a href="login.php" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-emerald-700 hover:bg-gray-50 rounded-xl transition">
                            Masuk
                        </a>
                        <a href="register.php" class="px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md shadow-emerald-200 transition">
                            Daftar
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Mobile Menu Button -->
                <button type="button" onclick="toggleMobileMenu()" class="md:hidden p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Container -->
        <div id="mobileMenu" class="hidden md:hidden border-t border-gray-100 py-3 space-y-2">
            <a href="index.php" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 hover:bg-gray-50">
                <i class="fa-solid fa-house mr-2 text-emerald-600"></i> Beranda
            </a>
            <a href="index.php#katalog" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 hover:bg-gray-50">
                <i class="fa-solid fa-bag-shopping mr-2 text-emerald-600"></i> Katalog Produk
            </a>
            <a href="keranjang.php" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 hover:bg-gray-50">
                <i class="fa-solid fa-cart-shopping mr-2 text-emerald-600"></i> Keranjang Belanja (<?= $cart_count ?>)
            </a>

            <?php if (!isLoggedIn()): ?>
                <div class="pt-2 border-t border-gray-100 flex gap-2">
                    <a href="login.php" class="flex-1 text-center py-2 px-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Masuk
                    </a>
                    <a href="register.php" class="flex-1 text-center py-2 px-3 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700">
                        Daftar Akun
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
const themeToggle = document.getElementById('themeToggle');
const applyTheme = (theme) => {
    document.documentElement.dataset.theme = theme;
    document.documentElement.style.colorScheme = theme;
    const darkMode = theme === 'dark';
    themeToggle.innerHTML = `<i class="fa-solid ${darkMode ? 'fa-sun' : 'fa-moon'} text-lg" aria-hidden="true"></i>`;
    themeToggle.setAttribute('aria-label', darkMode ? 'Aktifkan mode terang' : 'Aktifkan mode gelap');
    themeToggle.setAttribute('aria-pressed', String(darkMode));
    themeToggle.title = darkMode ? 'Aktifkan mode terang' : 'Aktifkan mode gelap';
};

const savedTheme = localStorage.getItem('umkm-theme');
const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
applyTheme(savedTheme || systemTheme);

themeToggle.addEventListener('click', () => {
    const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
    localStorage.setItem('umkm-theme', nextTheme);
    applyTheme(nextTheme);
});

function toggleDropdown() {
    const menu = document.getElementById('dropdownMenu');
    menu.classList.toggle('hidden');
}

function toggleMobileMenu() {
    const mobile = document.getElementById('mobileMenu');
    mobile.classList.toggle('hidden');
}

// Tutup dropdown jika klik di luar
window.addEventListener('click', function(e) {
    const dropdown = document.getElementById('dropdownMenu');
    const btn = document.getElementById('userMenuDropdown');
    if (dropdown && btn && !btn.contains(e.target)) {
        dropdown.classList.add('hidden');
    }
});
</script>

