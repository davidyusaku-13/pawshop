<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['product_id'])) {
  $product_id = (int)$_GET['product_id'];

  if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $key => $item) {
      if ($item['id'] == $product_id) {
        unset($_SESSION['cart'][$key]);
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
