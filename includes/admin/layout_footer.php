        </main>
        <!-- Admin Footer -->
        <footer class="bg-white border-t border-gray-200 py-4 px-8 text-center text-xs text-gray-500">
            &copy; <?= date('Y') ?> <strong>UMKM Hebat Admin Panel</strong>. Sistem Manajemen Penjualan Produk Lokal.
        </footer>
    </div>

    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }
    </script>
</body>
</html>

