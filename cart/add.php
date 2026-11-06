<?php
require_once __DIR__ . '/../config/database.php';

if (!isset($userid) || empty($userid)) {
  echo "<script>alert('Anda harus login untuk menambahkan keranjang!');location.href = '" . BASE_URL . "auth/login.php';</script>";
  exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = max(1, (int)$_POST['quantity']);

    if (!isset($_SESSION['cart'])) {
      $_SESSION['cart'] = array();
    }

    $item_exists = false;
    foreach ($_SESSION['cart'] as &$item) {
      if ($item['id'] == $product_id) {
        $item['quantity'] += $quantity;
        $item_exists = true;
        $_SESSION['cart_status'] = 1;
        break;
      }
    }

    if (!$item_exists) {
      $_SESSION['cart'][] = array(
        'id' => $product_id,
        'quantity' => $quantity,
        'name' => $_POST['nama_produk'] ?? '',
        'price' => (int)($_POST['harga'] ?? 0),
      );
      $_SESSION['cart_status'] = 1;
    }

    $redirect = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . 'index.php';
    header("Location: " . $redirect);
    exit();
  }
}

header("Location: " . BASE_URL . "index.php");
exit();
