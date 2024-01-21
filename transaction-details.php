<?php
include('config.php');

$total = 0;

if (!isset($userid)) {
  header('Location: index.php');
}

if (isset($_GET['id']) && $_GET['id'] != null) {
  $transID = $_GET['id'];
} else {
  header('Location: transactions.php');
}

$search = '';
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

  <!-- Start Navbar -->
  <header>
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
            <li class="nav-item dropdown" id="profileDropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-user-tie"></i>
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="./profile.php">My Profile</a></li>
                <li><a class="dropdown-item" href="./transactions.php">Transactions</a></li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <?php
                if (isset($_SESSION['userid']) && $_SESSION['userid'] != null) {
                ?>

                  <li class="nav-item">
                    <a class="dropdown-item" href="./logout.php">Logout</a>
                  </li>
                <?php
                } else {
                ?>
                  <li class="nav-item">
                    <a class="dropdown-item" href="./login.php">Login</a>
                  </li>
                <?php
                }
                ?>
              </ul>
            </li>
            <li class="nav-item dropdown" id="kategoriDropdown">
              <button class="nav-link dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Kategori
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item <?= $kategori_status ?>" href="./index.php?k=">Semua Produk</a></li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <?php
                $sql = "SELECT * FROM kategori";
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                  // output data of each row
                  while ($row = mysqli_fetch_assoc($result)) {
                ?>
                    <li><a class="dropdown-item" href="?k=<?= $row['id']; ?>"><?= $row['name']; ?></a></li>
                <?php
                  }
                } else {
                  echo "";
                }
                ?>
              </ul>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="offcanvas" href="#cart" role="button" aria-controls="cart">
                <i class="fa-solid fa-cart-shopping"></i>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>
  <!-- End Navbar -->

  <!-- Start Content -->
  <main>
    <div class="mt-3 container">
      <div class="row">
        <p>
          <a class="btn btn-info" href="./transactions.php">
            << Transaksi</a>
        </p>
        <!-- START TABLE -->
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Nama Produk</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT * FROM transaksi_detail trd JOIN produk p ON trd.product_id=p.id WHERE transactions_id='$transID'";
              $result = mysqli_query($conn, $sql);

              if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
              ?>
                  <tr>
                    <td><?= $row['nama_produk'] ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td>Rp <?= number_format($row['unit_price']) ?></td>
                    <td>Rp <?= number_format($row['subtotal']) ?></td>
                  </tr>
              <?php
                }
              }
              ?>
            </tbody>
            <tfoot>
              <tr>
                <th>Nama Produk</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Subtotal</th>
              </tr>
            </tfoot>
          </table>
        </div>
        <!-- END TABLE -->
        <div class="d-block text-end">
          <a href="./invoice.php?id=<?= $_GET['id'] ?>" class="btn btn-primary">Print Nota</a>
        </div>
      </div>
    </div>


    <!-- Start Cart -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="cart" aria-labelledby="cartLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="cartLabel">Your Shopping Cart</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Aksi</th>
                <th>Nama</th>
                <th>Qty</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                  $subtotal = $item['quantity'] * $item['price'];
                  $total += $subtotal;
              ?>
                  <tr>
                    <td class="text-center"><a href="removeFromCart.php?product_id=<?= $item['id'] ?>"><i class="fa-solid fa-trash"></i></a></td>
                    <td><?= $item['name'] ?></td>
                    <td>
                      <form class="d-flex justify-content-between" action="updateCart.php" method="POST">
                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                        <?php
                        $id = $item['id'];
                        $sql = "SELECT * FROM product WHERE id='$id'";
                        $res = mysqli_query($conn, $sql);
                        if (mysqli_num_rows($res) > 0) {
                          while ($row = mysqli_fetch_assoc($res)) {
                        ?>
                            <input class="form-control" type="number" name="quantity" max="<?= $row['stok'] ?>" value="<?= $item['quantity'] ?>" min="1">
                        <?php
                          }
                        }
                        ?>
                        <button class="ms-1 btn btn-primary" type="submit"><i class="fa-solid fa-refresh"></i></button>
                      </form>
                    </td>
                    <td>Rp <?= number_format($subtotal) ?></td>
                  </tr>
              <?php
                }
              } else {
                echo "<p>Your cart is empty.</p>";
              }
              ?>
            </tbody>
            <tfoot>
              <tr>
                <th class="text-end" colspan="3">Total</th>
                <th>Rp <?= number_format($total) ?></th>
              </tr>
            </tfoot>
          </table>
          <form action="checkout.php" method="post">
            <input type="hidden" name="user_id" value="<?= $userid ?>">
            <select class="form-select" name="payment_method" id="payment_method" required>
              <option value="" disabled selected>Metode Pembayaran</option>
              <option value="Transfer">Transfer</option>
              <option value="Tunai">Tunai</option>
            </select>
            <button name="checkout" class="mt-3 btn btn-success">Checkout</button>
          </form>
        </div>
      </div>
    </div>
    <!-- End Cart -->
  </main>
  <!-- End Content -->
  <?php
  $sql = "SELECT expiry_date FROM transaksi WHERE id='$transID'";
  $res = mysqli_query($conn, $sql);
  if (mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
      $expiry_date = $row['expiry_date'];
    }
  }
  ?>
  <div id="compare"><?= $expiry_date ?></div>
  <div id="clock"></div>

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
  <script src="./js/jquery-3.7.1.min.js"></script>
  <script>
    // Reusable function for dropdown hover
    function enableDropdownHover(dropdownId) {
      $(document).ready(function() {
        // Show dropdown on hover
        $(`#${dropdownId}`).hover(
          function() {
            $(this).addClass('show');
            $('ul.dropdown-menu', this).addClass('show');
          },
          function() {
            $(this).removeClass('show');
            $('ul.dropdown-menu', this).removeClass('show');
          }
        );
      });
    }

    // Enable hover for the second dropdown
    enableDropdownHover('profileDropdown');

    // Enable hover for the first dropdown
    enableDropdownHover('kategoriDropdown');

    // LIVE CLOCK
    var liveclock = document.getElementById('clock');
    var compare = document.getElementById('compare');

    function time() {
      var d = new Date();
      var D = ("0" + d.getDate()).substr(-2);
      var M = ("0" + (d.getMonth() + 1)).substr(-2);
      var Y = ("0" + d.getFullYear()).substr(-2);
      var h = ("0" + d.getHours()).substr(-2);
      var m = ("0" + d.getMinutes()).substr(-2);
      var s = ("0" + d.getSeconds()).substr(-2);

      liveclock.textContent = D + M + Y + h + m + s;
      while (liveclock >= compare) {
        <?php
        $sql = "UPDATE transaksi SET status_id=6 WHERE id='$transID'";
        if (mysqli_query($conn, $sql)) {
        ?>
          // window.alert('Dalam 2x24 jam tidak ada pembayaran. Transaksi dibatalkan!');
          break;
        <?php
        }
        ?>
      }
    }
    setInterval(time, 1000);
  </script>
</body>

</html>