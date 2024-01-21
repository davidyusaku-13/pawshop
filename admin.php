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
  <script src="./js/datatables.min.js"></script>
  <script src="https://kit.fontawesome.com/ec712a3d01.js" crossorigin="anonymous"></script>
</head>

<body>
  <!-- Start Navbar -->
  <nav class="navbar navbar-expand-sm navbar-light bg-light sticky-top">
    <div class="container-fluid">
      <a class="navbar-brand" href="./index.php">
        <img src="./logo.png" height="30" alt="">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mynavbar">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link" href="./admin-transaksi.php">Transaksi</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./admin-produk.php">Produk</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./admin-kategori.php">Kategori</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./admin-status.php">Status</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./admin-users.php">Users</a>
          </li>
          <?php
          if (isset($_SESSION['userid']) && $_SESSION['userid'] != null) {
          ?>

            <li class="nav-item">
              <a class="nav-link" href="./logout.php">Logout</a>
            </li>
          <?php
          } else {
          ?>
            <li class="nav-item">
              <a class="nav-link" href="./login.php">Login</a>
            </li>
          <?php
          }
          ?>
        </ul>
      </div>
    </div>
  </nav>
  <!-- End Navbar -->

  <!-- Start Content -->
  <div class="container">
    <div class="row">
      <?php
      $trCount = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi"));
      $prodCount = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM produk"));
      $catCount = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM kategori"));
      $statCount = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM status"));
      $userCount = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users"));

      $rowCount = array($trCount, $prodCount, $catCount, $statCount, $userCount);
      $dashboard = array("Transaksi", "Produk", "Kategori", "Status", "Users")
      ?>

      <!-- Start Card -->
      <?php
      for ($i = 0; $i < count($rowCount); $i++) {
      ?>
        <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-6 col-sm-1 mt-3 d-flex justify-content-center">
          <div class="card" style="width:300px">
            <div class="card-body text-center">
              <!-- <img src="./img/cart-shopping-svgrepo-com.png" class="card-img-top w-25" alt=""> -->
              <h5 class="card-title"><?= $rowCount[$i] ?></h5>
              <a class="btn btn-secondary" href="./admin-<?= strtolower($dashboard[$i]) ?>.php" class="card-text"><?= $dashboard[$i] ?></a>
            </div>
          </div>
        </div>
      <?php
      }
      ?>
      <!-- End Card -->

    </div>
  </div>
  <!-- End Content -->


  <!-- Start Footer -->
  <footer class="footer mt-3 py-3 bg-body-tertiary">
    <div class="container">
      <span class="text-body-secondary">
        &copy; 2023 Pawshop, Inc. &middot; <a href="#">Privacy</a> &middot; <a href="#">Terms</a>
      </span>
    </div>
  </footer>
  <!-- End Footer -->

  <script src="./js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script>
    new DataTable('#example');
  </script>
</body>

</html>