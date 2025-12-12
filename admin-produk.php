<?php
include 'config.php';

// Require admin access
requireAdmin();

$status = '';
$file = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!csrfValidate()) {
        $status = '<div class="mt-3 alert alert-danger" role="alert">Sesi tidak valid. Silakan coba lagi.</div>';
    } else {
        // ADD DATA
        if (isset($_POST['submit']) && isset($_POST['product_name']) && !isset($_POST['id']) && !isset($_POST['delete_product_name'])) {
            $product_name = post('product_name');
            $category_id = postPositiveInt('category_id');
            $stok = postPositiveInt('stok');
            $harga = postPositiveInt('harga');
            $detail = post('detail');

            if (!empty($product_name) && $category_id > 0) {
                $gambar = 'x.jpg'; // default

                // Handle file upload if present
                if (isset($_FILES['gambar']) && $_FILES['gambar']['name'] != '') {
                    $errors = validateImageUpload($_FILES['gambar']);
                    if (empty($errors)) {
                        $newFilename = generateSafeFilename($_FILES['gambar']['name']);
                        $targetPath = UPLOAD_DIR . $newFilename;
                        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetPath)) {
                            $gambar = $newFilename;
                            $file = '<div class="mt-3 alert alert-success" role="alert">File upload berhasil!</div>';
                        } else {
                            $file = '<div class="mt-3 alert alert-danger" role="alert">File upload gagal!</div>';
                        }
                    } else {
                        $file = '<div class="mt-3 alert alert-danger" role="alert">' . e(implode(', ', $errors)) . '</div>';
                    }
                }

                $result = dbQuery(
                    "INSERT INTO produk (gambar, nama_produk, category_id, stok, harga, detail) VALUES (?, ?, ?, ?, ?, ?)",
                    'ssiiss',
                    [$gambar, $product_name, $category_id, $stok, $harga, $detail]
                );

                if ($result) {
                    $status = '<div class="mt-3 alert alert-success" role="alert">Produk berhasil ditambahkan!</div>';
                } else {
                    $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal menambahkan produk!</div>';
                }
            }
        }

        // EDIT DATA
        if (isset($_POST['submit']) && isset($_POST['id']) && isset($_POST['edit_product_name'])) {
            $id = postPositiveInt('id');
            $edit_product_name = post('edit_product_name');
            $edit_category_id = postPositiveInt('edit_category_id');
            $edit_stok = postPositiveInt('edit_stok');
            $edit_harga = postPositiveInt('edit_harga');
            $edit_detail = post('edit_detail');

            if ($id > 0 && !empty($edit_product_name) && $edit_category_id > 0) {
                // Handle file upload if present
                if (isset($_FILES['edit_gambar']) && $_FILES['edit_gambar']['name'] != '') {
                    $errors = validateImageUpload($_FILES['edit_gambar']);
                    if (empty($errors)) {
                        $newFilename = generateSafeFilename($_FILES['edit_gambar']['name']);
                        $targetPath = UPLOAD_DIR . $newFilename;
                        if (move_uploaded_file($_FILES['edit_gambar']['tmp_name'], $targetPath)) {
                            dbQuery(
                                "UPDATE produk SET gambar = ?, nama_produk = ?, category_id = ?, stok = ?, harga = ?, detail = ? WHERE id = ?",
                                'ssiiisi',
                                [$newFilename, $edit_product_name, $edit_category_id, $edit_stok, $edit_harga, $edit_detail, $id]
                            );
                            $file = '<div class="mt-3 alert alert-success" role="alert">File upload berhasil!</div>';
                            $status = '<div class="mt-3 alert alert-success" role="alert">Produk berhasil diubah!</div>';
                        } else {
                            $file = '<div class="mt-3 alert alert-danger" role="alert">File upload gagal!</div>';
                        }
                    } else {
                        $file = '<div class="mt-3 alert alert-danger" role="alert">' . e(implode(', ', $errors)) . '</div>';
                    }
                } else {
                    // Update without changing image
                    $result = dbQuery(
                        "UPDATE produk SET nama_produk = ?, category_id = ?, stok = ?, harga = ?, detail = ? WHERE id = ?",
                        'siiisi',
                        [$edit_product_name, $edit_category_id, $edit_stok, $edit_harga, $edit_detail, $id]
                    );
                    if ($result) {
                        $status = '<div class="mt-3 alert alert-success" role="alert">Produk berhasil diubah!</div>';
                    } else {
                        $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal mengubah produk!</div>';
                    }
                }
            }
        }

        // DELETE DATA
        if (isset($_POST['submit']) && isset($_POST['delete_product_name']) && !empty($_POST['delete_product_name'])) {
            $id = postPositiveInt('id');
            if ($id > 0) {
                $result = dbQuery("DELETE FROM produk WHERE id = ?", 'i', [$id]);
                if ($result) {
                    $status = '<div class="mt-3 alert alert-success" role="alert">Produk berhasil dihapus!</div>';
                } else {
                    $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal menghapus produk!</div>';
                }
            }
        }
    }
}

