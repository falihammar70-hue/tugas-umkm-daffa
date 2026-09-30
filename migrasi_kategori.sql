USE `db_toko`;

CREATE TABLE IF NOT EXISTS `tb_kategori` (
  `id_kategori` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id_kategori`),
  UNIQUE KEY `uq_kategori_nama` (`nama_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `tb_kategori` (`nama_kategori`)
SELECT DISTINCT CASE WHEN TRIM(`kategori`) = '' THEN 'Umum' ELSE TRIM(`kategori`) END
FROM `tb_produk`;

ALTER TABLE `tb_produk` ADD COLUMN `id_kategori` INT(11) NULL AFTER `poto`;

UPDATE `tb_produk` AS p
JOIN `tb_kategori` AS k
  ON k.`nama_kategori` = CASE WHEN TRIM(p.`kategori`) = '' THEN 'Umum' ELSE TRIM(p.`kategori`) END
SET p.`id_kategori` = k.`id_kategori`;

ALTER TABLE `tb_produk`
  MODIFY `id_kategori` INT(11) NOT NULL,
  ADD KEY `fk_produk_kategori` (`id_kategori`),
  ADD CONSTRAINT `fk_produk_kategori` FOREIGN KEY (`id_kategori`)
    REFERENCES `tb_kategori` (`id_kategori`) ON DELETE RESTRICT ON UPDATE CASCADE,
  DROP COLUMN `kategori`;