<?php
// admin/layout_header.php - Header & Sidebar Admin Template
require_once __DIR__ . '/../config/koneksi.php';
/** @var mysqli $koneksi */
global $koneksi;

// Proteksi Wajib Admin
requireAdmin('../login.php');

$current_admin_page = basename($_SERVER['PHP_SELF']);
$admin_user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Dashboard Admin' ?> - Panel UMKM Hebat</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#059669',
                        dark: '#0f172a'
                    }
                }
            }
        }
    </script>
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans text-gray-800 antialiased min-h-screen flex">

    <!-- Sidebar Admin (Desktop) -->
    <aside id="sidebar" class="w-64 bg-slate-900 text-slate-300 min-h-screen flex flex-col flex-shrink-0 transition-all duration-300 z-50 fixed md:static inset-y-0 left-0 -translate-x-full md:translate-x-0">
        <!-- Brand Logo -->
        <div class="h-20 flex items-center px-6 border-b border-slate-800 gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-emerald-900/40">
                <i class="fa-solid fa-gauge-high"></i>
            </div>
            <div>
                <span class="text-lg font-black text-white tracking-tight">Admin<span class="text-emerald-500">UMKM</span></span>
                <span class="text-[10px] text-slate-400 block -mt-1 font-medium">Panel Manajemen Toko</span>
            </div>
        </div>

        <!-- Navigation Menus -->
        <div class="px-4 py-6 flex-1 space-y-1.5 overflow-y-auto">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Menu Utama</div>
            
            <a href="dashboard.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition <?= $current_admin_page == 'dashboard.php' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/40' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>">
                <i class="fa-solid fa-chart-pie w-4 text-center"></i> Dashboard
            </a>

            <a href="produk.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition <?= $current_admin_page == 'produk.php' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/40' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>">
                <i class="fa-solid fa-boxes-stacked w-4 text-center"></i> Manajemen Produk
            </a>

            <a href="kategori.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition <?= $current_admin_page == 'kategori.php' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/40' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>">
                <i class="fa-solid fa-tags w-4 text-center"></i> Manajemen Kategori
            </a>

            <a href="transaksi.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition <?= $current_admin_page == 'transaksi.php' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/40' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>">
                <i class="fa-solid fa-receipt w-4 text-center"></i> Manajemen Transaksi
            </a>

            <a href="pelanggan.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition <?= $current_admin_page == 'pelanggan.php' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/40' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>">
                <i class="fa-solid fa-users w-4 text-center"></i> Data Pelanggan
            </a>

            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 pt-6 mb-2">Akses Cepat</div>

            <a href="../index.php" target="_blank" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-emerald-400 hover:bg-slate-800 transition">
                <i class="fa-solid fa-arrow-up-right-from-square w-4 text-center"></i> Buka Website Toko
            </a>

            <a href="../logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar dari panel admin?');" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-rose-400 hover:text-rose-300 hover:bg-rose-950/40 transition">
                <i class="fa-solid fa-power-off w-4 text-center"></i> Keluar (Logout)
            </a>
        </div>

        <!-- Admin Profile Info -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/60">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-xs">
                    <?= strtoupper(substr($admin_user['nama'] ?? 'A', 0, 1)) ?>
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-bold text-white truncate"><?= htmlspecialchars($admin_user['nama']) ?></p>
                    <p class="text-[10px] text-emerald-400 font-medium">Administrator</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Overlay for mobile sidebar -->
    <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>

    <!-- Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Topbar -->
        <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-8 sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <button type="button" onclick="toggleSidebar()" class="md:hidden p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <div>
                    <h1 class="text-lg font-bold text-gray-900 leading-tight"><?= $page_title ?? 'Dashboard' ?></h1>
                    <p class="text-xs text-gray-500 hidden sm:block"><?= date('l, d F Y') ?></p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <a href="../index.php" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg text-xs font-semibold transition border border-emerald-200">
                    <i class="fa-solid fa-store"></i> Toko Live
                </a>
                <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>
                <div class="flex items-center gap-2 text-xs font-medium text-gray-600">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Admin Mode</span>
                </div>
            </div>
        </header>

        <!-- Main Content Wrapper -->
        <main class="flex-1 p-4 sm:p-8 overflow-y-auto">

