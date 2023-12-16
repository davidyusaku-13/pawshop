<?php
include "config.php";
session_start();

if (!isset($_SESSION['idsession']) && !isset($_SESSION['usersession'])) {
  header('Location: login.php');
} else {
  $idsession = $_SESSION['idsession'];
}

if ($_SESSION['usersession'] != 'admin') {
  $customer = "w3-hide";
  $admin = "";
} else {
  $customer = "";
  $admin = "w3-hide";
}

if (isset($_GET['id']) && $_GET['id'] != null) {
  $transid = $_GET['id'];
}

?>
<!DOCTYPE html>
<html>
<title>Pawshop</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="./css/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

<body>
  <!-- Sidebar -->
  <div class="w3-sidebar w3-bar-block w3-border-right" style="display:none" id="mySidebar">
    <button onclick="w3_close()" class="w3-button w3-bar-item w3-large w3-hover-red">Close &times;</button>
    <a href="./index.php" class="w3-bar-item w3-button w3-blue w3-hover-teal">Dashboard</a>
    <a href="./tambah-barang.php" class="w3-bar-item w3-button w3-hover-teal <?= $customer; ?>">Tambah Barang</a>
    <a href="./cart.php" class="w3-bar-item w3-button w3-hover-teal <?= $admin; ?>">Cart</a>
    <a href="./transaksi.php" class="w3-bar-item w3-button w3-hover-teal <?= $customer; ?>">Transaksi</a>
    <a href="./profile.php" class="w3-bar-item w3-button w3-hover-teal">Profile</a>
    <a href="./logout.php" class="w3-bar-item w3-button w3-hover-red">Logout</a>
  </div>

  <!-- Header -->
  <div class="w3-teal">
    <button class="w3-button w3-teal w3-xlarge" onclick="w3_open()">☰</button>
    <div class="w3-container">
      <center>
        <h1>Hi, <?= $_SESSION['usersession']; ?>!</h1>
      </center>
    </div>
  </div>

  <!-- Content -->
  <div class="w3-container w3-margin-top">
    <table class="w3-table-all w3-hoverable">
      <tr class="w3-blue">
        <th class="w3-center">Transaction ID</th>
        <th class="w3-center">Nama Produk</th>
        <th class="w3-center">Quantity</th>
        <th class="w3-center">Unit Price</th>
        <th class="w3-center">Subtotal</th>
      </tr>
      <?php
      $sql = "SELECT trd.transaction_id, p.nama_produk, trd.quantity, trd.unit_price, trd.subtotal FROM transaction_details trd JOIN product p ON trd.product_id=p.id WHERE trd.transaction_id='$transid'"; // JOIN users ON transactions.user_id=users.id
      $result = mysqli_query($conn, $sql);

      if (mysqli_num_rows($result) > 0) {
        // output data of each row
        while ($row = mysqli_fetch_assoc($result)) {
      ?>
          <tr>
            <td class="w3-center" style="vertical-align: middle;">
              <?= $row['transaction_id'] ?>
            </td>
            <td class="w3-center" style="vertical-align: middle;">
              <?= $row['nama_produk'] ?>
            </td>
            <td class="w3-center" style="vertical-align: middle;">
              <?= $row['quantity'] ?>
            </td>
            <td class="w3-center" style="vertical-align: middle;">
              Rp <?= number_format($row['unit_price']) ?>
            </td>
            <td class="w3-center" style="vertical-align: middle;">
              Rp <?= number_format($row['subtotal']) ?>
            </td>
          </tr>
        <?php
        }
      } else {
        ?>
        <tr>
          <td class="w3-center" colspan="5">You have 0 items.</td>
        </tr>
      <?php
      }
      ?>
    </table>
  </div>

  <script>
    function w3_open() {
      document.getElementById("mySidebar").style.display = "block";
    }

    function w3_close() {
      document.getElementById("mySidebar").style.display = "none";
    }
  </script>
</body>

</html>