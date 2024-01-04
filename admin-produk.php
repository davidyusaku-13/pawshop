<?php
session_start();
include 'config.php';

if ($_SESSION['privilege'] != 'admin') {
    header('Location: index.php');
}

$status = '';
$file = '';
$kategori_status = '';

if (isset($_POST['submit'])) {
    // ADD DATA
    if (isset($_FILES) && isset($_POST['product_name']) && isset($_POST['stok']) && isset($_POST['harga']) && isset($_POST['detail'])) {
        // AMBIL DATA DARI FORM
        $gambar = basename($_FILES["gambar"]["name"]);
        $product_name = $_POST['product_name'];
        $category_id = $_POST['category_id'];
        $stok = $_POST['stok'];
        $harga = $_POST['harga'];
        $detail = $_POST['detail'];

        // UPDATE DATABASE
        if ($gambar == "") {
            $sql = "INSERT INTO produk (gambar, nama_produk, category_id, stok, harga, detail) VALUES ('x.jpg', '$product_name', $category_id, $stok, $harga, '$detail')";
        } else {
            $sql = "INSERT INTO produk (gambar, nama_produk, category_id, stok, harga, detail) VALUES ('$gambar', '$product_name', $category_id, $stok, $harga, '$detail')";

            // AMBIL DATA GAMBAR
            $target_dir = "./img/";
            $target_file = $target_dir . basename($_FILES["gambar"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["gambar"]["tmp_name"]);
            if ($check !== false) {
                $file = '<div class="mt-3 alert alert-success" role="alert">File adalah gambar!</div>';
                $uploadOk = 1;
            } else {
                // echo "File is not an image.";
                $file = '<div class="mt-3 alert alert-danger" role="alert">File bukan gambar!</div>';
                $uploadOk = 0;
            }
            if ($uploadOk == 0) {
                // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                    // FINAL UPLOAD FILE SETELAH CHECK MACEM2
                    $file = '<div class="mt-3 alert alert-success" role="alert">File upload berhasil!</div>';
                } else {
                    $file = '<div class="mt-3 alert alert-danger" role="alert">File upload gagal!</div>';
                }
            }
        }

        if (mysqli_query($conn, $sql)) {
            $status = '<div class="mt-3 alert alert-success" role="alert">Produk berhasil ditambahkan!</div>';
        } else {
            $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal menambahkan produk!</div>';
        }
    }

    // EDIT DATA
    if (isset($_POST['id']) && isset($_POST['edit_product_name']) && isset($_POST['edit_category_id']) && isset($_POST['edit_stok']) && isset($_POST['edit_harga']) && isset($_POST['edit_detail'])) {
        // AMBIL DATA DARI FORM
        $id = $_POST['id'];
        $edit_gambar = basename($_FILES["edit_gambar"]["name"]);
        $edit_product_name = $_POST['edit_product_name'];
        $edit_category_id = $_POST['edit_category_id'];
        $edit_stok = $_POST['edit_stok'];
        $edit_harga = $_POST['edit_harga'];
        $edit_detail = $_POST['edit_detail'];

        // UPDATE DATABASE
        if ($edit_gambar == "") {
            $sql = "UPDATE produk SET nama_produk='$edit_product_name', category_id='$edit_category_id', stok='$edit_stok', harga='$edit_harga', detail='$edit_detail' WHERE id=$id";
        } else {
            $sql = "UPDATE produk SET gambar='$edit_gambar', nama_produk='$edit_product_name', category_id='$edit_category_id', stok='$edit_stok', harga='$edit_harga', detail='$edit_detail' WHERE id=$id";

            // AMBIL DATA GAMBAR
            $target_dir = "./img/";
            $target_file = $target_dir . basename($_FILES["edit_gambar"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["edit_gambar"]["tmp_name"]);
            if ($check !== false) {
                $file = '<div class="mt-3 alert alert-success" role="alert">File adalah gambar!</div>';
                $uploadOk = 1;
            } else {
                // echo "File is not an image.";
                $file = '<div class="mt-3 alert alert-danger" role="alert">File bukan gambar!</div>';
                $uploadOk = 0;
            }
            if ($uploadOk == 0) {
                // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["edit_gambar"]["tmp_name"], $target_file)) {
                    // FINAL UPLOAD FILE SETELAH CHECK MACEM2
                    $file = '<div class="mt-3 alert alert-success" role="alert">File upload berhasil!</div>';
                } else {
                    $file = '<div class="mt-3 alert alert-danger" role="alert">File upload gagal!</div>';
                }
            }
        }

        if (mysqli_query($conn, $sql)) {
            $status = '<div class="mt-3 alert alert-success" role="alert">Produk berhasil diubah!</div>';
        } else {
            $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal mengubah produk!</div>';
        }
    }

    // DELETE DATA
    if (isset($_POST['delete_product_name']) && $_POST['delete_product_name'] != null) {
        $id = $_POST['id'];
        $sql = "DELETE FROM produk WHERE id=$id";
        $status = "";
        if (mysqli_query($conn, $sql)) {
            $status = '<div class="mt-3 alert alert-success" role="alert">Produk berhasil dihapus!</div>';
        } else {
            $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal menghapus produk!</div>';
        }
    }
}

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
    <script src="./js/datatables.min.js"></script>
    <script src="./js/jquery-3.7.1.min.js"></script>
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
                            <input class="form-control" type="file" name="gambar">
                            <div class="form-floating">
                                <input class="mt-2 form-control" type="text" name="product_name" placeholder="Nama Produk" required>
                                <label for="product_name">Nama Produk</label>
                            </div>
                            <div class="form-floating">
                                <select class="mt-2 form-select" name="category_id" required>
                                    <option value="" disabled selected>Pilih kategori</option>
                                    <?php
                                    $sql = 'SELECT * FROM kategori';
                                    $res = mysqli_query($conn, $sql);
                                    if (mysqli_num_rows($res) > 0) {
                                        while ($row = mysqli_fetch_assoc($res)) {
                                    ?>
                                            <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
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
                                <input type="submit" name="submit" class="mt-2 btn btn-primary"></input>
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
                            <input class="form-control" type="file" name="edit_gambar">
                            <input type="hidden" name="id">
                            <div class="form-floating">
                                <input class="mt-2 form-control" type="text" name="edit_product_name" placeholder="Nama Produk" required>
                                <label for="edit_product_name">Nama Produk</label>
                            </div>
                            <div class="form-floating">
                                <select class="mt-2 form-select" name="edit_category_id" required>
                                    <option value="" disabled selected>Pilih kategori</option>
                                    <?php
                                    $sql = 'SELECT * FROM kategori';
                                    $res = mysqli_query($conn, $sql);
                                    if (mysqli_num_rows($res) > 0) {
                                        while ($row = mysqli_fetch_assoc($res)) {
                                    ?>
                                            <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
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
                                <input type="submit" name="submit" class="mt-2 btn btn-primary"></input>
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
                            <input type="hidden" name="id" value="">
                            <input type="hidden" name="delete_product_name">
                            <input type="submit" name="submit" value="Yes" class="mt-2 btn btn-danger"></input>
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
                    $fetch = "SELECT p.id, p.gambar, p.nama_produk, c.name, p.stok, p.harga, p.detail FROM produk p JOIN kategori c ON p.category_id=c.id";
                    $res = mysqli_query($conn, $fetch);

                    if (mysqli_num_rows($res) > 0) {
                        while ($row = mysqli_fetch_assoc($res)) {
                    ?>
                            <tr>
                                <td>
                                    <button class="btn" data-bs-toggle="modal" data-id="<?= $row['id']; ?>" data-bs-target="#editProduct"><i class="fa-solid fa-edit"></i></button>
                                    <button class="btn" data-bs-toggle="modal" data-id="<?= $row['id']; ?>" data-bs-target="#deleteProduct"><i class="fa-solid fa-trash"></i></button>
                                </td>
                                <td><?= $row['gambar']; ?></td>
                                <td><?= $row['nama_produk']; ?></td>
                                <td><?= $row['name']; ?></td>
                                <td><?= $row['stok']; ?></td>
                                <td>Rp <?= number_format($row['harga']); ?></td>
                                <td><?= $row['detail']; ?></td>
                            </tr>
                    <?php
                        }
                    }
                    ?>

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
                        // You can update other modal fields as needed
                    } else {
                        // Handle errors
                        console.log('Error fetching product:', data.error);
                    }
                },
                error: function(xhr, status, error) {
                    // Handle AJAX errors
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
                        // Set the text of the paragraph element in the modal header
                        $('#deleteProduct .modal-header p').text('Apakah anda yakin ingin menghapus ' + data.nama_produk + '?');

                        $('#deleteProduct input[name="id"]').val(data.id);
                        $('#deleteProduct input[name="delete_product_name"]').val(data.nama_produk);
                        // You can update other modal fields as needed
                    } else {
                        // Handle errors
                        console.log('Error fetching product:', data.error);
                    }
                },
                error: function(xhr, status, error) {
                    // Handle AJAX errors
                    console.error('AJAX error:', status, error);
                }
            });
        });
    </script>
</body>

</html>