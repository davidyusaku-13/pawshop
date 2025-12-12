<?php
include 'config.php';

// Require login
if (!isLoggedIn()) {
    echo "<script>alert('Anda harus login untuk menambahkan keranjang!');location.href = 'login.php';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate CSRF token
    if (!csrfValidate()) {
        $_SESSION['cart_error'] = 'Sesi tidak valid';
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
        exit;
    }

    $product_id = postPositiveInt('product_id');
    $quantity = postPositiveInt('quantity', 1);

    if ($product_id <= 0 || $quantity <= 0) {
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
        exit;
    }

    // Verify product exists and has stock
    $product = dbFetchOne(
        "SELECT id, nama_produk, harga, stok FROM produk WHERE id = ?",
        'i',
        [$product_id]
    );

    if (!$product || $product['stok'] < $quantity) {
        $_SESSION['cart_error'] = 'Stok tidak mencukupi';
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
        exit;
    }

    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if product already in cart
    $item_exists = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product_id) {
            // Check if total quantity doesn't exceed stock
            $newQty = $item['quantity'] + $quantity;
            if ($newQty > $product['stok']) {
                $newQty = $product['stok'];
            }
            $item['quantity'] = $newQty;
            $item_exists = true;
            $_SESSION['cart_status'] = 1;
            break;
        }
    }
    unset($item);

    // Add new item if not exists
    if (!$item_exists) {
        $_SESSION['cart'][] = [
            'id' => $product_id,
            'quantity' => min($quantity, $product['stok']),
            'name' => $product['nama_produk'],
            'price' => $product['harga']
        ];
        $_SESSION['cart_status'] = 1;
    }

    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
    exit;
}

header("Location: index.php");
exit;
