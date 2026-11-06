<?php
require_once __DIR__ . '/../config/database.php';

use App\AuthService;
use App\OrderService;

AuthService::requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $cartItems = $_SESSION['cart'] ?? [];
    if (empty($cartItems)) {
        flash('warning', 'Keranjang belanja Anda masih kosong.');
        redirect('index.php');
    }

    $userId = AuthService::getCurrentUserId();
    $paymentMethod = sanitize($_POST['payment_method'] ?? 'COD');
    $error = null;

    $orderId = OrderService::createOrder($userId, $cartItems, $paymentMethod, $error);

    if ($orderId) {
        unset($_SESSION['cart']);
        flash('success', "Pesanan #{$orderId} berhasil dibuat! Silakan lakukan pembayaran.");
        redirect('customer/transactions.php');
    } else {
        flash('danger', $error ?: 'Gagal memproses pesanan. Silakan coba kembali.');
        redirect('index.php');
    }
}

redirect('index.php');
