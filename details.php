<?php
include 'config.php';
session_start();

$search = '';
$kategori = '';
$total = 0;

$search = isset($_GET['s']) ? $_GET['s'] : '';
$kategori = isset($_GET['k']) ? $_GET['k'] : '';
$detailID = isset($_GET['id']) ? $_GET['id'] : '';
if ($kategori == '') {
  $kategori_status = 'active';
}

$transaction_status = '';
if (isset($_SESSION['transaction_status']) && $_SESSION['transaction_status'] == 1) {
  $transaction_status = 'alert("Transaksi berhasil dibuat!!");';
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
  <!-- Start Navbar -->
  <nav class="navbar navbar-expand-sm navbar-light bg-light sticky-top">
    <div class="container-fluid d-flex">
      <a class="navbar-brand" href="./index.php">
        <img src="./logo.png" height="30" alt="">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mynavbar">
        <ul class="navbar-nav me-auto">
          <ul class="navbar-nav">
            <li class="nav-item dropdown" id="profileDropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-user-tie"></i>
              </a>
              <ul class="dropdown-menu">
                <?php
                if (isset($_SESSION['userid'])) {
                ?>
                  <li><a class="dropdown-item" href="./profile.php">Profil</a></li>
                  <li><a class="dropdown-item" href="./transactions.php">Transaksi</a></li>
                  <li>
                    <hr class="dropdown-divider">
                  </li>
                <?php
                }

                if (isset($_SESSION['userid']) && $_SESSION['userid'] != null) {
                ?>

                  <li class="nav-item">
                    <a class="dropdown-item" href="./logout.php">Keluar</a>
                  </li>
                <?php
                } else {
                ?>
                  <li class="nav-item">
                    <a class="dropdown-item" href="./login.php">Masuk</a>
                  </li>
                <?php
                }
                ?>
              </ul>
            </li>
          </ul>
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
                  $kategori_status = ($kategori == $row['id']) ? 'active' : '';
              ?>
                  <li><a class="dropdown-item <?= $kategori_status; ?>" href="?k=<?= $row['id']; ?>"><?= $row['name']; ?></a></li>
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
        <form class="d-flex" action="" method="GET">
          <input class="form-control me-2" type="search" placeholder="Cari Produk" name="s" value="<?= $search; ?>">
          <button class="btn btn-primary" type="submit">Cari</button>
        </form>
      </div>
    </div>
  </nav>
  <!-- End Navbar -->

  <!-- Start Content -->

  <!-- Start Card -->
  <?php
  $sql = "SELECT * FROM produk WHERE id='$detailID'";
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
  ?>
      <div class="container col-xxl-8 px-4 py-5">
        <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
          <div class="col-10 col-sm-8 col-lg-6">
            <img src="./img/<?= $row['gambar'] ?>" class="d-block mx-lg-auto img-fluid" alt="Image" width="700" height="500" loading="lazy">
          </div>
          <div class="col-lg-6">
            <h1 class="display-5 fw-bold text-body-emphasis lh-1 mb-3"><?= $row['nama_produk'] ?></h1>
            <p class="lead"><?= $row['detail'] ?></p>
            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
              <form action="addToCart.php" method="POST" autocomplete="off">
                <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                <p class="fs-5 fw-bold" style="color: #725F63;">
                  <u>Rp<?= number_format($row['harga'], 2, ',', '.'); ?></u>
                </p>
                <div class="mb-3 form-floating d-flex justify-content-between">
                  <input type="number" class="form-control" id="quantity" min="0" placeholder="" max="<?= $row['stok'] ?>" name="quantity">
                  <label for="quantity">Kuantitas:</label>
                  <p class="ms-2 my-auto">tersisa <span class="fw-bold"><?= $row['stok'] ?></span> buah</p>
                </div>
                <input type="hidden" name="nama_produk" value="<?= $row['nama_produk'] ?>">
                <input type="hidden" name="harga" value="<?= $row['harga'] ?>">
                <!-- Add other necessary hidden fields, if needed -->
                <button type="submit" class="btn btn-primary btn-add-to-cart">Masukkan Keranjang</button>
              </form>
            </div>
          </div>
        </div>
      </div>
  <?php
    }
  }
  ?>
  <!-- End Card -->

  <!-- Start Cart -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="cart" aria-labelledby="cartLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="cartLabel">Keranjang</h5>
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
                  <td style="width: 10em;">
                    <form class="d-flex justify-content-between" action="updateCart.php" method="POST">
                      <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                      <?php
                      $id = $item['id'];
                      $sql = "SELECT * FROM produk WHERE id='$id'";
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
              echo '<tr><td class="text-center" colspan="4">Keranjang anda kosong!</td></tr>';
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
          <div class="d-flex justify-content-end">
            <button name="checkout" class="mt-3 btn btn-success">Checkout</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- End Cart -->

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
  <script src="./js/jquery-3.7.1.min.js"></script>
  <script>
    <?= $transaction_status; ?>
    <?php
    unset($_SESSION['transaction_status']);
    ?>

    // Add to Cart success alert
    $(document).ready(function() {
      $('btn.btn-add-to-cart').click(function() {
        alert('Produk ditambahkan ke keranjang!');
      });
    });

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
  </script>
</body>

</html>