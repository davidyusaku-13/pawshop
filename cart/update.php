<?php
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['userid'])) {
  header('Location: ' . BASE_URL . 'auth/login.php');
  exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['product_id']) && isset($_POST['quantity'])) {
  $product_id = (int)$_POST['product_id'];
  $quantity = max(1, (int)$_POST['quantity']);

  if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as &$item) {
      if ($item['id'] == $product_id) {
        $item['quantity'] = $quantity;
        break;
      }
    }
  }

  $redirect = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . 'index.php';
  header("Location: " . $redirect);
  exit();
}

header("Location: " . BASE_URL . "index.php");
exit();
