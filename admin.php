<?php
if ($_SESSION['privilege'] != 'admin') {
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
  <nav class="navbar navbar-expand-sm navbar-dark bg-dark sticky-top">
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
            <a class="nav-link" href="./admin-transactions.php">Transaksi</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./admin-products.php">Produk</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./admin-categories.php">Kategori</a>
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

  </div>
  <!-- End Content -->


  <!-- Start Footer -->
  <footer class="footer mt-auto py-3 bg-body-tertiary">
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