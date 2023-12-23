<?php
$search = isset($_GET['s']) ? $_GET['s'] : '';
$kategori = isset($_GET['k']) ? $_GET['k'] : '';
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

  <!-- Start Carousel -->
  <div id="carouselPawshop" class="carousel slide" data-bs-touch="true">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="./img/carousel1.jpg" class="d-block img-fluid" alt="./img/carousel1.jpg">
        <div class="carousel-caption d-md-block">
          <h3>Selamat datang di Pawshop</h3>
          <p>Menjual beragam kebutuhan kucing</p>
        </div>
      </div>
      <div class="carousel-item">
        <img src="./img/carousel2.jpg" class="d-block img-fluid" alt="./img/carousel2.jpg">
        <div class="carousel-caption d-md-block">
          <h3>Selamat datang di Pawshop</h3>
          <p>Mulai dari makanan, vitamin, mainan kucing</p>
        </div>
      </div>
      <div class="carousel-item">
        <img src="./img/carousel5.jpg" class="d-block img-fluid" alt="./img/carousel5.jpg">
        <div class="carousel-caption d-md-block">
          <h3>Selamat datang di Pawshop</h3>
          <p>Hingga perlengkapan kebersihan kucing</p>
        </div>
      </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselPawshop" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselPawshop" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>
  <!-- End Carousel -->

  <!-- Start Card -->
  <div class="container">
    <div class="row">
      <?php
      $sql = "SELECT * FROM produk";

      $prev = '';
      $next = '';

      $page = 1;
      if (isset($_GET['page']) && $_GET['page'] != null) {
        $page = $_GET['page'];
      }

      $item_per_page = 4;
      $total_item = mysqli_num_rows(mysqli_query($conn, $sql));
      $total_page = ceil($total_item / $item_per_page);
      $offset = ($page - 1) * $item_per_page;
      $sql = "SELECT * FROM produk LIMIT $item_per_page OFFSET $offset";

      $prev = $page == 1 ? 'disabled' : '';
      $next = $page == $total_page ? 'disabled' : '';

      if (isset($_GET['s'])) {
        $search = $_GET['s'];
        $sql = "SELECT * FROM produk WHERE nama_produk LIKE '%$search%'";
        $page = 1;
        if (isset($_GET['page']) && $_GET['page'] != null) {
          $page = $_GET['page'];
        }
        $item_per_page = 4;
        $total_item = mysqli_num_rows(mysqli_query($conn, $sql));
        $total_page = ceil($total_item / $item_per_page);
        $offset = ($page - 1) * $item_per_page;
        $sql = "SELECT * FROM produk WHERE nama_produk LIKE '%$search%' LIMIT $item_per_page OFFSET $offset";
      }

      if (isset($_GET['k'])) {
        $kategori = $_GET['k'];
        $sql = "SELECT * FROM produk WHERE category_id LIKE '%$kategori%'";
        $page = 1;
        if (isset($_GET['page']) && $_GET['page'] != null) {
          $page = $_GET['page'];
        }
        $item_per_page = 4;
        $total_item = mysqli_num_rows(mysqli_query($conn, $sql));
        $total_page = ceil($total_item / $item_per_page);
        $offset = ($page - 1) * $item_per_page;
        $sql = "SELECT * FROM produk WHERE category_id LIKE '%$kategori%' LIMIT $item_per_page OFFSET $offset";
      }

      $result = mysqli_query($conn, $sql);

      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
      ?>


          <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-6 col-sm-1 mt-3 d-flex justify-content-center">
            <div class="card" style="width:300px">
              <img class="card-img-top" src="./img/<?= $row['gambar'] ?>" alt="Card image">
              <div class="card-body">
                <h4 class="card-title"><?= $row['nama_produk'] ?></h4>
                <form action="addToCart.php" method="POST" autocomplete="off">
                  <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                  <p class="text-end fs-5 fw-bold" style="color: #725F63;">
                    <u>Rp<?= number_format($row['harga'], 2, ',', '.'); ?></u>
                  </p>
                  <div class="mb-3">
                    <label for="quantity" class="form-label">Kuantitas:</label>
                    <input type="number" class="form-control" id="quantity" min="0" value="1" max="<?= $row['stok'] ?>" name="quantity">
                  </div>
                  <input type="hidden" name="nama_produk" value="<?= $row['nama_produk'] ?>">
                  <input type="hidden" name="harga" value="<?= $row['harga'] ?>">
                  <!-- Add other necessary hidden fields, if needed -->
                  <div class="card-text d-flex justify-content-between">
                    <p>Stok</p>
                    <p><?= $row['stok'] ?></p>
                  </div>
                  <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-add-to-cart">Masukkan Keranjang</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
      <?php
        }
      }
      ?>
    </div>
  </div>
  <!-- End Card -->

  <!-- Start Pagination -->
  <div class="container mt-3 d-flex justify-content-center">
    <nav aria-label="Page navigation example">
      <ul class="pagination">
        <li class="page-item">
          <a class="page-link <?= $prev ?>" href="?page=<?= $page - 1 ?>" aria-label="Previous">
            <span aria-hidden="true">&laquo;</span>
          </a>
        </li>
        <?php
        for ($i = 0; $i < $total_page; $i++) {
          if ($page == $i + 1) {
            $page_status = "active";
          } else {
            $page_status = "";
          }
        ?>
          <li class="page-item"><a class="page-link <?= $page_status; ?>" href="?page=<?= $i + 1; ?>"><?= $i + 1; ?></a></li>
        <?php
        }
        ?>
        <li class="page-item">
          <a class="page-link <?= $next ?>" href="?page=<?= $page + 1 ?>" aria-label="Next">
            <span aria-hidden="true">&raquo;</span>
          </a>
        </li>
      </ul>
    </nav>
  </div>
  <!-- End Pagination -->

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