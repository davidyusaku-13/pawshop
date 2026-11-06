<?php

namespace App;

use Throwable;

class OrderService
{
    /**
     * Atomically create an order with pessimistic locking and rollback.
     */
    public static function createOrder(int $userId, array $cartItems, string $paymentMethod, ?string &$error = null): ?string
    {
        if (empty($cartItems)) {
            $error = "Keranjang belanja kosong.";
            return null;
        }

        Database::beginTransaction();

        try {
            $totalAmount = 0;
            $itemsToProcess = [];

            foreach ($cartItems as $item) {
                $productId = (int)$item['id'];
                $quantity = max(1, (int)$item['quantity']);

                // Fetch product with row-level lock
                $product = Database::fetchOne(
                    "SELECT id, nama_produk, harga, stok FROM produk WHERE id = ? FOR UPDATE",
                    [$productId]
                );

                if (!$product) {
                    throw new \Exception("Produk ID #{$productId} tidak ditemukan.");
                }

                if ($product['stok'] < $quantity) {
                    throw new \Exception("Stok tidak mencukupi untuk '{$product['nama_produk']}' (Tersedia: {$product['stok']}).");
                }

                $price = (int)$product['harga'];
                $subtotal = $quantity * $price;
                $totalAmount += $subtotal;

                $itemsToProcess[] = [
                    'product_id' => $productId,
                    'quantity'   => $quantity,
                    'price'      => $price,
                    'subtotal'   => $subtotal,
                    'new_stok'   => $product['stok'] - $quantity,
                ];
            }

            // Deduct stock for all items
            foreach ($itemsToProcess as $pItem) {
                Database::execute(
                    "UPDATE produk SET stok = ? WHERE id = ?",
                    [$pItem['new_stok'], $pItem['product_id']]
                );
            }

            // Generate unique transaction ID
            $transId = "TRS" . date("dmyHis") . rand(10, 99);
            $timestamp = date("d-m-Y H:i");
            // Expiry date: 24 hours payment deadline (Unix timestamp)
            $expiryDate = time() + 86400;

            // Insert parent order (status 1 = Menunggu Pembayaran)
            Database::execute(
                "INSERT INTO transaksi (id, timestamp, user_id, total_amount, payment_method, status_id, expiry_date) VALUES (?, ?, ?, ?, ?, 1, ?)",
                [$transId, $timestamp, $userId, $totalAmount, $paymentMethod, $expiryDate]
            );

            // Insert line items
            foreach ($itemsToProcess as $pItem) {
                Database::execute(
                    "INSERT INTO transaksi_detail (transactions_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)",
                    [$transId, $pItem['product_id'], $pItem['quantity'], $pItem['price'], $pItem['subtotal']]
                );
            }

            Database::commit();
            return $transId;

        } catch (Throwable $e) {
            Database::rollBack();
            $error = $e->getMessage();
            return null;
        }
    }

    public static function getOrderById(string $orderId): ?array
    {
        return Database::fetchOne(
            "SELECT t.*, u.username, u.phone_number, s.name AS status_name 
             FROM transaksi t 
             JOIN users u ON t.user_id = u.id 
             JOIN status s ON t.status_id = s.id 
             WHERE t.id = ?",
            [$orderId]
        );
    }

    public static function getOrderDetails(string $orderId): array
    {
        return Database::query(
            "SELECT td.*, p.nama_produk, p.gambar 
             FROM transaksi_detail td 
             JOIN produk p ON td.product_id = p.id 
             WHERE td.transactions_id = ?",
            [$orderId]
        );
    }

    public static function getOrdersByUser(int $userId): array
    {
        return Database::query(
            "SELECT t.*, s.name AS status_name 
             FROM transaksi t 
             JOIN status s ON t.status_id = s.id 
             WHERE t.user_id = ? 
             ORDER BY t.id DESC",
            [$userId]
        );
    }

