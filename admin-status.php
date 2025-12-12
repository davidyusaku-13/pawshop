<?php
include 'config.php';

// Require admin access
requireAdmin();

$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!csrfValidate()) {
        $status = '<div class="mt-3 alert alert-danger" role="alert">Sesi tidak valid. Silakan coba lagi.</div>';
    } else {
        // Add status
        if (isset($_POST['submit']) && isset($_POST['status_name']) && !empty($_POST['status_name']) && !isset($_POST['id'])) {
            $status_name = post('status_name');
            if (!empty($status_name)) {
                $result = dbQuery("INSERT INTO status (name) VALUES (?)", 's', [$status_name]);
                if ($result) {
                    $status = '<div class="mt-3 alert alert-success" role="alert">Status berhasil ditambahkan!</div>';
                } else {
                    $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal menambahkan status!</div>';
                }
            }
        }

        // Edit status
        if (isset($_POST['submit']) && isset($_POST['edit_status_name']) && !empty($_POST['edit_status_name'])) {
            $id = postPositiveInt('id');
            $edit_status_name = post('edit_status_name');
            if ($id > 0 && !empty($edit_status_name)) {
                $result = dbQuery("UPDATE status SET name = ? WHERE id = ?", 'si', [$edit_status_name, $id]);
                if ($result) {
                    $status = '<div class="mt-3 alert alert-success" role="alert">Edit berhasil!</div>';
                } else {
                    $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal mengedit!</div>';
                }
            }
        }

        // Delete status
        if (isset($_POST['submit']) && isset($_POST['delete_status_name']) && !empty($_POST['delete_status_name'])) {
            $id = postPositiveInt('id');
            if ($id > 0) {
                $result = dbQuery("DELETE FROM status WHERE id = ?", 'i', [$id]);
                if ($result) {
                    $status = '<div class="mt-3 alert alert-success" role="alert">Status berhasil dihapus!</div>';
                } else {
                    $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal menghapus status!</div>';
                }
            }
        }
    }
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pawshop | Status</title>
    <link rel="icon" type="image/x-icon" href="./logo-title.png">
    <link href="./css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/datatables.min.css">
    <script src="./js/datatables.min.js"></script>
    <script src="./js/jquery-3.7.1.min.js"></script>
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
                        <a class="nav-link active" href="./admin-status.php">Status</a>
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

        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newStatus">Tambah</button>
        <?= $status; ?>

        <!-- Start MODAL TAMBAH -->
        <div class="modal" id="newStatus">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Tambah Status</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="POST">
                            <?= csrfField() ?>
                            <div class="form-floating">
                                <input class="form-control" type="text" name="status_name" placeholder="Nama Status" required>
                                <label for="status_name">Nama Status</label>
                            </div>
                            <div class="d-flex justify-content-end">
                                <input type="submit" name="submit" class="mt-2 btn btn-primary">
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        <!-- End MODAL TAMBAH -->

        <!-- Start MODAL EDIT -->
        <div class="modal" id="editStatus">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Status</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="POST">
                            <?= csrfField() ?>
                            <input type="hidden" name="id" value="">
                            <div class="form-floating">
                                <input class="form-control" type="text" name="edit_status_name" value="" required>
                                <label for="edit_status_name">Nama Status</label>
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
        <div class="modal" id="deleteStatus">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <p></p>
                    </div>
                    <div class="modal-body">
                        <form class="d-flex justify-content-between" action="" method="POST">
                            <?= csrfField() ?>
                            <input type="hidden" name="id" value="">
                            <input type="hidden" name="delete_status_name">
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
            <table id="statusDataTables" class="table display">
                <thead>
                    <tr>
                        <th>Aksi</th>
                        <th>ID</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $statuses = dbFetchAll("SELECT * FROM status");
                    foreach ($statuses as $row):
                    ?>
                        <tr>
                            <td>
                                <button class="btn" data-bs-toggle="modal" data-id="<?= e($row['id']); ?>" data-bs-target="#editStatus"><i class="fa-solid fa-edit"></i></button>
                                <button class="btn" data-bs-toggle="modal" data-id="<?= e($row['id']); ?>" data-bs-target="#deleteStatus"><i class="fa-solid fa-trash"></i></button>
                            </td>
                            <td><?= e($row['id']); ?></td>
                            <td><?= e($row['name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Aksi</th>
                        <th>ID</th>
                        <th>Status</th>
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
        new DataTable('#statusDataTables');

        // JavaScript to handle the modal show event
        $('#editStatus').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var statusId = button.data('id');

            $.ajax({
                url: 'admin-status-fetch.php',
                type: 'GET',
                data: { id: statusId },
                dataType: 'json',
                success: function(data) {
                    if (!data.error) {
                        $('#editStatus input[name="id"]').val(data.id);
                        $('#editStatus input[name="edit_status_name"]').val(data.name);
                    } else {
                        console.log('Error fetching status:', data.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                }
            });
        });

        $('#deleteStatus').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var statusId = button.data('id');

            $.ajax({
                url: 'admin-status-fetch.php',
                type: 'GET',
                data: { id: statusId },
                dataType: 'json',
                success: function(data) {
                    if (!data.error) {
                        $('#deleteStatus .modal-header p').text('Apakah anda yakin ingin menghapus ' + data.name + '?');
                        $('#deleteStatus input[name="id"]').val(data.id);
                        $('#deleteStatus input[name="delete_status_name"]').val(data.name);
                    } else {
                        console.log('Error fetching status:', data.error);
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
