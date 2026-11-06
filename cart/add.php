<?php
require_once __DIR__ . '/../config/database.php';

use App\AuthService;
use App\ProductService;

if (!AuthService::isLoggedIn()) {
    flash('warning', 'Silakan masuk terlebih dahulu untuk berbelanja.');
    redirect('auth/login');
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['product_id'])) {
    $productId = (int)$_POST['product_id'];
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));

    $product = ProductService::getById($productId);
    if (!$product) {
        flash('danger', 'Produk tidak ditemukan.');
        redirect('');
    }

    if ($product['stok'] < $quantity) {
        flash('warning', "Stok {$product['nama_produk']} hanya tersisa {$product['stok']}.");
        $redirect = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL;
        header("Location: {$redirect}");
        exit();
    }

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $itemExists = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] === $productId) {
            $newQty = $item['quantity'] + $quantity;
            if ($newQty > $product['stok']) {
                flash('warning', "Jumlah di keranjang melebihi stok yang tersedia ({$product['stok']}).");
                $itemExists = true;
                break;
            }
            $item['quantity'] = $newQty;
            $itemExists = true;
            $_SESSION['cart_status'] = 1;
            break;
        }
    }

    if (!$itemExists) {
        $_SESSION['cart'][] = [
            'id'       => $productId,
            'quantity' => $quantity,
            'name'     => $product['nama_produk'],
            'price'    => (int)$product['harga'],
        ];
        $_SESSION['cart_status'] = 1;
    }

    $redirect = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL;
    header("Location: {$redirect}");
    exit();
}

redirect('');
