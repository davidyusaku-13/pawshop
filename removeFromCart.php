<?php
session_start();

if (!isset($_SESSION)) {
  header('Location: index.php');
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
  if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Remove item from the cart
    foreach ($_SESSION['cart'] as $key => $item) {
      if ($item['id'] == $product_id) {
        unset($_SESSION['cart'][$key]);
        break;
      }
    }

    // Redirect back to the cart page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
  }
}
