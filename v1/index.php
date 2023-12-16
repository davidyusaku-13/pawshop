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
        <center>
            <input style="width: 25%;" class="w3-center w3-input w3-border w3-padding" type="text" placeholder="Search product name..." id="myInput" onkeyup="myFunction()">
        </center>
        <table class="w3-margin-top w3-table-all w3-hoverable" style="width: 50%;margin-left: 25%;" id="myTable">
            <tr class="w3-blue">
                <th class="w3-center" style="width: 25%;">Gambar</th>
                <th class="w3-center">Nama</th>
                <th class="w3-center">Stok</th>
                <th class="w3-center">Harga</th>
                <th class="w3-center">Aksi</th>
            </tr>
            <?php
            $sql = "SELECT *, CONCAT('Rp ', FORMAT(harga, 0)) AS harga FROM product";
            $result = mysqli_query($conn, $sql);

            $item_per_page = 2;
            $rows = count(mysqli_fetch_assoc($result));
            $total_page = ceil($rows / $item_per_page);
            $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
            $offset = ($current_page - 1) * $item_per_page;
            $sql = "SELECT *, CONCAT('Rp ', FORMAT(harga, 0)) AS harga FROM product LIMIT $offset, $item_per_page";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                // output data of each row
                while ($row = mysqli_fetch_assoc($result)) {
            ?>
                    <tr>
                        <td class="w3-center" style="vertical-align: middle;">
                            <img width="100%" src="./img/<?= $row['gambar'] ?>">
                        </td>
                        <td class="w3-center" style="vertical-align: middle;">
                            <?= $row['nama_produk'] ?>
                        </td>
                        <td class="w3-center" style="vertical-align: middle;">
                            <?= $row['stok'] ?>
                        </td>
                        <td class="w3-center" style="vertical-align: middle;">
                            <?= $row['harga'] ?>
                        </td>
                        <td class="w3-center" style="vertical-align: middle;">
                            <a href="edit.php?id=<?= $row['id']; ?>"><i class="<?= $customer; ?> material-icons">edit</i></a>
                            <a href="delete-data.php?id=<?= $row['id']; ?>"><i class="<?= $customer; ?> material-icons">delete</i></a>
                            <form class="<?= $admin; ?>" method="post" action="./cart.php">
                                <input type="hidden" name="product_id" value="<?= $row['id']; ?>"> <!-- ID produk dari database -->
                                <p><input type="number" style="width: 50%;" name="quantity" value="0" min="0" max="<?= $row['stok']; ?>"></p>
                                <p><button style="width: auto;" class="w3-button w3-blue-grey w3-hover-teal" type="submit" name="submit">Add to Cart</button></p>
                            </form>
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
        <div class="w3-margin-top w3-bar w3-center">
            <?php
            for ($i = 0; $i < $total_page - 1; $i++) {
                # code...
                echo '<a href="?page=' . $i + 1 . '" class="w3-button">' . $i + 1 . '</a>';
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

        function myFunction() {
            var input, filter, table, tr, td, i;
            input = document.getElementById("myInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("myTable");
            tr = table.getElementsByTagName("tr");
            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[1];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
</body>

</html>