    public static function getAllOrders(): array
    {
        return Database::query(
            "SELECT t.*, u.username, s.name AS status_name 
             FROM transaksi t 
             JOIN users u ON t.user_id = u.id 
             JOIN status s ON t.status_id = s.id 
             ORDER BY t.id DESC"
        );
    }

    public static function updateStatus(string $orderId, int $statusId): bool
    {
        return Database::execute(
            "UPDATE transaksi SET status_id = ? WHERE id = ?",
            [$statusId, $orderId]
        );
    }

    public static function uploadPaymentProof(string $orderId, int $userId, array $file, ?string &$error = null): bool
    {
        $order = self::getOrderById($orderId);
        if (!$order || (int)$order['user_id'] !== $userId) {
            $error = "Transaksi tidak valid atau tidak memiliki akses.";
            return false;
        }

        $uploaded = FileUploader::uploadImage($file, $order['bukti_pembayaran'] ?? null, $error);
        if (!$uploaded) {
            return false;
        }

        // Status 3 = Dikirim / Menunggu Konfirmasi Bukti
        return Database::execute(
            "UPDATE transaksi SET status_id = 3, bukti_pembayaran = ? WHERE id = ? AND user_id = ?",
            [$uploaded, $orderId, $userId]
        );
    }

    public static function deleteOrder(string $orderId): bool
    {
        Database::beginTransaction();
        try {
            $order = self::getOrderById($orderId);
            if ($order && !empty($order['bukti_pembayaran'])) {
                FileUploader::delete($order['bukti_pembayaran']);
            }
            Database::execute("DELETE FROM transaksi_detail WHERE transactions_id = ?", [$orderId]);
            Database::execute("DELETE FROM transaksi WHERE id = ?", [$orderId]);
            Database::commit();
            return true;
        } catch (Throwable $e) {
            Database::rollBack();
            return false;
        }
    }

    // STATUS CRUD
    public static function getAllStatuses(): array
    {
        return Database::query("SELECT * FROM status ORDER BY id ASC");
    }

    public static function getStatusById(int $id): ?array
    {
        return Database::fetchOne("SELECT * FROM status WHERE id = ?", [$id]);
    }

    public static function createStatus(string $name): bool
    {
        return Database::execute("INSERT INTO status (name) VALUES (?)", [trim($name)]);
    }

    public static function updateStatusName(int $id, string $name): bool
    {
        return Database::execute("UPDATE status SET name = ? WHERE id = ?", [trim($name), $id]);
    }

    public static function deleteStatus(int $id): bool
    {
        return Database::execute("DELETE FROM status WHERE id = ?", [$id]);
    }

    // ANALYTICS & DASHBOARD METRICS
    public static function getDailyIncome(): float
    {
        $today = date("d-m-Y");
        $row = Database::fetchOne(
            "SELECT COALESCE(SUM(total_amount), 0) AS total FROM transaksi WHERE timestamp = ?",
            [$today]
        );
        return (float)($row['total'] ?? 0);
    }

    public static function getMonthlyIncome(): float
    {
        $thisMonth = date("-m-Y");
        $row = Database::fetchOne(
            "SELECT COALESCE(SUM(total_amount), 0) AS total FROM transaksi WHERE timestamp LIKE ?",
            ["%{$thisMonth}"]
        );
        return (float)($row['total'] ?? 0);
    }

    public static function getCategorySalesShare(): array
    {
        $sql = "SELECT k.id, k.name, COUNT(td.id) AS sales_count 
                FROM kategori k 
                LEFT JOIN produk p ON k.id = p.category_id 
                LEFT JOIN transaksi_detail td ON p.id = td.product_id 
                GROUP BY k.id, k.name 
                ORDER BY sales_count DESC";
        $categories = Database::query($sql);
        $totalSales = array_sum(array_column($categories, 'sales_count'));

        foreach ($categories as &$cat) {
            $cat['percentage'] = $totalSales > 0 ? round(($cat['sales_count'] / $totalSales) * 100) : 0;
        }

        return $categories;
    }
}
