<?php
session_start();

if (!isset($_SESSION['userid'])) {
  header('Location: index.php');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Update quantity in the cart
    foreach ($_SESSION['cart'] as &$item) {
      if ($item['id'] == $product_id) {
        $item['quantity'] = $quantity;
        break;
      }
    }

    // Redirect back to the cart page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
  }
}
