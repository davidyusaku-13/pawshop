<?php
include "config.php";
session_start();
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

if ($_SESSION['usersession'] != 'admin') {
    $customer = "w3-hide";
    $admin = "";
} else {
    $customer = "";
    $admin = "w3-hide";
}
if (!isset($_SESSION['idsession']) && !isset($_SESSION['usersession'])) {
    header('Location: login.php');
} else {
    $idsession = $_SESSION['idsession'];
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
        <a href="./tambah-barang.php" class="w3-bar-item w3-button w3-hover-teal <?= $customer; ?>">Tambah Barang</a>
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
            <?php
            $sql = "SELECT * FROM product WHERE id='$id'";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                // output data of each row
                while ($row = mysqli_fetch_assoc($result)) {
            ?>
                    <img width="100%" src="./img/<?= $row['gambar'] ?>">
                    <form class="w3-container" method="POST" action="update-data.php" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <p>
                            <label class="w3-text-teal"><b>Gambar</b></label>
                            <input class="w3-input w3-border w3-light-grey" type="file" name="gambar">
                        </p>
                        <p>
                            <label class="w3-text-teal"><b>Nama Produk</b></label>
                            <input class="w3-input w3-border w3-light-grey" type="text" name="nama_produk" value="<?= $row['nama_produk'] ?>">
                        </p>
                        <p>
                            <label class="w3-text-teal"><b>Stok</b></label>
                            <input class="w3-input w3-border w3-light-grey" type="number" name="stok" min="0" value="<?= $row['stok'] ?>">
                        </p>
                        <p>
                            <label class="w3-text-teal"><b>Harga</b></label>
                            <input class="w3-input w3-border w3-light-grey" type="number" name="harga" min="0" value="<?= $row['harga'] ?>">
                        </p>
                        <center>
                            <p>
                                <button class="w3-button w3-blue-grey w3-hover-teal" type="submit" name="submit">Update</button>
                                <button class="w3-button w3-blue-grey w3-hover-red" type="reset">Reset</button>
                            </p>
                        </center>
                    </form>
            <?php
                }
            } else {
                echo "0 results";
            }
            ?>

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