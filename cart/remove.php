<?php
require_once __DIR__ . '/../config/database.php';

if (isset($_GET['product_id']) && isset($_SESSION['cart'])) {
    $productId = (int)$_GET['product_id'];

    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] === $productId) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    // Re-index array keys
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

$redirect = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . 'index.php';
header("Location: {$redirect}");
exit();