// Fetch categories once for dropdowns
$categories = dbFetchAll("SELECT id, name FROM kategori");
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pawshop | Produk</title>
    <link rel="icon" type="image/x-icon" href="./logo-title.png">
    <link href="./css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/datatables.min.css">
    <script src="./js/jquery-3.7.1.min.js"></script>
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
                        <a class="nav-link active" href="./admin-produk.php">Produk</a>
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
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="./logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="./login.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <!-- End Navbar -->

    <!-- Start Content -->
    <div class="container mt-3">

        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newProduct">Tambah</button>
        <?= $file; ?>
        <?= $status; ?>

        <!-- Start MODAL TAMBAH PRODUK -->
        <div class="modal" id="newProduct">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Tambah Produk</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="POST" enctype="multipart/form-data" autocomplete="off">
                            <?= csrfField() ?>
                            <input class="form-control" type="file" name="gambar">
                            <div class="form-floating">
                                <input class="mt-2 form-control" type="text" name="product_name" placeholder="Nama Produk" required>
                                <label for="product_name">Nama Produk</label>
                            </div>
                            <div class="form-floating">
                                <select class="mt-2 form-select" name="category_id" required>
                                    <option value="" disabled selected>Pilih kategori</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= e($cat['id']) ?>"><?= e($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="category_id">Kategori</label>
                            </div>
                            <div class="form-floating">
                                <input class="mt-2 form-control" type="number" min="0" name="stok" placeholder="Stok" required>
                                <label for="stok">Stok</label>
                            </div>
                            <div class="form-floating">
                                <input class="mt-2 form-control" type="number" min="0" name="harga" placeholder="Harga" required>
                                <label for="harga">Harga</label>
                            </div>
                            <div class="form-floating">
                                <textarea class="mt-2 form-control" rows="3" name="detail" placeholder="Detail Produk"></textarea>
                                <label for="detail" class="form-label">Detail Produk</label>
                            </div>
                            <div class="d-flex justify-content-end">
                                <input type="submit" name="submit" class="mt-2 btn btn-primary">
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        <!-- End MODAL TAMBAH PRODUK -->

        <!-- Start MODAL EDIT -->
        <div class="modal" id="editProduct">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Produk</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="POST" enctype="multipart/form-data" autocomplete="off">
                            <?= csrfField() ?>
                            <input class="form-control" type="file" name="edit_gambar">
                            <input type="hidden" name="id">
                            <div class="form-floating">
                                <input class="mt-2 form-control" type="text" name="edit_product_name" placeholder="Nama Produk" required>
                                <label for="edit_product_name">Nama Produk</label>
                            </div>
                            <div class="form-floating">
                                <select class="mt-2 form-select" name="edit_category_id" required>
                                    <option value="" disabled selected>Pilih kategori</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= e($cat['id']) ?>"><?= e($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="edit_category_id">Kategori</label>
                            </div>
                            <div class="form-floating">
                                <input class="mt-2 form-control" type="number" min="0" name="edit_stok" placeholder="Stok" required>
                                <label for="edit_stok">Stok</label>
                            </div>
                            <div class="form-floating">
                                <input class="mt-2 form-control" type="number" min="0" name="edit_harga" placeholder="Harga" required>
                                <label for="edit_harga">Harga</label>
                            </div>
                            <div class="form-floating">
                                <textarea class="mt-2 form-control" rows="3" name="edit_detail" placeholder="Detail Produk"></textarea>
                                <label for="edit_detail" class="form-label">Detail Produk</label>
                            </div>
                            <div class="d-flex justify-content-end">
                                <input type="submit" name="submit" class="mt-2 btn btn-primary">
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        <!-- End MODAL EDIT -->

        <!-- Start MODAL DELETE -->
        <div class="modal" id="deleteProduct">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <p></p>
                    </div>
                    <div class="modal-body">
                        <form class="d-flex justify-content-between" action="" method="POST">
                            <?= csrfField() ?>
                            <input type="hidden" name="id" value="">
                            <input type="hidden" name="delete_product_name">
                            <input type="submit" name="submit" value="Yes" class="mt-2 btn btn-danger">
                            <button data-bs-dismiss="modal" class="mt-2 btn btn-primary">No</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        <!-- End MODAL DELETE -->

        <!-- Start Table -->
        <div class="mt-3 table-responsive">
            <table id="productsDataTables" class="table display">
                <thead>
                    <tr>
                        <th>Aksi</th>
                        <th>Gambar</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Harga</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $products = dbFetchAll("SELECT p.id, p.gambar, p.nama_produk, c.name, p.stok, p.harga, p.detail FROM produk p JOIN kategori c ON p.category_id = c.id");
                    foreach ($products as $row):
                    ?>
                        <tr>
                            <td>
                                <button class="btn" data-bs-toggle="modal" data-id="<?= e($row['id']); ?>" data-bs-target="#editProduct"><i class="fa-solid fa-edit"></i></button>
                                <button class="btn" data-bs-toggle="modal" data-id="<?= e($row['id']); ?>" data-bs-target="#deleteProduct"><i class="fa-solid fa-trash"></i></button>
                            </td>
                            <td><?= e($row['gambar']); ?></td>
                            <td><?= e($row['nama_produk']); ?></td>
                            <td><?= e($row['name']); ?></td>
                            <td><?= e($row['stok']); ?></td>
                            <td>Rp <?= e(number_format($row['harga'])); ?></td>
                            <td><?= e($row['detail']); ?></td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
                <tfoot>
                    <tr>
                        <th>Aksi</th>
                        <th>Gambar</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Harga</th>
                        <th>Detail</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- End Table -->
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
    <script src="./js/jquery.dataTables.min.js"></script>
    <script>
        new DataTable('#productsDataTables');
    </script>
    <script>
        // JavaScript to handle the modal show event
        $('#editProduct').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var productId = button.data('id');

            // Fetch data using AJAX
            $.ajax({
                url: 'admin-produk-fetch.php',
                type: 'GET',
                data: {
                    id: productId
                },
                dataType: 'json',
                success: function(data) {
                    // Update the modal content with the fetched data
                    if (!data.error) {
                        $('#editProduct input[name="id"]').val(data.id);
                        $('#editProduct input[name="edit_product_name"]').val(data.nama_produk);
                        $('#editProduct select[name="edit_category_id"]').val(data.category_id);
                        $('#editProduct input[name="edit_stok"]').val(data.stok);
                        $('#editProduct input[name="edit_harga"]').val(data.harga);
                        $('#editProduct textarea[name="edit_detail"]').val(data.detail);
                    } else {
                        console.log('Error fetching product:', data.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                }
            });
        });

        // JavaScript to handle the modal show event
        $('#deleteProduct').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var productId = button.data('id');

            // Fetch data using AJAX
            $.ajax({
                url: 'admin-produk-fetch.php',
                type: 'GET',
                data: {
                    id: productId
                },
                dataType: 'json',
                success: function(data) {
                    // Update the modal content with the fetched data
                    if (!data.error) {
                        $('#deleteProduct .modal-header p').text('Apakah anda yakin ingin menghapus ' + data.nama_produk + '?');
                        $('#deleteProduct input[name="id"]').val(data.id);
                        $('#deleteProduct input[name="delete_product_name"]').val(data.nama_produk);
                    } else {
                        console.log('Error fetching product:', data.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                }
            });
        });
    </script>
</body>

</html>
