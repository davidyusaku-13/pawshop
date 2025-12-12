<?php

$search = get('s');
$kategori = get('k');

if ($kategori == '') {
    $kategori_status = 'active';
}

$cart_status = '';
if (isset($_SESSION['cart_status']) && $_SESSION['cart_status'] == 1) {
    $cart_status = 'alert("Berhasil menambah keranjang!!");';
}

$transaction_status = '';
if (isset($_SESSION['transaction_status']) && $_SESSION['transaction_status'] == 1) {
    $transaction_status = 'alert("Transaksi berhasil dibuat!!");';
}

$transaction_error = '';
if (isset($_SESSION['transaction_error'])) {
    $transaction_error = 'alert(' . eJs($_SESSION['transaction_error']) . ');';
}

$item_per_page = getPositiveInt('itemsPerPage', 4);
if (!in_array($item_per_page, [4, 8, 12])) {
    $item_per_page = 4;
}

$total = 0;
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
                                    <?php if (isLoggedIn()): ?>
                                        <li><a class="dropdown-item" href="./profile.php">Profil</a></li>
                                        <li><a class="dropdown-item" href="./transactions.php">Transaksi</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="./logout.php">Keluar</a>
                                        </li>
                                    <?php else: ?>
                                        <li class="nav-item">
                                            <a class="dropdown-item" href="./login.php">Masuk</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        </ul>
                        <li class="nav-item dropdown" id="kategoriDropdown">
                            <button class="nav-link dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Kategori
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item <?= $kategori_status ?? '' ?>" href="./index.php?k=">Semua Produk</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <?php
                                $categories = dbFetchAll("SELECT * FROM kategori");
                                foreach ($categories as $row):
                                    $active = ($kategori == $row['id']) ? 'active' : '';
                                ?>
                                    <li><a class="dropdown-item <?= $active ?>" href="?k=<?= e($row['id']) ?>"><?= e($row['name']) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="offcanvas" href="#cart" role="button" aria-controls="cart">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </a>
                        </li>
                    </ul>
                    <form class="d-flex" action="" method="GET">
                        <input class="form-control me-2" type="search" placeholder="Cari Produk" name="s" value="<?= e($search) ?>">
                        <button class="btn btn-primary" type="submit">Cari</button>
                    </form>
                </div>
            </div>
        </nav>
    </header>
    <!-- End Navbar -->

    <!-- Start Content -->
    <main>
        <!-- Start Carousel -->
        <div id="carouselPawshop" class="carousel slide container" data-bs-touch="true">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="./img/carousel1.jpg" class="d-block img-fluid" alt="carousel1">
                    <div class="carousel-caption d-md-block">
                        <h3>Selamat datang di Pawshop</h3>
                        <p>Menjual beragam kebutuhan kucing</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="./img/carousel2.jpg" class="d-block img-fluid" alt="carousel2">
                    <div class="carousel-caption d-md-block">
                        <h3>Selamat datang di Pawshop</h3>
                        <p>Mulai dari makanan, vitamin, mainan kucing</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="./img/carousel5.jpg" class="d-block img-fluid" alt="carousel5">
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
                // Build query with prepared statements
                $page = getPositiveInt('page', 1);
                $offset = ($page - 1) * $item_per_page;

                $params = [];
                $types = '';
                $whereClause = '';

                if (!empty($search)) {
                    $whereClause = "WHERE nama_produk LIKE ?";
                    $searchParam = "%$search%";
                    $params[] = $searchParam;
                    $types .= 's';
                } elseif (!empty($kategori)) {
                    $whereClause = "WHERE category_id = ?";
                    $params[] = $kategori;
                    $types .= 'i';
                }

                // Count total items
                $countSql = "SELECT COUNT(*) as total FROM produk $whereClause";
                $countResult = dbFetchOne($countSql, $types, $params);
                $total_item = $countResult['total'] ?? 0;
                $total_page = max(1, ceil($total_item / $item_per_page));

                // Ensure page is within bounds
                if ($page > $total_page) $page = $total_page;
                if ($page < 1) $page = 1;
                $offset = ($page - 1) * $item_per_page;

                // Fetch products with pagination
                $sql = "SELECT * FROM produk $whereClause LIMIT ? OFFSET ?";
                $fetchParams = array_merge($params, [$item_per_page, $offset]);
                $fetchTypes = $types . 'ii';
                $products = dbFetchAll($sql, $fetchTypes, $fetchParams);

                $prev = $page == 1 ? 'disabled' : '';
                $next = $page >= $total_page ? 'disabled' : '';

                foreach ($products as $row):
                ?>
                    <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-6 col-sm-4 mt-3 d-flex justify-content-center">
                        <div class="card" style="width:300px">
                            <a href="./details.php?id=<?= e($row['id']) ?>">
                                <img class="card-img-top" src="./img/<?= e($row['gambar']) ?>" alt="<?= e($row['nama_produk']) ?>">
                            </a>
                            <div class="card-body">
                                <h4 class="card-title"><?= e($row['nama_produk']) ?></h4>
                                <form action="addToCart.php" method="POST" autocomplete="off">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="product_id" value="<?= e($row['id']) ?>">
                                    <p class="text-end fs-5 fw-bold" style="color: #725F63;">
                                        <u>Rp<?= number_format($row['harga'], 2, ',', '.') ?></u>
                                    </p>
                                    <div class="mb-3">
                                        <label for="quantity-<?= e($row['id']) ?>" class="form-label">Kuantitas:</label>
                                        <input type="number" class="form-control" id="quantity-<?= e($row['id']) ?>" min="1" value="1" max="<?= e($row['stok']) ?>" name="quantity">
                                    </div>
                                    <input type="hidden" name="nama_produk" value="<?= e($row['nama_produk']) ?>">
                                    <input type="hidden" name="harga" value="<?= e($row['harga']) ?>">
                                    <div class="card-text d-flex justify-content-between">
                                        <p>Stok</p>
                                        <p><?= e($row['stok']) ?></p>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary btn-add-to-cart" <?= $row['stok'] <= 0 ? 'disabled' : '' ?>>Masukkan Keranjang</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <!-- End Card -->

        <!-- Start Pagination -->
        <div class="container mt-3 d-flex justify-content-center">
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <li class="page-item">
                        <a class="page-link <?= $prev ?>" href="?page=<?= $page - 1 ?><?= $search ? '&s=' . urlencode($search) : '' ?><?= $kategori ? '&k=' . urlencode($kategori) : '' ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php for ($i = 0; $i < $total_page; $i++):
                        $page_status = ($page == $i + 1) ? "active" : "";
                    ?>
                        <li class="page-item"><a class="page-link <?= $page_status ?>" href="?page=<?= $i + 1 ?><?= $search ? '&s=' . urlencode($search) : '' ?><?= $kategori ? '&k=' . urlencode($kategori) : '' ?>"><?= $i + 1 ?></a></li>
                    <?php endfor; ?>
                    <li class="page-item">
                        <a class="page-link <?= $next ?>" href="?page=<?= $page + 1 ?><?= $search ? '&s=' . urlencode($search) : '' ?><?= $kategori ? '&k=' . urlencode($kategori) : '' ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <!-- End Pagination -->

        <!-- Start SELECTION PAGINATION -->
        <div class="mx-auto d-flex justify-content-center" style="width: 15%;">
            <label for="itemsPerPage" class="me-2">Items per page:</label>
            <select id="itemsPerPage" class="" onchange="changeItemsPerPage()">
                <option value="4" <?= ($item_per_page == 4) ? 'selected' : '' ?>>4</option>
                <option value="8" <?= ($item_per_page == 8) ? 'selected' : '' ?>>8</option>
                <option value="12" <?= ($item_per_page == 12) ? 'selected' : '' ?>>12</option>
            </select>
        </div>
        <!-- End SELECTION PAGINATION -->

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
                            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])):
                                // Get all product IDs from cart to batch fetch stock info
                                $cartIds = array_column($_SESSION['cart'], 'id');
                                $stockInfo = [];
                                if (!empty($cartIds)) {
                                    $placeholders = implode(',', array_fill(0, count($cartIds), '?'));
                                    $stockResult = dbFetchAll(
                                        "SELECT id, stok FROM produk WHERE id IN ($placeholders)",
                                        str_repeat('i', count($cartIds)),
                                        $cartIds
                                    );
                                    foreach ($stockResult as $stock) {
                                        $stockInfo[$stock['id']] = $stock['stok'];
                                    }
                                }

                                foreach ($_SESSION['cart'] as $item):
                                    $subtotal = $item['quantity'] * $item['price'];
                                    $total += $subtotal;
                                    $maxStock = $stockInfo[$item['id']] ?? 999;
                            ?>
                                    <tr>
                                        <td class="text-center">
                                            <a href="removeFromCart.php?product_id=<?= e($item['id']) ?>&csrf_token=<?= urlencode(csrfToken()) ?>">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </td>
                                        <td><?= e($item['name']) ?></td>
                                        <td style="width: 6em;">
                                            <form action="updateCart.php" method="POST">
                                                <?= csrfField() ?>
                                                <input type="hidden" name="product_id" value="<?= e($item['id']) ?>">
                                                <input class="d-block form-control" type="number" name="quantity" max="<?= e($maxStock) ?>" value="<?= e($item['quantity']) ?>" min="1">
                                                <div class="d-block mt-1 text-end">
                                                    <button class="btn btn-primary" type="submit"><i class="fa-solid fa-refresh"></i></button>
                                                </div>
                                            </form>
                                        </td>
                                        <td>Rp <?= number_format($subtotal) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td class="text-center" colspan="4">Keranjang anda kosong!</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-end" colspan="3">Total</th>
                                <th>Rp <?= number_format($total) ?></th>
                            </tr>
                        </tfoot>
                    </table>
                    <form action="checkout.php" method="post">
                        <?= csrfField() ?>
                        <select class="form-select" name="payment_method" id="payment_method" required>
                            <option value="" disabled selected>Metode Pembayaran</option>
                            <option value="Transfer">Transfer</option>
                            <option value="Tunai">Tunai</option>
                        </select>
                        <div class="d-flex justify-content-end">
                            <button name="checkout" class="mt-3 btn btn-success" <?= !isLoggedIn() ? 'disabled title="Silakan login terlebih dahulu"' : '' ?>>Checkout</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Cart -->
    </main>
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
        <?= $cart_status ?>
        <?= $transaction_status ?>
        <?= $transaction_error ?>
        <?php
        unset($_SESSION['cart_status']);
        unset($_SESSION['transaction_status']);
        unset($_SESSION['transaction_error']);
        ?>

        // Reusable function for dropdown hover
        function enableDropdownHover(dropdownId) {
            $(document).ready(function() {
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

        enableDropdownHover('profileDropdown');
        enableDropdownHover('kategoriDropdown');

        // CHANGE PAGINATION ITEMS
        function changeItemsPerPage() {
            var selectedItemsPerPage = document.getElementById('itemsPerPage').value;
            window.location.href = updateQueryStringParameter(window.location.href, 'itemsPerPage', selectedItemsPerPage);
        }

        function updateQueryStringParameter(uri, key, value) {
            var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
            var separator = uri.indexOf('?') !== -1 ? "&" : "?";

            if (uri.match(re)) {
                return uri.replace(re, '$1' + key + "=" + value + '$2');
            } else {
                return uri + separator + key + "=" + value;
            }
        }
    </script>
</body>

</html>
