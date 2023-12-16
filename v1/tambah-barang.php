<?php
include "config.php";
session_start();
if (!isset($_SESSION['idsession']) && !isset($_SESSION['usersession'])) {
    header('Location: login.php');
} else {
    $idsession = $_SESSION['idsession'];
}
if ($_SESSION['usersession'] != 'admin') {
    header('Location: login.php');
}
if (isset($_GET['id'])) {
    $id = isset($_GET['id']);
}

if ($_SESSION['usersession'] != 'admin') {
    $customer = "w3-hide";
    $admin = "";
} else {
    $customer = "";
    $admin = "w3-hide";
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
        <a href="./index.php" class="w3-bar-item w3-button w3-hover-teal">Dashboard</a>
        <a href="./tambah-barang.php" class="w3-bar-item w3-button w3-blue w3-hover-teal">Tambah Barang</a>
        <a href="./cart.php" class="w3-bar-item w3-button w3-hover-teal <?= $admin; ?>">Cart</a>
        <a href="./transaksi.php" class="w3-bar-item w3-button w3-hover-teal">Transaksi</a>
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
        <div class="w3-card" style="width: 25%; margin-left: 37.5%;">
            <form class="w3-container" method="POST" action="add-data.php" enctype="multipart/form-data">
                <p>
                    <label class="w3-text-teal"><b>Gambar</b></label>
                    <input class="w3-input w3-border w3-light-grey" type="file" name="gambar">
                </p>
                <p>
                    <label class="w3-text-teal"><b>Nama Produk</b></label>
                    <input class="w3-input w3-border w3-light-grey" type="text" name="nama_produk">
                </p>
                <p>
                    <label class="w3-text-teal"><b>Kategori</b></label>
                    <select class="w3-select" name="kategori">
                        <option value="" disabled selected>Choose your option</option>
                        <option value="1">Makanan Kucing</option>
                        <option value="2">Perlengkapan Makan dan Minum</option>
                        <option value="3">Perlengkapan Kesehatan dan Kebersihan</option>
                        <option value="4">Mainan Kucing</option>
                    </select>
                </p>
                <p>
                    <label class="w3-text-teal"><b>Stok</b></label>
                    <input class="w3-input w3-border w3-light-grey" type="number" name="stok" min="0">
                </p>
                <p>
                    <label class="w3-text-teal"><b>Harga</b></label>
                    <input class="w3-input w3-border w3-light-grey" type="number" name="harga" min="0">
                </p>
                <center>
                    <p>
                        <button class="w3-button w3-blue-grey w3-hover-teal" type="submit" name="submit">Tambah</button>
                        <button class="w3-button w3-blue-grey w3-hover-red" type="reset">Reset</button>
                    </p>
                </center>
            </form>
        </div>
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