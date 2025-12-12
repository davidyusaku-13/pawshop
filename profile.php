<?php
include 'config.php';

// Require login
requireLogin();

$total = 0;
$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!csrfValidate()) {
        $status = '<div class="mt-3 alert alert-danger" role="alert">Sesi tidak valid. Silakan coba lagi.</div>';
    } else {
        $id = postPositiveInt('id');
        $oldpassword = $_POST['oldpassword'] ?? '';
        $newpassword = $_POST['newpassword'] ?? '';
        $newrepassword = $_POST['newrepassword'] ?? '';

        // Only process if all password fields are filled
        if (!empty($oldpassword) && !empty($newpassword) && !empty($newrepassword)) {
            // Verify current user ID matches session
            if ($id !== $userid) {
                $status = '<div class="mt-3 alert alert-danger" role="alert">Akses tidak diizinkan!</div>';
            } else {
                // Get current password hash
                $user = dbFetchOne("SELECT password FROM users WHERE id = ?", 'i', [$id]);

                if (!$user) {
                    $status = '<div class="mt-3 alert alert-danger" role="alert">User tidak ditemukan!</div>';
                } else {
                    // Verify old password
                    $verification = verifyPassword($oldpassword, $user['password']);

                    if ($verification === 'invalid') {
                        $status = '<div class="mt-3 alert alert-danger" role="alert">Password lama salah!</div>';
                    } elseif ($newpassword !== $newrepassword) {
                        $status = '<div class="mt-3 alert alert-danger" role="alert">Password baru tidak sama!</div>';
                    } else {
                        // Validate new password
                        $passwordErrors = validatePassword($newpassword);
                        if (!empty($passwordErrors)) {
                            $status = '<div class="mt-3 alert alert-danger" role="alert">' . e(implode(', ', $passwordErrors)) . '</div>';
                        } else {
                            // Update password with bcrypt
                            $newHash = hashPassword($newpassword);
                            $result = dbQuery(
                                "UPDATE users SET password = ? WHERE id = ?",
                                'si',
                                [$newHash, $id]
                            );

                            if ($result) {
                                $status = '<div class="mt-3 alert alert-success" role="alert">Password berhasil diubah!</div>';
                                csrfRegenerate();
                            } else {
                                $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal mengubah password!</div>';
                            }
                        }
                    }
                }
            }
        } else {
            $status = '<div class="mt-3 alert alert-info" role="alert">Tidak ada perubahan.</div>';
        }
    }
}

// Get user data
$user = dbFetchOne("SELECT id, username, phone_number FROM users WHERE id = ?", 'i', [$userid]);
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
                            <li><a class="dropdown-item active" href="./profile.php">Profil</a></li>
                            <li><a class="dropdown-item" href="./transactions.php">Transaksi</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <?php if (isLoggedIn()): ?>
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
                    <li class="nav-item dropdown" id="kategoriDropdown">
                        <button class="nav-link dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Kategori
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="./index.php?k=">Semua Produk</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <?php
                            $categories = dbFetchAll("SELECT * FROM kategori");
                            foreach ($categories as $row):
                            ?>
                                <li><a class="dropdown-item" href="index.php?k=<?= e($row['id']) ?>"><?= e($row['name']) ?></a></li>
                            <?php endforeach; ?>
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
    <!-- End Navbar -->

    <!-- Start Content -->
    <div class="container">
        <div class="row">
            <?php if ($user): ?>
                <!-- Card -->
                <div class="my-4 d-flex justify-content-center">
                    <div class="card" style="width:300px">
                        <img class="card-img-top" src="./avatar.png" alt="Avatar">
                        <div class="card-body">
                            <form action="" method="POST" autocomplete="off">
                                <?= csrfField() ?>
                                <input type="hidden" name="id" value="<?= e($user['id']) ?>">
                                <input class="form-control" type="text" name="username" value="<?= e($user['username']) ?>" readonly>
                                <input class="mt-3 form-control" type="password" name="oldpassword" placeholder="Password Lama">
                                <input class="mt-3 form-control" type="password" name="newpassword" placeholder="Password Baru" minlength="6">
                                <input class="mt-3 form-control" type="password" name="newrepassword" placeholder="Ulangi Password Baru" minlength="6">
                                <?= $status ?>
                                <div class="mt-3 d-flex justify-content-between">
                                    <button class="btn btn-info" type="submit" name="submit">Update</button>
                                    <button class="btn btn-danger" type="reset">Clear</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Card -->
            <?php endif; ?>
        </div>
    </div>

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
                                    <td style="width: 10em;">
                                        <form class="d-flex justify-content-between" action="updateCart.php" method="POST">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="product_id" value="<?= e($item['id']) ?>">
                                            <input class="form-control" type="number" name="quantity" max="<?= e($maxStock) ?>" value="<?= e($item['quantity']) ?>" min="1">
                                            <button class="ms-1 btn btn-primary" type="submit"><i class="fa-solid fa-refresh"></i></button>
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
                        <button name="checkout" class="mt-3 btn btn-success">Checkout</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Cart -->

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
    <script src="./js/jquery-3.7.1.min.js"></script>
    <script>
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
    </script>
</body>

</html>
