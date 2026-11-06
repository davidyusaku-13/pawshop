-- Pawshop Database Schema and Initial Seed Data

CREATE DATABASE IF NOT EXISTS `pawshop` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `pawshop`;

-- --------------------------------------------------------
-- Table structure for table `kategori`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `kategori` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `kategori` (`id`, `name`) VALUES
(1, 'Makanan Kucing'),
(2, 'Peralatan Kucing'),
(3, 'Kesehatan Kucing'),
(4, 'Mainan Kucing')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- --------------------------------------------------------
-- Table structure for table `produk`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `produk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gambar` varchar(255) NOT NULL DEFAULT 'x.jpg',
  `nama_produk` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `harga` int(11) NOT NULL DEFAULT 0,
  `detail` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `produk` (`id`, `gambar`, `nama_produk`, `category_id`, `stok`, `harga`, `detail`) VALUES
(6, 'bolt.jpg', 'Makanan Kucing Bolt Pakan Kucing Bolt Catfood', 1, 56, 9500, 'Makanan kucing Bolt bernutrisi lengkap dan seimbang.'),
(7, 'fish-oil.jpg', 'MINYAK IKAN FISH OIL VITAMIN OMEGA BULU KESEHATAN KUCING', 3, 45, 9000, 'Minyak ikan kaya omega 3 & 6 untuk bulu dan kesehatan kucing.'),
(8, 'liebao.jpg', 'Snack Kucing Kitten Adult Hewan Peliharaan 15g/Strip Liebao', 1, 77, 850, 'Camilan basah lezat bernutrisi untuk kucing kesayangan.'),
(9, 'tongkat-mainan.jpg', 'Tongkat Bulu Mainan Kucing Interaktif Lonceng 14mm', 4, 47, 3000, 'Mainan tongkat bulu interaktif dengan lonceng.'),
(10, 'wormectin.jpg', 'WORMECTIN 2ML - Obat Hewan Anti Jamur Cacing Parasit Kucing', 3, 32, 9500, 'Obat anti parasit, jamur, dan cacing untuk kucing.')
ON DUPLICATE KEY UPDATE `nama_produk`=VALUES(`nama_produk`);

-- --------------------------------------------------------
-- Table structure for table `status`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `status` (`id`, `name`) VALUES
(1, 'Menunggu Pembayaran'),
(2, 'Diproses Penjual'),
(3, 'Dikirim'),
(4, 'Diterima Pembeli'),
(5, 'Dalam Pengembalian Dana'),
(6, 'Dibatalkan'),
(7, 'Selesai'),
(8, 'Menunggu Konfirmasi Pembeli'),
(9, 'Dalam Proses Pengiriman'),
(10, 'Pesanan Tidak Dapat Diproses'),
(11, 'Menunggu Konfirmasi'),
(12, 'Ditunda')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(30) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT '',
  `privilege` varchar(20) NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Passwords:
-- admin: admin (md5: 21232f297a57a5a743894a0e4a801fc3)
-- david: david (md5: 172522ec1028ab781d9dfd17eaca4427)
INSERT INTO `users` (`id`, `username`, `password`, `phone_number`, `remember_token`, `privilege`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', '081234567890', '', 'admin'),
(2, 'david', '172522ec1028ab781d9dfd17eaca4427', '081234567891', '', 'user')
ON DUPLICATE KEY UPDATE `username`=VALUES(`username`);

-- --------------------------------------------------------
-- Table structure for table `transaksi`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `transaksi` (
  `id` varchar(25) NOT NULL,
  `timestamp` varchar(30) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `status_id` int(11) NOT NULL,
  `expiry_date` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status_id` (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `transaksi` (`id`, `timestamp`, `user_id`, `total_amount`, `payment_method`, `bukti_pembayaran`, `status_id`, `expiry_date`) VALUES
('TRS141223140150', '14-12-2023', 2, 31567, 'Transfer', NULL, 1, 1702598400),
('TRS141223140920', '14-12-2023', 2, 2969, 'Tunai', NULL, 1, 1702598400),
('TRS141223145043', '14-12-2023', 2, 790, 'Transfer', 'bolt.jpg', 7, 1702598400)
ON DUPLICATE KEY UPDATE `total_amount`=VALUES(`total_amount`);

-- --------------------------------------------------------
-- Table structure for table `transaksi_detail`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `transaksi_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transactions_id` varchar(25) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `transactions_id` (`transactions_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `transaksi_detail` (`id`, `transactions_id`, `product_id`, `quantity`, `unit_price`, `subtotal`) VALUES
(1, 'TRS141223140150', 6, 2, 9500, 19000),
(2, 'TRS141223140150', 7, 1, 9000, 9000),
(3, 'TRS141223140150', 9, 1, 3000, 3000),
(4, 'TRS141223140920', 8, 3, 850, 2550),
(5, 'TRS141223145043', 8, 1, 850, 850)
ON DUPLICATE KEY UPDATE `subtotal`=VALUES(`subtotal`);

-- --------------------------------------------------------
-- Backward-compatibility Views for older English table queries
-- --------------------------------------------------------

CREATE OR REPLACE VIEW `product` AS SELECT * FROM `produk`;
CREATE OR REPLACE VIEW `categories` AS SELECT * FROM `kategori`;
CREATE OR REPLACE VIEW `transaction_details` AS SELECT * FROM `transaksi_detail`;
CREATE OR REPLACE VIEW `transactions` AS SELECT * FROM `transaksi`;
