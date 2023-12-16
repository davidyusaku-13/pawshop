<?php
session_start();

if (!isset($_SESSION['userid'])) {
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

      // Add item to cart
      $_SESSION['cart'][] = array(
        'id' => $product_id,
        'quantity' => $quantity,
        'name' => $_POST['nama_produk'],
        'price' => $_POST['harga'],
        // Add other necessary fields, if needed
      );

      // Redirect back to the previous page
      header("Location: " . $_SERVER['HTTP_REFERER']);
      exit();
    }
  }
}
