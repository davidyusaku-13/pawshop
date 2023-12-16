<?php
session_start();
include "config.php"; // Assuming you have a file for database configuration

if (!isset($_SESSION['idsession']) && !isset($_SESSION['usersession'])) {
  header('Location: login.php');
} else {
  $idsession = $_SESSION['idsession'];
}

if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = array();
}

if (isset($_POST['submit'])) {
  $product_id = $_POST['product_id'];
  $quantity = $_POST['quantity'];

  // Check if the product is already in the cart
  if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] += $quantity;
  } else {
    // If not, add it to the cart
    $_SESSION['cart'][$product_id] = $quantity;
  }
}

if ($_SESSION['usersession'] != 'admin') {
  $customer = "w3-hide";
  $admin = "";
} else {
  $customer = "";
  $admin = "w3-hide";
}

// Check if the clearCart parameter is set in the POST request
if (isset($_POST['clearCart']) && $_POST['clearCart'] == true) {
  // Clear the session variable
  $_SESSION['cart'] = array();
}

// Check if the Checkout button is pressed
if (isset($_POST['checkout'])) {
  // Process checkout logic here

  // Create TRANSACTIONS
  $id_transaction = "TRS" . date("ymdHis");
  $date_transaction = date("d-m-Y");
  $total_amount = $_POST['total_amount'];
  $payment_method = $_POST['payment_method'];
  $transactions = "INSERT INTO `transactions` (`id`, `timestamp`, `user_id`, `total_amount`, `payment_method`) VALUES ('$id_transaction', '$date_transaction', '$idsession', '$total_amount', '$payment_method')";

  $product_id = array();
  $quantity = array();
  foreach ($_SESSION['cart'] as $key => $value) {
    $product_id[] = $key;
    $quantity[] = $value;
  }
  $c = 0;
  for ($i = 0; $i < count($product_id); $i++) {
    $fetch = "SELECT * FROM product WHERE id='$product_id[$c]'";
    $res = mysqli_fetch_assoc(mysqli_query($conn, $fetch));

    // Update PRODUCT
    $fetchStok = $res['stok'];
    $finalStok = $fetchStok - $quantity[$c];
    $product = "UPDATE product SET stok='$finalStok' WHERE id='$product_id[$c]'";

    // Create TRANSACTION_DETAILS
    $price = $res['harga'];
    $subtotal = $price * $quantity[$c];
    $transaction_details = "INSERT INTO `transaction_details` (`id`, `transaction_id`, `product_id`, `quantity`, `unit_price`, `subtotal`) VALUES (NULL, '$id_transaction', '$product_id[$c]', '$quantity[$c]', '$price', '$subtotal')";

    if (mysqli_query($conn, $product) && mysqli_query($conn, $transaction_details)) {
      echo "Record updated successfully";
    }
    $c++;
  }

  if (mysqli_query($conn, $transactions)) {
    $_SESSION['cart'] = array();
    echo "Record updated successfully";
    header('Location: index.php');
  } else {
    echo "Error updating record: " . mysqli_error($conn);
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/w3.css">
  <title>Pawshop</title>
</head>

<body>
  <!-- Sidebar -->
  <div class="w3-sidebar w3-bar-block w3-border-right" style="display:none" id="mySidebar">
    <button onclick="w3_close()" class="w3-button w3-bar-item w3-large w3-hover-red">Close &times;</button>
    <a href="./index.php" class="w3-bar-item w3-button w3-hover-teal">Dashboard</a>
    <a href="./tambah-barang.php" class="w3-bar-item w3-button w3-hover-teal <?= $customer; ?>">Tambah Barang</a>
    <a href="./cart.php" class="w3-bar-item w3-button w3-blue w3-hover-teal <?= $admin; ?>">Cart</a>
    <a href="./transaksi.php" class="w3-bar-item w3-button w3-hover-teal <?= $customer; ?>">Transaksi</a>
    <a href="./profile.php" class="w3-bar-item w3-button w3-hover-teal">Profile</a>
    <a href="./logout.php" class="w3-bar-item w3-button w3-hover-red">Logout</a>
  </div>

  <!-- Header -->
  <div class="w3-teal">
    <button class="w3-button w3-teal w3-xlarge" onclick="w3_open()">☰</button>
    <div class="w3-container">
      <center>
        <h1>Your shopping Cart!</h1>
      </center>
    </div>
  </div>
  <table style="margin-left: 25%; width: 50%;" class="w3-margin-top w3-table-all w3-hoverable">
    <tr class="w3-blue">
      <th class="w3-center" style="width: 25%;">Gambar</th>
      <th class="w3-center">Nama</th>
      <th class="w3-center">Qty</th>
      <th class="w3-center">Subtotal</th>
    </tr>
    <?php
    // Display the cart content
    if (!empty($_SESSION['cart'])) {
      $total = 0;
      foreach ($_SESSION['cart'] as $product_id => $quantity) {
        // Fetch product details from the database
        $sql = "SELECT * FROM product WHERE id = $product_id";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
          $row = mysqli_fetch_assoc($result);
          $subtotal = (int)$row['harga'] * (int)$quantity;
          $total += $subtotal;
          echo "<tr>";
          echo "<td class='w3-center'><img width='100%' src='./img/{$row['gambar']}'></td>";
          echo "<td class='w3-center'>{$row['nama_produk']}</td>";
          echo "<td class='w3-center'>$quantity</td>";
          echo "<td class='w3-right-align'>Rp " . number_format($subtotal) . "</td>";
          echo "</tr>";
        }
      }
    ?>
      <tr>
        <td class="w3-center w3-blue"> </td>
        <td class="w3-center w3-blue"> </td>
        <td class="w3-center w3-blue"><b>Total</b></td>
        <td class="w3-right-align">Rp <?= number_format($total); ?></td>
      </tr>
    <?php

      echo '';
    } else {
    ?>
      <tr>
        <td class="w3-center" colspan="4">Your cart is empty.</td>
      </tr>
    <?php
    }
    ?>
  </table>

  <form action="" method="post">
    <input type="hidden" name="total_amount" value="<?= $total; ?>">
    <div class="w3-container">
      <center>
        <p>Metode Pembayaran: </p>
        <select class="w3-select" name="payment_method" style="width: 20%;">
          <option value="" disabled selected>Pilih metode</option>
          <option value="BCA">BCA</option>
          <option value="Tunai">Tunai</option>
        </select>
      </center>
    </div>
    <div class="w3-container">
      <p class="w3-center">
        <button name="checkout" href="./checkout.php" class="w3-button w3-teal w3-hover-blue">Checkout</button>
        <button id="clearCartBtn" class="w3-button w3-red w3-hover-orange">Clear Cart</button>
      </p>
    </div>
  </form>

  <script>
    function w3_open() {
      document.getElementById("mySidebar").style.display = "block";
    }

    function w3_close() {
      document.getElementById("mySidebar").style.display = "none";
    }
  </script>
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script>
    $(document).ready(function() {
      $("#clearCartBtn").click(function() {
        // Clear the session variable on the client side
        $.ajax({
          type: "POST",
          url: "cart.php",
          data: {
            clearCart: true
          }, // Pass a parameter to indicate clearing the cart
          success: function(response) {
            // Handle the response if needed
            console.log(response);

            // Reload cart.php
            location.reload();
          },
          error: function(xhr, status, error) {
            // Handle errors if needed
            console.error(xhr.responseText);
          }
        });
      });
    });
  </script>
</body>

</html>