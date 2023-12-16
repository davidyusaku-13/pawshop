-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 16, 2023 at 07:12 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pawshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Makanan Kucing'),
(2, 'Peralatan Kucing'),
(3, 'Kesehatan Kucing'),
(4, 'Mainan Kucing');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `gambar` varchar(40) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `stok` int(11) NOT NULL,
  `harga` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `gambar`, `nama_produk`, `category_id`, `stok`, `harga`) VALUES
(6, 'bolt.jpg', 'Makanan Kucing Bolt Pakan Kucing Bolt Catfood', 1, 56, 9500),
(7, 'fish-oil.jpg', 'MINYAK IKAN FISH OIL VITAMIN OMEGA BULU KESEHATAN KUCING', 3, 45, 9000),
(8, 'liebao.jpg', 'Snack Kucing Kitten Adult Hewan Peliharaan 15g/Strip Liebao', 1, 77, 850),
(9, 'tongkat-mainan.jpg', 'Tongkat Bulu Mainan Kucing Interaktif Lonceng 14mm', 4, 47, 3000),
(10, 'wormectin.jpg', 'WORMECTIN 2ML - Obat Hewan Anti Jamur Cacing Parasit Kucing', 3, 32, 9500);

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `id` int(11) NOT NULL,
  `name` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status`
--

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
(12, 'Ditunda');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` varchar(15) NOT NULL,
  `timestamp` varchar(10) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` int(11) NOT NULL,
  `payment_method` varchar(40) NOT NULL,
  `status_id` int(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `timestamp`, `user_id`, `total_amount`, `payment_method`, `status_id`) VALUES
('TRS141223140150', '14-12-2023', 2, 31567, 'Transfer', 1),
('TRS141223140920', '14-12-2023', 2, 2969, 'Tunai', 1),
('TRS141223145043', '14-12-2023', 2, 790, 'Transfer', 7);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_details`
--

CREATE TABLE `transaction_details` (
  `id` int(11) NOT NULL,
  `transactions_id` varchar(15) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_details`
--

INSERT INTO `transaction_details` (`id`, `transactions_id`, `product_id`, `quantity`, `unit_price`, `subtotal`) VALUES
(14, 'TRS141223140113', 1, 5, 790, 3950),
(15, 'TRS141223140113', 3, 1, 8819, 8819),
(16, 'TRS141223140113', 4, 2, 9399, 18798),
(17, 'TRS141223140150', 1, 5, 790, 3950),
(18, 'TRS141223140150', 3, 1, 8819, 8819),
(19, 'TRS141223140150', 4, 2, 9399, 18798),
(20, 'TRS141223140920', 2, 1, 2969, 2969),
(21, 'TRS141223145043', 1, 1, 790, 790);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(40) NOT NULL,
  `password` char(32) NOT NULL,
  `privilege` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `privilege`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin'),
(2, 'david', '172522ec1028ab781d9dfd17eaca4427', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `transaction_details`
--
ALTER TABLE `transaction_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
