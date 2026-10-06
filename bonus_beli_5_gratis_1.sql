-- Promo: setiap total 5 unit barang dalam satu transaksi mendapat 1 unit gratis.
-- Jalankan sekali pada database db_toko yang sudah ada.

ALTER TABLE tb_detail
    ADD COLUMN IF NOT EXISTS jumlah_bonus INT NOT NULL DEFAULT 0 AFTER jumlah;

DROP TRIGGER IF EXISTS trg_detail_bonus_before_insert;
DROP TRIGGER IF EXISTS trg_detail_bonus_after_insert;

DELIMITER $$

CREATE TRIGGER trg_detail_bonus_before_insert
BEFORE INSERT ON tb_detail
FOR EACH ROW
BEGIN
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
END$$

CREATE TRIGGER trg_detail_bonus_after_insert
AFTER INSERT ON tb_detail
FOR EACH ROW
BEGIN
    UPDATE tb_produk
    SET stok = stok - NEW.jumlah - NEW.jumlah_bonus
    WHERE id = NEW.id_produk
      AND stok >= NEW.jumlah + NEW.jumlah_bonus;

    IF ROW_COUNT() = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Stok tidak cukup untuk pembelian dan bonus';
    END IF;
END$$

DELIMITER ;