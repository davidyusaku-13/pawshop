<?php
if ($privilege != 'admin') {
  header('Location: index.php');
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pawshop | Dashboard</title>
  <link rel="icon" type="image/x-icon" href="./logo-title.png">
  <link href="./css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link rel="stylesheet" href="./css/datatables.min.css">
  <link rel="stylesheet" href="./css/sidebar.css">
  <script src="./js/sidebar.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Montserrat', sans-serif;
    }
  </style>
  <script src="./js/bootstrap.bundle.js"></script>
  <script src="./js/datatables.min.js"></script>
  <script src="https://kit.fontawesome.com/ec712a3d01.js" crossorigin="anonymous"></script>
</head>

<body id="body-pd" style="background-color: #DCDDDD;">
  <header class="header" id="header">
    <div class="header_toggle"> <i class='bx bx-menu' id="header-toggle"></i> </div>
    <div class="header_img"> <img src="./logo-title.png" alt=""> </div>
  </header>
  <div class="l-navbar" id="nav-bar">
    <nav class="nav">
      <div>
        <!-- <a href="#" class="nav_logo"> <i class='bx bx-layer nav_logo-icon'></i> <span class="nav_logo-name">BBBootstrap</span> </a> -->
        <div class="nav_list">
          <a href="./index.php" class="nav_link active"><i class='bx bx-grid-alt nav_icon'></i><span class="nav_name">Dasbor</span></a>
          <a href="#" class="nav_link"><img src="./paw.png" class="nav_icon" width="18em" alt=""><span class="nav_name">Produk</span></a>
          <a href="#" class="nav_link"><i class='bx bx-user nav_icon'></i><span class="nav_name">Pengguna</span></a>
          <a href="#" class="nav_link"><i class='bx bx-bar-chart-alt-2 nav_icon'></i> <span class="nav_name">Laporan</span></a>
        </div>
      </div> <a href="./logout.php" class="nav_link"><i class='bx bx-log-out nav_icon'></i><span class="nav_name">Keluar</span></a>
    </nav>
  </div>

  <!-- Start Content -->
  <div class="container">
    <div class="row">
      <!-- Start Card Pemasukan -->
      <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-6 col-sm-1 mt-3 d-flex justify-content-center">
        <div class="card text-white rounded-4" style="width:300px; background-color: #20639B;">
          <div class="card-body ms-3 mt-2">
            <p class="fs-6 fw-bold">PEMASUKAN</p>
            <?php
            $sql = "SELECT SUM(total_amount) AS pemasukan FROM transaksi";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
              while ($row = mysqli_fetch_assoc($result)) {
                $pemasukan = $row['pemasukan'];
              }
            }
            ?>
            <p class="fs-5" style="font-weight: 900;">Rp <?= number_format($pemasukan, 2, ",", ".") ?></p>
          </div>
        </div>
      </div>
      <!-- End Card Pemasukan -->

      <!-- Start Card GATAU -->
      <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-6 col-sm-1 mt-3 d-flex justify-content-center">
        <div class="card text-white rounded-4" style="width:300px; background-color: #ED553B;">
          <div class="card-body ms-3 mt-2">
            <p class="fs-6 fw-bold">GATAU</p>
            <p class="fs-5" style="font-weight: 900;">Rp 150.000,00</p>
          </div>
        </div>
      </div>
      <!-- End Card GATAU -->

      <!-- Start Card Produk Stok Habis -->
      <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-6 col-sm-1 mt-3 d-flex justify-content-center">
        <div class="card text-white rounded-4" style="width:300px; background-color: #3CAEA3;">
          <div class="card-body ms-3 mt-2">
            <p class="fs-6 fw-bold position-relative">
              PRODUK
              <span class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle">
                <span class="visually-hidden">New alerts</span>
              </span>
            </p>
            <p class="fs-5" style="font-weight: 900;">3 Produk Hampir Habis</p>
          </div>
        </div>
      </div>
      <!-- End Card Produk Stok Habis -->

      <!-- Start Card Notif Transaksi -->
      <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-6 col-sm-1 mt-3 d-flex justify-content-center">
        <div class="card text-white rounded-4" style="width:300px; background-color: #F6D55C;">
          <div class="card-body ms-3 mt-2">
            <p class="fs-6 fw-bold position-relative">
              TRANSAKSI
              <span class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle">
                <span class="visually-hidden">New alerts</span>
              </span>
            </p>
            <p class="fs-5" style="font-weight: 900;">2 Pemberitahuan</p>
          </div>
        </div>
      </div>
      <!-- End Card Notif Transaksi -->
    </div>
  </div>

  <!-- START PROGRESSBAR GRAFIK PENJUALAN -->
  <div class="container mt-4">
    <div class="row">
      <div class="col d-flex justify-content-center">
        <div class="card text-white rounded-4" style="width:100%; background-color: #173F5F;">
          <div class="card-body ms-3 mt-2">
            <p class="fs-6 fw-bold">GRAFIK PENJUALAN (PER KATEGORI)</p>
            <?php
            $sql = "SELECT name, SUM(produk_terjual) AS total_terjual_per_kategori, SUM(total_stok) AS total_stok FROM (SELECT p.category_id, SUM(quantity) AS produk_terjual, SUM(stok) AS total_stok FROM transaksi_detail trd JOIN produk p ON trd.product_id = p.id GROUP BY p.id, p.category_id) AS derived_table JOIN kategori k ON category_id=k.id GROUP BY category_id";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
              $color = array("#ED553B", "#3CAEA3", "#E0B828", "#ffc9d4");
              $c = 0;
              while ($row = mysqli_fetch_assoc($result)) {
                $persentase = $row['total_terjual_per_kategori'] / ($row['total_stok'] + $row['total_terjual_per_kategori']) * 100;
                $persentase = number_format($persentase, 0);
            ?>
                <?= $row['name'] ?>
                <div class="progress me-3" role="progressbar" aria-label="Basic example" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                  <div class="progress-bar" style="background-color:<?= $color[$c] ?>;width: <?= $persentase ?>%"><?= $persentase ?>%</div>
                </div>
                <p><?= $row['total_terjual_per_kategori'] ?> dari <?= $row['total_stok'] + $row['total_terjual_per_kategori'] ?></p>
            <?php
                $c++;
              }
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  <!-- END PROGRESSBAR GRAFIK PENJUALAN -->
  <!-- End Content -->


  <!-- Start Footer -->
  <footer>
  </footer>
  <!-- End Footer -->

  <script>
    new DataTable('#example');
  </script>
</body>

</html>