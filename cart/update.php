<?php
require_once __DIR__ . '/../config/database.php';

use App\AuthService;
use App\ProductService;

AuthService::requireAuth();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['product_id'], $_POST['quantity'])) {
    $productId = (int)$_POST['product_id'];
    $quantity = max(1, (int)$_POST['quantity']);
    $itemSubtotal = 0;
    $updatedQuantity = $quantity;

    $product = ProductService::getById($productId);
    if ($product && isset($_SESSION['cart'])) {
        $quantity = min($quantity, (int)$product['stok']);
        $updatedQuantity = $quantity;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] === $productId) {
                $item['quantity'] = $quantity;
                $itemSubtotal = $quantity * $item['price'];
                break;
            }
        }
    }

    $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
              || isset($_POST['ajax']) || isset($_GET['ajax']);

    if ($isAjax) {
        $cartTotal = 0;
        $cartCount = 0;
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $it) {
                $cartTotal += $it['quantity'] * $it['price'];
                $cartCount += $it['quantity'];
            }
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'productId' => $productId,
            'quantity' => $updatedQuantity,
            'itemSubtotal' => format_rupiah($itemSubtotal),
            'cartTotal' => format_rupiah($cartTotal),
            'cartCount' => $cartCount
        ]);
        exit();
    }

    $redirect = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . 'index.php';
    header("Location: {$redirect}");
    exit();
}

redirect('index.php');
