<?php
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['userid'])) {
  header('Location: ' . BASE_URL . 'auth/login.php');
  exit();
}

if (isset($_POST['checkout']) && isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
  $total = 0;
  $transID = "TRS" . date("dmyHis");
  $transDate = date("d-m-Y");
  $user_id = (int)$_POST['user_id'];
  $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
  $expiry_date = (int)date("dmyHis") + 20000000000;

  for ($i = 0; $i < count($_SESSION['cart']); $i++) {
    $product_id = (int)$_SESSION['cart'][$i]['id'];
    $quantity = (int)$_SESSION['cart'][$i]['quantity'];
    $price = (int)$_SESSION['cart'][$i]['price'];
    $subtotal = $quantity * $price;
    $total += $subtotal;

    // Deduct stock
    $stokRes = mysqli_query($conn, "SELECT stok FROM produk WHERE id='$product_id'");
    $finalStok = 0;
    if ($stokRes && mysqli_num_rows($stokRes) > 0) {
      $stokRow = mysqli_fetch_assoc($stokRes);
      $finalStok = max(0, $stokRow['stok'] - $quantity);
    }
    mysqli_query($conn, "UPDATE produk SET stok=$finalStok WHERE id='$product_id'");

    // Insert detail
    mysqli_query($conn, "INSERT INTO transaksi_detail (id, transactions_id, product_id, quantity, unit_price, subtotal) VALUES (null, '$transID', $product_id, $quantity, $price, $subtotal)");
  }

  $transactions = "INSERT INTO transaksi (id, timestamp, user_id, total_amount, payment_method, status_id, expiry_date) VALUES ('$transID', '$transDate', $user_id, $total, '$payment_method', 1, $expiry_date)";
  if (mysqli_query($conn, $transactions)) {
    unset($_SESSION['cart']);
    $_SESSION['transaction_status'] = 1;
    header("Location: " . BASE_URL . "customer/transactions.php");
    exit();
  }
}

header('Location: ' . BASE_URL . 'index.php');
exit();
