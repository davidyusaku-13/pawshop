<?php
include 'config.php';

if (!isset($userid)) {
  header("Location: index.php");
}

if (isset($_GET['id']) && $_GET['id'] != '') {
  $transID = $_GET['id'];
} else {
  header('Location: transactions.php');
}

?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pawshop</title>
  <link rel="icon" type="image/x-icon" href="./logo-title.png">
  <link href="./css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <script src="https://kit.fontawesome.com/ec712a3d01.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="./css/style.css">
</head>

<body>
  <header>
    <!-- place navbar here -->
  </header>
  <main>
    <!-- START NOTA -->
    <div class="container mt-3">
      <div class="text-center">
        <img src="./logo-title.png" alt="" width="100em">
        <h1>Pawshop</h1>
        <p>----------------------</p>
        <p><?= $transID; ?></p>
        <p>----------------------</p>
      </div>
      <!-- START HEADER TABEL -->
      <div class="row">
        <div class="col">

          <!-- START TABLE -->
          <div class="table-responsive">
            <table class="table table-borderless mx-auto" style="width: 75%;">
              <thead>
                <tr>
                  <th>Nama Produk</th>
                  <th class="text-end">Qty</th>
                  <th class="text-end">Harga</th>
                  <th class="text-end">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <!-- END HEADER TABEL -->
                <?php
                $sql = "SELECT * FROM transaksi_detail trd JOIN produk p ON trd.product_id=p.id JOIN transaksi tr ON trd.transactions_id=tr.id WHERE transactions_id='$transID'";
                $result = mysqli_query($conn, $sql);
                $total = 0;
                $payment_method = '';
                if (mysqli_num_rows($result) > 0) {
                  while ($row = mysqli_fetch_assoc($result)) {
                    $total += $row['subtotal'];
                    $payment_method = $row['payment_method'];
                ?>
                    <tr>
                      <td><?= $row['nama_produk'] ?></td>
                      <td class="text-end"><?= $row['quantity'] ?></td>
                      <td class="text-end">Rp <?= number_format($row['harga'], 2, ',', '.') ?></td>
                      <td class="text-end">Rp <?= number_format($row['subtotal'], 2, ',', '.') ?></td>
                    </tr>
                <?php
                  }
                }
                ?>
              </tbody>
              <tfoot>
                <tr>
                  <th colspan="3" class="text-end">Total</th>
                  <th colspan="3" class="text-end">Rp <?= number_format($total, 2, ',', '.') ?></th>
                </tr>
                <tr>
                  <td colspan="4" class="text-end">Metode Pembayaran: <?= $payment_method ?></td>
                </tr>
              </tfoot>
            </table>
          </div>
          <!-- END TABLE -->
          <?php
          $sql = "SELECT username FROM users WHERE id=$userid";
          $result = mysqli_query($conn, $sql);
          if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
              $username = $row['username'];
            }
          }
          ?>
          <div class="table-responsive">
            <table class="table table-borderless mx-auto" style="width: 75%;">
              <tr>
                <td class="text-end">Pembeli: <?= $username ?></td>
              </tr>
              <tr>
                <?php
                $prefix = substr($transID, 0, 3);
                $datePart = substr($transID, 3, 6);
                $timePart = substr($transID, 9);

                // Formatting the date part
                $year = "20" . substr($datePart, 0, 2);
                $month = substr($datePart, 2, 2);
                $day = substr($datePart, 4, 2);

                // Formatting the time part
                $hour = substr($timePart, 0, 2);
                $minute = substr($timePart, 2, 2);
                $second = substr($timePart, 4, 2);

                // Creating the final formatted output
                $output = "$year-$month-$day $hour:$minute:$second";
                ?>
                <td class="text-end"><?= $output ?></td>
              </tr>
            </table>
          </div>

        </div>
      </div>
    </div>
    <!-- END NOTA -->
  </main>
  <footer>
    <!-- place footer here -->
  </footer>
  <!-- Bootstrap JavaScript Libraries -->
  <script src="./js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="./js/jquery-3.7.1.min.js"></script>
</body>

</html>