USE `db_toko`;
START TRANSACTION;

UPDATE `tb_kategori`
SET `nama_kategori` = CASE `id_kategori`
  WHEN 1 THEN 'Paket Rias'
  WHEN 2 THEN 'Kebaya'
  WHEN 4 THEN 'Gaun Pengantin'
  WHEN 5 THEN 'Aksesori Pengantin'
END
WHERE `id_kategori` IN (1, 2, 4, 5);

UPDATE `tb_produk` AS p
JOIN (
  SELECT 2 AS `id`, 'Paket Rias Akad Natural' AS `nama`, 650000 AS `harga`, 8 AS `stok`, 'https://images.unsplash.com/photo-1583784561126-c18e59057f3b?auto=format&fit=crop&w=900&q=80' AS `poto`, 'Paket rias natural untuk akad, dengan hasil lembut dan tahan lama.' AS `deskripsi`
  UNION ALL SELECT 9, 'Paket Rias Pengantin Sunda', 850000, 6, 'https://images.unsplash.com/photo-1559980828-dc98e2c105d5?auto=format&fit=crop&w=900&q=80', 'Paket rias bernuansa Sunda untuk akad atau resepsi, termasuk konsultasi tampilan.'
  UNION ALL SELECT 10, 'Paket Rias Pengantin Jawa', 950000, 5, 'https://images.unsplash.com/photo-1551533257-b74835499334?auto=format&fit=crop&w=900&q=80', 'Rias pengantin adat Jawa dengan tata rias dan sentuhan tradisional yang anggun.'
  UNION ALL SELECT 11, 'Paket Rias Modern Glam', 1100000, 5, 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=900&q=80', 'Rias modern glam untuk resepsi dengan pilihan tampilan yang dapat dikonsultasikan.'
  UNION ALL SELECT 12, 'Paket Rias Lengkap Resepsi', 1750000, 4, 'https://images.unsplash.com/photo-1537633552985-df8429e8048b?auto=format&fit=crop&w=900&q=80', 'Paket rias resepsi lengkap dengan persiapan wajah dan penyesuaian gaya busana.'
  UNION ALL SELECT 5, 'Kebaya Pengantin Adat Jawa', 2500000, 4, 'https://images.unsplash.com/photo-1743090286549-52f6d1983064?fm=jpg&q=60&w=1200&auto=format&fit=crop', 'Kebaya pengantin bernuansa klasik dengan detail bordir untuk acara akad dan resepsi.'
  UNION ALL SELECT 7, 'Kebaya Brokat Modern', 1850000, 5, 'https://images.unsplash.com/photo-1681714552617-fe3f4cf4be47?fm=jpg&q=60&w=1200&auto=format&fit=crop', 'Kebaya brokat modern dengan potongan anggun untuk momen pernikahan.'
  UNION ALL SELECT 13, 'Kebaya Akad Putih Premium', 3200000, 3, 'https://images.unsplash.com/photo-1696831443481-9fef47852c91?auto=format&fit=crop&w=900&q=80', 'Kebaya putih premium untuk akad, dirancang dengan detail halus dan siluet elegan.'
  UNION ALL SELECT 14, 'Kebaya Pengantin Adat Sunda', 2800000, 3, 'https://images.unsplash.com/photo-1632729331892-b318f62e4e72?auto=format&fit=crop&w=900&q=80', 'Kebaya pengantin adat Sunda dengan detail tradisional untuk akad dan resepsi.'
  UNION ALL SELECT 15, 'Kebaya Tulle Dusty Rose', 2200000, 4, 'https://images.unsplash.com/photo-1744367848789-bf6761d0fdde?auto=format&fit=crop&w=900&q=80', 'Kebaya tulle warna dusty rose dengan potongan modern dan detail renda halus.'
  UNION ALL SELECT 16, 'Gaun Pengantin Putih A-Line', 3500000, 3, 'https://images.unsplash.com/photo-1781269034633-7add54287c6c?auto=format&fit=crop&w=900&q=80', 'Gaun putih siluet A-line dengan tampilan klasik untuk upacara dan resepsi.'
  UNION ALL SELECT 17, 'Gaun Pengantin Muslimah Lace', 3900000, 3, 'https://images.unsplash.com/photo-1678830795557-d7886693c9ee?auto=format&fit=crop&w=900&q=80', 'Gaun pengantin muslimah dengan detail renda dan potongan yang santun.'
  UNION ALL SELECT 18, 'Gaun Resepsi Satin Ivory', 4200000, 2, 'https://images.unsplash.com/photo-1774660810310-5023390fe028?auto=format&fit=crop&w=900&q=80', 'Gaun satin warna ivory dengan tampilan bersih dan elegan untuk resepsi.'
  UNION ALL SELECT 19, 'Gaun Pengantin Adat Sunda', 3800000, 2, 'https://images.unsplash.com/photo-1782787231777-57f68be77d76?auto=format&fit=crop&w=900&q=80', 'Gaun pengantin dengan inspirasi adat Sunda untuk perayaan penuh makna.'
  UNION ALL SELECT 20, 'Gaun Pengantin Custom Bordir', 4800000, 2, 'https://images.unsplash.com/photo-1516290774656-b3a48f655bcd?auto=format&fit=crop&w=900&q=80', 'Gaun custom dengan detail bordir, dapat disesuaikan melalui konsultasi desain.'
  UNION ALL SELECT 21, 'Mahkota Pengantin Kristal', 650000, 8, 'https://images.unsplash.com/photo-1721206625649-b7fbe071c7b9?auto=format&fit=crop&w=900&q=80', 'Mahkota kristal sebagai aksesori kepala untuk melengkapi tampilan pengantin.'
  UNION ALL SELECT 22, 'Veil Pengantin Cathedral', 750000, 5, 'https://images.unsplash.com/photo-1753703986156-46e2b962c012?auto=format&fit=crop&w=900&q=80', 'Veil panjang bergaya cathedral untuk melengkapi gaun pengantin klasik.'
  UNION ALL SELECT 23, 'Hand Bouquet Mawar Putih', 350000, 12, 'https://images.unsplash.com/photo-1521543832500-49e69fb2bea2?auto=format&fit=crop&w=900&q=80', 'Hand bouquet mawar putih yang dirangkai untuk akad, foto, maupun resepsi.'
  UNION ALL SELECT 24, 'Cincin Pernikahan Emas Putih', 125000, 20, 'https://images.unsplash.com/photo-1515934751635-c81c6bc9a2d8?auto=format&fit=crop&w=900&q=80', 'Cincin pernikahan dengan tampilan emas putih untuk melengkapi koleksi aksesori pengantin.'
  UNION ALL SELECT 25, 'Hiasan Sanggul Melati', 275000, 10, 'https://images.unsplash.com/photo-1783255166225-6890aee7381e?auto=format&fit=crop&w=900&q=80', 'Hiasan sanggul bernuansa melati untuk melengkapi tata rias pengantin tradisional.'
) AS wedding ON wedding.`id` = p.`id`
SET p.`nama` = wedding.`nama`,
    p.`harga` = wedding.`harga`,
    p.`stok` = wedding.`stok`,
    p.`poto` = wedding.`poto`,
    p.`deskripsi` = wedding.`deskripsi`;

COMMIT;

SELECT k.`nama_kategori`, COUNT(p.`id`) AS `jumlah_produk`
FROM `tb_kategori` AS k
LEFT JOIN `tb_produk` AS p ON p.`id_kategori` = k.`id_kategori`
WHERE k.`id_kategori` IN (1, 2, 4, 5)
GROUP BY k.`id_kategori`, k.`nama_kategori`
ORDER BY k.`nama_kategori`;

SELECT COUNT(*) AS `total_produk`, COUNT(DISTINCT `poto`) AS `foto_unik`
FROM `tb_produk`
WHERE `id` BETWEEN 2 AND 25;