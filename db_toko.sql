-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 06, 2026 at 04:46 PM
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
-- Table structure for table `log_produk`
--

CREATE TABLE `log_produk` (
  `id_log` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `aksi` varchar(20) NOT NULL,
  `waktu` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_produk`
--

INSERT INTO `log_produk` (`id_log`, `id_produk`, `nama_produk`, `aksi`, `waktu`) VALUES
(1, 24, 'Cincin Pernikahan Emas Putih', 'DIUBAH', '2026-10-06 13:37:48'),
(2, 25, 'Hiasan Sanggul Melati', 'DIUBAH', '2026-10-06 13:40:56'),
(3, 25, 'Hiasan Sanggul Melati', 'DIUBAH', '2026-10-06 21:40:20');

-- --------------------------------------------------------

--
-- Table structure for table `tb_detail`
--

CREATE TABLE `tb_detail` (
  `id_detail` int(11) NOT NULL,
  `id_transaksi` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `jumlah_bonus` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_detail`
--

INSERT INTO `tb_detail` (`id_detail`, `id_transaksi`, `id_produk`, `jumlah`, `jumlah_bonus`) VALUES
(2, 1, 2, 1, 0),
(6, 4, 2, 1, 0),
(7, 5, 19, 1, 0),
(8, 6, 25, 1, 0),
(9, 7, 24, 1, 0),
(10, 8, 25, 1, 0),
(11, 9, 25, 5, 0),
(12, 10, 23, 1, 0),
(13, 10, 25, 4, 0),
(14, 11, 25, 4, 0),
(15, 11, 23, 1, 0),
(16, 12, 25, 5, 1);

--
-- Triggers `tb_detail`
--
DELIMITER $$
CREATE TRIGGER `trg_detail_bonus_after_insert` AFTER INSERT ON `tb_detail` FOR EACH ROW BEGIN
    UPDATE tb_produk
    SET stok = stok - NEW.jumlah - NEW.jumlah_bonus
    WHERE id = NEW.id_produk
      AND stok >= NEW.jumlah + NEW.jumlah_bonus;

    IF ROW_COUNT() = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Stok tidak cukup untuk pembelian dan bonus';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_detail_bonus_before_insert` BEFORE INSERT ON `tb_detail` FOR EACH ROW BEGIN
    DECLARE jumlah_sebelumnya INT DEFAULT 0;

    IF NEW.jumlah <= 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Jumlah pembelian harus lebih dari 0';
    END IF;

    SELECT COALESCE(SUM(jumlah), 0)
    INTO jumlah_sebelumnya
    FROM tb_detail
    WHERE id_transaksi = NEW.id_transaksi;

    SET NEW.jumlah_bonus = FLOOR((jumlah_sebelumnya + NEW.jumlah) / 5)
                         - FLOOR(jumlah_sebelumnya / 5);
END
$$
DELIMITER ;

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
(2, 'Paket Rias Akad Natural', 650000, 8, 'https://images.unsplash.com/photo-1583784561126-c18e59057f3b?auto=format&fit=crop&w=900&q=80', 1, 'Paket rias natural untuk akad, dengan hasil lembut dan tahan lama.'),
(5, 'Kebaya Pengantin Adat Jawa', 2500000, 4, 'https://images.unsplash.com/photo-1743090286549-52f6d1983064?auto=format&fit=crop&w=900&q=80', 2, 'Kebaya pengantin bernuansa klasik dengan detail bordir untuk acara akad dan resepsi.'),
(7, 'Kebaya Brokat Modern', 1850000, 5, 'https://images.unsplash.com/photo-1681714552617-fe3f4cf4be47?auto=format&fit=crop&w=900&q=80', 2, 'Kebaya brokat modern dengan potongan anggun untuk momen pernikahan.'),
(9, 'Paket Rias Pengantin Sunda', 850000, 6, 'https://images.unsplash.com/photo-1559980828-dc98e2c105d5?auto=format&fit=crop&w=900&q=80', 1, 'Paket rias bernuansa Sunda untuk akad atau resepsi, termasuk konsultasi tampilan.'),
(10, 'Paket Rias Pengantin Jawa', 950000, 5, 'https://images.unsplash.com/photo-1551533257-b74835499334?auto=format&fit=crop&w=900&q=80', 1, 'Rias pengantin adat Jawa dengan tata rias dan sentuhan tradisional yang anggun.'),
(11, 'Paket Rias Modern Glam', 1100000, 5, 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=900&q=80', 1, 'Rias modern glam untuk resepsi dengan pilihan tampilan yang dapat dikonsultasikan.'),
(12, 'Paket Rias Lengkap Resepsi', 1750000, 4, 'https://images.unsplash.com/photo-1537633552985-df8429e8048b?auto=format&fit=crop&w=900&q=80', 1, 'Paket rias resepsi lengkap dengan persiapan wajah dan penyesuaian gaya busana.'),
(13, 'Kebaya Akad Putih Premium', 3200000, 3, 'https://images.unsplash.com/photo-1696831443481-9fef47852c91?auto=format&fit=crop&w=900&q=80', 2, 'Kebaya putih premium untuk akad, dirancang dengan detail halus dan siluet elegan.'),
(14, 'Kebaya Pengantin Adat Sunda', 2800000, 3, 'https://images.unsplash.com/photo-1632729331892-b318f62e4e72?auto=format&fit=crop&w=900&q=80', 2, 'Kebaya pengantin adat Sunda dengan detail tradisional untuk akad dan resepsi.'),
(15, 'Kebaya Tulle Dusty Rose', 2200000, 4, 'https://images.unsplash.com/photo-1744367848789-bf6761d0fdde?auto=format&fit=crop&w=900&q=80', 2, 'Kebaya tulle warna dusty rose dengan potongan modern dan detail renda halus.'),
(16, 'Gaun Pengantin Putih A-Line', 3500000, 3, 'https://images.unsplash.com/photo-1781269034633-7add54287c6c?auto=format&fit=crop&w=900&q=80', 4, 'Gaun putih siluet A-line dengan tampilan klasik untuk upacara dan resepsi.'),
(17, 'Gaun Pengantin Muslimah Lace', 3900000, 3, 'https://images.unsplash.com/photo-1678830795557-d7886693c9ee?auto=format&fit=crop&w=900&q=80', 4, 'Gaun pengantin muslimah dengan detail renda dan potongan yang santun.'),
(18, 'Gaun Resepsi Satin Ivory', 4200000, 2, 'https://images.unsplash.com/photo-1774660810310-5023390fe028?auto=format&fit=crop&w=900&q=80', 4, 'Gaun satin warna ivory dengan tampilan bersih dan elegan untuk resepsi.'),
(19, 'Gaun Pengantin Adat Sunda', 3800000, 1, 'https://images.unsplash.com/photo-1782787231777-57f68be77d76?auto=format&fit=crop&w=900&q=80', 4, 'Gaun pengantin dengan inspirasi adat Sunda untuk perayaan penuh makna.'),
(20, 'Gaun Pengantin Custom Bordir', 4800000, 2, 'https://images.unsplash.com/photo-1516290774656-b3a48f655bcd?auto=format&fit=crop&w=900&q=80', 4, 'Gaun custom dengan detail bordir, dapat disesuaikan melalui konsultasi desain.'),
(21, 'Mahkota Pengantin Kristal', 650000, 8, 'https://images.unsplash.com/photo-1721206625649-b7fbe071c7b9?auto=format&fit=crop&w=900&q=80', 5, 'Mahkota kristal sebagai aksesori kepala untuk melengkapi tampilan pengantin.'),
(22, 'Veil Pengantin Cathedral', 750000, 5, 'https://images.unsplash.com/photo-1753703986156-46e2b962c012?auto=format&fit=crop&w=900&q=80', 5, 'Veil panjang bergaya cathedral untuk melengkapi gaun pengantin klasik.'),
(23, 'Hand Bouquet Mawar Putih', 350000, 12, 'https://images.unsplash.com/photo-1521543832500-49e69fb2bea2?auto=format&fit=crop&w=900&q=80', 5, 'Hand bouquet mawar putih yang dirangkai untuk akad, foto, maupun resepsi.'),
(24, 'Cincin Pernikahan Emas Putih', 125000, 19, 'https://images.unsplash.com/photo-1515934751635-c81c6bc9a2d8?auto=format&fit=crop&w=900&q=80', 5, 'Cincin pernikahan dengan tampilan emas putih untuk melengkapi koleksi aksesori pengantin.'),
(25, 'Hiasan Sanggul Melati', 275000, 3, 'https://images.unsplash.com/photo-1783255166225-6890aee7381e?auto=format&fit=crop&w=900&q=80', 5, 'Hiasan sanggul bernuansa melati untuk melengkapi tata rias pengantin tradisional.');

--
-- Triggers `tb_produk`
--
DELIMITER $$
CREATE TRIGGER `after_produk_delete` AFTER DELETE ON `tb_produk` FOR EACH ROW BEGIN
    INSERT INTO log_produk (id_produk, nama_produk, aksi)
    VALUES (OLD.id, OLD.nama, 'DIHAPUS');
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_produk_insert` AFTER INSERT ON `tb_produk` FOR EACH ROW BEGIN
    INSERT INTO log_produk (id_produk, nama_produk, aksi)
    VALUES (NEW.id, NEW.nama, 'DITAMBAHKAN');
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_produk_update` AFTER UPDATE ON `tb_produk` FOR EACH ROW BEGIN
    INSERT INTO log_produk (id_produk, nama_produk, aksi)
    VALUES (NEW.id, NEW.nama, 'DIUBAH');
END
$$
DELIMITER ;

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
(4, 15, '2026-09-30', 65000),
(5, 15, '2026-09-30', 3800000),
(6, 16, '2026-10-04', 275000),
(7, 15, '2026-10-06', 125000),
(8, 15, '2026-10-06', 275000),
(9, 15, '2026-10-06', 1375000),
(10, 15, '2026-10-06', 1450000),
(11, 15, '2026-10-06', 1450000),
(12, 15, '2026-10-06', 1375000);

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
(15, 'pelanggan', 'pelanggan@local.invalid', 'pelanggan', '7f78f06d2d1262a0a222ca9834b15d9d', '0907877709', 'jln perjuangan', 'pelanggan'),
(16, 'daffa00', 'daffa00@local.invalid', 'daffa00', '17aace767bdab423c1aab85f171ee3d0', '', '', 'pelanggan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `log_produk`
--
ALTER TABLE `log_produk`
  ADD PRIMARY KEY (`id_log`);

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
-- AUTO_INCREMENT for table `log_produk`
--
ALTER TABLE `log_produk`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_detail`
--
ALTER TABLE `tb_detail`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tb_kategori`
--
ALTER TABLE `tb_kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_produk`
--
ALTER TABLE `tb_produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
