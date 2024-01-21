<?php
session_start();
include('config.php');

if (!isset($userid) || $userid == '') {
  header('Location: login.php');
} else {
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
      $product_id = $_POST['product_id'];
      $quantity = $_POST['quantity'];

      // Initialize cart if not already exists
      if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
      }

      // Check if the product is already in the cart
      $item_exists = false;
      foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product_id) {
          // Product already in cart, update quantity
          $item['quantity'] += $quantity;
          $item_exists = true;
          $_SESSION['cart_status'] = 1;
          break;
        }
      }

      // If the product is not in the cart, add it
      if (!$item_exists) {
        $_SESSION['cart'][] = array(
          'id' => $product_id,
          'quantity' => $quantity,
          'name' => $_POST['nama_produk'],
          'price' => $_POST['harga'],

          // Add other necessary fields, if needed
        );
        $_SESSION['cart_status'] = 1;
      }

      // Redirect back to the previous page
      header("Location: " . $_SERVER['HTTP_REFERER']);
      exit();
    }
  }
}
