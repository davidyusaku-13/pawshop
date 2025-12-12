<?php
include 'config.php';

// Require login
requireLogin();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate CSRF token
    if (!csrfValidate()) {
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
        exit;
    }

    $product_id = postPositiveInt('product_id');
    $quantity = postPositiveInt('quantity', 1);

    if ($product_id > 0 && $quantity > 0 && isset($_SESSION['cart'])) {
        // Verify stock availability
        $product = dbFetchOne(
            "SELECT stok FROM produk WHERE id = ?",
            'i',
            [$product_id]
        );

        $maxStock = $product['stok'] ?? 1;

        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $product_id) {
                $item['quantity'] = min($quantity, $maxStock);
                break;
            }
        }
        unset($item);
    }

    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
    exit;
}

header("Location: index.php");
exit;
