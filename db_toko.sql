-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 30, 2026 at 06:34 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_toko`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_detail`
--

CREATE TABLE `tb_detail` (
  `id_detail` int(11) NOT NULL,
  `id_transaksi` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_detail`
--

INSERT INTO `tb_detail` (`id_detail`, `id_transaksi`, `id_produk`, `jumlah`) VALUES
(2, 1, 2, 1),
(6, 4, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_kategori`
--

CREATE TABLE `tb_kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_kategori`
--

INSERT INTO `tb_kategori` (`id_kategori`, `nama_kategori`) VALUES
(5, 'Aksesori Pengantin'),
(4, 'Gaun Pengantin'),
(2, 'Kebaya'),
(1, 'Paket Rias');

-- --------------------------------------------------------

--
-- Table structure for table `tb_produk`
--

CREATE TABLE `tb_produk` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `harga` int(11) NOT NULL,
  `stok` int(11) NOT NULL,
  `poto` text DEFAULT NULL,
  `id_kategori` int(11) NOT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_produk`
--

INSERT INTO `tb_produk` (`id`, `nama`, `harga`, `stok`, `poto`, `id_kategori`, `deskripsi`) VALUES
(2, 'Paket Rias Akad Natural', 650000, 8, 'https://images.unsplash.com/photo-1537633552985-df8429e8048b?auto=format&fit=crop&w=900&q=80', 1, 'Paket rias natural untuk akad, dengan hasil lembut dan tahan lama.'),
(5, 'Kebaya Pengantin Adat Jawa', 2500000, 4, 'https://images.unsplash.com/photo-1743090286549-52f6d1983064?fm=jpg&q=60&w=1200&auto=format&fit=crop', 2, 'Kebaya pengantin bernuansa klasik dengan detail bordir untuk acara akad dan resepsi.'),
(7, 'Kebaya Brokat Modern', 1850000, 5, 'https://images.unsplash.com/photo-1681714552617-fe3f4cf4be47?fm=jpg&q=60&w=1200&auto=format&fit=crop', 2, 'Kebaya brokat modern dengan potongan anggun untuk momen pernikahan.'),
(9, 'Paket Rias Pengantin Sunda', 850000, 6, 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=900&q=80', 1, 'Paket rias bernuansa Sunda untuk akad atau resepsi, termasuk konsultasi tampilan.'),
(10, 'Paket Rias Pengantin Jawa', 950000, 5, 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=900&q=80', 1, 'Rias pengantin adat Jawa dengan tata rias dan sentuhan tradisional yang anggun.'),
(11, 'Paket Rias Modern Glam', 1100000, 5, 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?auto=format&fit=crop&w=900&q=80', 1, 'Rias modern glam untuk resepsi dengan pilihan tampilan yang dapat dikonsultasikan.'),
(12, 'Paket Rias Lengkap Resepsi', 1750000, 4, 'https://images.unsplash.com/photo-1591604466107-ec97de577aff?auto=format&fit=crop&w=900&q=80', 1, 'Paket rias resepsi lengkap dengan persiapan wajah dan penyesuaian gaya busana.'),
(13, 'Kebaya Akad Putih Premium', 3200000, 3, 'https://images.unsplash.com/photo-1743090286549-52f6d1983064?fm=jpg&q=60&w=1200&auto=format&fit=crop', 2, 'Kebaya putih premium untuk akad, dirancang dengan detail halus dan siluet elegan.'),
(14, 'Kebaya Pengantin Adat Sunda', 2800000, 3, 'https://images.unsplash.com/photo-1681714552617-fe3f4cf4be47?fm=jpg&q=60&w=1200&auto=format&fit=crop', 2, 'Kebaya pengantin adat Sunda dengan detail tradisional untuk akad dan resepsi.'),
(15, 'Kebaya Tulle Dusty Rose', 2200000, 4, 'https://images.unsplash.com/photo-1743090286549-52f6d1983064?fm=jpg&q=60&w=1200&auto=format&fit=crop', 2, 'Kebaya tulle warna dusty rose dengan potongan modern dan detail renda halus.'),
(16, 'Gaun Pengantin Putih A-Line', 3500000, 3, 'https://images.unsplash.com/photo-1743090286549-52f6d1983064?fm=jpg&q=60&w=1200&auto=format&fit=crop', 4, 'Gaun putih siluet A-line dengan tampilan klasik untuk upacara dan resepsi.'),
(17, 'Gaun Pengantin Muslimah Lace', 3900000, 3, 'https://images.unsplash.com/photo-1681714552617-fe3f4cf4be47?fm=jpg&q=60&w=1200&auto=format&fit=crop', 4, 'Gaun pengantin muslimah dengan detail renda dan potongan yang santun.'),
(18, 'Gaun Resepsi Satin Ivory', 4200000, 2, 'https://images.unsplash.com/photo-1743090286549-52f6d1983064?fm=jpg&q=60&w=1200&auto=format&fit=crop', 4, 'Gaun satin warna ivory dengan tampilan bersih dan elegan untuk resepsi.'),
(19, 'Gaun Pengantin Adat Sunda', 3800000, 2, 'https://images.unsplash.com/photo-1681714552617-fe3f4cf4be47?fm=jpg&q=60&w=1200&auto=format&fit=crop', 4, 'Gaun pengantin dengan inspirasi adat Sunda untuk perayaan penuh makna.'),
(20, 'Gaun Pengantin Custom Bordir', 4800000, 2, 'https://images.unsplash.com/photo-1743090286549-52f6d1983064?fm=jpg&q=60&w=1200&auto=format&fit=crop', 4, 'Gaun custom dengan detail bordir, dapat disesuaikan melalui konsultasi desain.'),
(21, 'Mahkota Pengantin Kristal', 650000, 8, 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=900&q=80', 5, 'Mahkota kristal sebagai aksesori kepala untuk melengkapi tampilan pengantin.'),
(22, 'Veil Pengantin Cathedral', 750000, 5, 'https://images.unsplash.com/photo-1523438885200-e635ba2c371e?auto=format&fit=crop&w=900&q=80', 5, 'Veil panjang bergaya cathedral untuk melengkapi gaun pengantin klasik.'),
(23, 'Hand Bouquet Mawar Putih', 350000, 12, 'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?auto=format&fit=crop&w=900&q=80', 5, 'Hand bouquet mawar putih yang dirangkai untuk akad, foto, maupun resepsi.'),
(24, 'Bros Bunga Pengantin Handmade', 125000, 20, 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?auto=format&fit=crop&w=900&q=80', 5, 'Bros bunga buatan tangan untuk aksen kebaya dan busana keluarga pengantin.'),
(25, 'Hiasan Sanggul Melati', 275000, 10, 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=900&q=80', 5, 'Hiasan sanggul bernuansa melati untuk melengkapi tata rias pengantin tradisional.');

-- --------------------------------------------------------

--
-- Table structure for table `tb_transaksi`
--

CREATE TABLE `tb_transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `total_harga` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_transaksi`
--

INSERT INTO `tb_transaksi` (`id_transaksi`, `id_pelanggan`, `tanggal`, `total_harga`) VALUES
(1, 2, '2026-08-20', 95000),
(4, 15, '2026-09-30', 65000);

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `hp` varchar(255) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `role` enum('admin','pelanggan') NOT NULL DEFAULT 'pelanggan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id`, `nama`, `email`, `username`, `password`, `hp`, `alamat`, `role`) VALUES
(1, 'Administrator Toko', 'admin@umkm.id', 'admin', '0192023a7bbd73250516f069df18b500', '081234567890', 'Jl. Merdeka No. 1, Jakarta', 'admin'),
(2, 'Budi Santoso', 'budi@gmail.com', 'budi', '325077d1d7b6fa325b095fb212f3bc42', '082198765432', 'Jl. Melati No. 45, Bandung', 'pelanggan'),
(11, 'daffa1', 'daffagga@gmail.com', 'daffa1', '9482f4a6062bc5c99be22d570c0eb290', '08994003', 'jhsdvshdv', 'admin'),
(14, 'daffa', 'daffa2@gmail.com', 'daffa', 'daffa2', '266371882', 'uhdvd', 'admin'),
(15, 'pelanggan', 'pelanggan@local.invalid', 'pelanggan', '7f78f06d2d1262a0a222ca9834b15d9d', '0907877709', 'jln perjuangan', 'pelanggan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_detail`
--
ALTER TABLE `tb_detail`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `fk_detail_transaksi` (`id_transaksi`),
  ADD KEY `fk_detail_produk` (`id_produk`);

--
-- Indexes for table `tb_kategori`
--
ALTER TABLE `tb_kategori`
  ADD PRIMARY KEY (`id_kategori`),
  ADD UNIQUE KEY `uq_kategori_nama` (`nama_kategori`);

--
-- Indexes for table `tb_produk`
--
ALTER TABLE `tb_produk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_produk_kategori` (`id_kategori`);

--
-- Indexes for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `fk_transaksi_user` (`id_pelanggan`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_detail`
--
ALTER TABLE `tb_detail`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tb_kategori`
--
ALTER TABLE `tb_kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_produk`
--
ALTER TABLE `tb_produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_detail`
--
ALTER TABLE `tb_detail`
  ADD CONSTRAINT `fk_detail_produk` FOREIGN KEY (`id_produk`) REFERENCES `tb_produk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_transaksi` FOREIGN KEY (`id_transaksi`) REFERENCES `tb_transaksi` (`id_transaksi`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_produk`
--
ALTER TABLE `tb_produk`
  ADD CONSTRAINT `fk_produk_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `tb_kategori` (`id_kategori`) ON UPDATE CASCADE;

--
-- Constraints for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD CONSTRAINT `fk_transaksi_user` FOREIGN KEY (`id_pelanggan`) REFERENCES `tb_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
