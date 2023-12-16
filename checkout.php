<?php
session_start();
include 'config.php';

if (!isset($_SESSION['userid'])) {
  header('Location: index.php');
}

// Implement the checkout logic, e.g., update the database, create an order, etc.
if (isset($_POST['checkout']) && isset($_SESSION['cart'])) {
  $total = 0;
  if (count($_SESSION['cart']) > 0) {
    for ($i = 0; $i < count($_SESSION['cart']); $i++) {
      // COLLECT EVERYTHING
      $transID = "TRS" . date("dmyHis");
      $transDate = date("d-m-Y");

      $product_id = $_SESSION['cart'][$i]['id'];
      $quantity = $_SESSION['cart'][$i]['quantity'];
      $nama_produk = $_SESSION['cart'][$i]['name'];
      $price = $_SESSION['cart'][$i]['price'];
      $user_id = $_POST['user_id'];
      $payment_method = $_POST['payment_method'];
      $subtotal = $quantity * $price;
      $total += $subtotal;

      $stok = "SELECT * FROM product WHERE id='$product_id'";
      $stok = mysqli_query($conn, $stok);
      $finalStok = 0;
      if (mysqli_num_rows($stok) > 0) {
        while ($row = mysqli_fetch_assoc($stok)) {
          $finalStok = $row['stok'] - $quantity;
        }
      }
      $products = "UPDATE product SET stok=$finalStok WHERE id='$product_id'";
      if (mysqli_query($conn, $products)) {
        // SUCCESS
      } else {
        // FAILED
      }

      $transaction_details = "INSERT INTO transaction_details (id, transactions_id, product_id, quantity, unit_price, subtotal) VALUES (null, '$transID', $product_id, $quantity, $price, $subtotal)";
      if (mysqli_query($conn, $transaction_details)) {
        // SUCCESS
      } else {
        // FAILED
      }
    }
  } else {
    header('Location: index.php');
  }
  $transactions = "INSERT INTO transactions (id, timestamp, user_id, total_amount, payment_method, status_id) VALUES ('$transID', '$transDate', $user_id, $total, '$payment_method', 1)";
  if (mysqli_query($conn, $transactions)) {
    // SUCCESS

    // Clear the cart after checkout
    unset($_SESSION['cart']);
    // Create session transaction_status for alert
    $_SESSION['transaction_status'] = 1;

    // Redirect to home
    header("Location: index.php");
  } else {
    // FAILED
  }
} else {
  header('Location: index.php');
}
exit();
