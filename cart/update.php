<?php
require_once __DIR__ . '/../config/database.php';

use App\AuthService;
use App\ProductService;

AuthService::requireAuth();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['product_id'], $_POST['quantity'])) {
    $productId = (int)$_POST['product_id'];
    $quantity = max(1, (int)$_POST['quantity']);

    $product = ProductService::getById($productId);
    if ($product && isset($_SESSION['cart'])) {
        $quantity = min($quantity, (int)$product['stok']);
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] === $productId) {
                $item['quantity'] = $quantity;
                break;
            }
        }
    }

    $redirect = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . 'index.php';
    header("Location: {$redirect}");
    exit();
}

redirect('index.php');
