<?php
include 'config.php';

// Require login
requireLogin();

// Validate CSRF token (from query string for GET request)
$csrf_token = get('csrf_token');
if (!csrfValidate($csrf_token)) {
    header('Location: index.php');
    exit;
}

$product_id = getPositiveInt('product_id');

if ($product_id > 0 && isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $product_id) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    // Re-index array
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit;
