<?php
include 'config.php';

// Require admin access
requireAdmin();

$status = '';
$wrongpass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!csrfValidate()) {
        $status = '<div class="mt-3 alert alert-danger" role="alert">Sesi tidak valid. Silakan coba lagi.</div>';
    } else {
        // Update privilege
        if (isset($_POST['submit']) && isset($_POST['edit_privilege']) && !empty($_POST['edit_privilege']) && !isset($_POST['delete_username'])) {
            $id = postPositiveInt('id');
            $edit_privilege = post('edit_privilege');

            // Validate privilege value
            if ($id > 0 && in_array($edit_privilege, ['admin', 'user'], true)) {
                $result = dbQuery(
                    "UPDATE users SET privilege = ? WHERE id = ?",
                    'si',
                    [$edit_privilege, $id]
                );
                if ($result) {
                    $status = '<div class="mt-3 alert alert-success" role="alert">Privilege berhasil diubah!</div>';
                } else {
                    $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal mengganti privilege!</div>';
                }
            } else {
                $status = '<div class="mt-3 alert alert-danger" role="alert">Invalid privilege value!</div>';
            }
        }

        // Update password (only if new password fields are filled)
        if (isset($_POST['submit']) && isset($_POST['edit_new_password']) && !empty($_POST['edit_new_password'])) {
            $id = postPositiveInt('id');
            $edit_new_password = $_POST['edit_new_password'] ?? '';
            $edit_new_repassword = $_POST['edit_new_repassword'] ?? '';

            if ($edit_new_password !== $edit_new_repassword) {
                $wrongpass = '<div class="mt-3 alert alert-danger" role="alert">Password does not match!</div>';
            } elseif ($id > 0) {
                // Validate new password
                $passwordErrors = validatePassword($edit_new_password);
                if (!empty($passwordErrors)) {
                    $wrongpass = '<div class="mt-3 alert alert-danger" role="alert">' . e(implode(', ', $passwordErrors)) . '</div>';
                } else {
                    // Update with bcrypt hash
                    $newHash = hashPassword($edit_new_password);
                    $result = dbQuery(
                        "UPDATE users SET password = ? WHERE id = ?",
                        'si',
                        [$newHash, $id]
                    );
                    if ($result) {
                        $status = '<div class="mt-3 alert alert-success" role="alert">Password berhasil diubah!</div>';
                    } else {
                        $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal mengganti password!</div>';
                    }
                }
            }
        }

        // Delete user
        if (isset($_POST['submit']) && isset($_POST['delete_username']) && !empty($_POST['delete_username'])) {
            $id = postPositiveInt('id');

            // Prevent deleting own account
            if ($id === $userid) {
                $status = '<div class="mt-3 alert alert-danger" role="alert">Tidak bisa menghapus akun sendiri!</div>';
            } elseif ($id > 0) {
                $result = dbQuery("DELETE FROM users WHERE id = ?", 'i', [$id]);
                if ($result) {
                    $status = '<div class="mt-3 alert alert-success" role="alert">User has been deleted!</div>';
                } else {
                    $status = '<div class="mt-3 alert alert-danger" role="alert">Failed to delete user!</div>';
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
    <title>Pawshop | Users</title>
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
                        <a class="nav-link" href="./admin-status.php">Status</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="./admin-users.php">Users</a>
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

        <?= $wrongpass; ?>
        <?= $status; ?>

        <!-- Start MODAL EDIT -->
        <div class="modal" id="editUser">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit User</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="POST">
                            <?= csrfField() ?>
                            <input type="hidden" name="id">
                            <div class="form-floating">
                                <input class="form-control" type="text" name="edit_username" id="edit_username" readonly>
                                <label for="edit_username">Username</label>
                            </div>
                            <div class="form-floating mt-2">
                                <input class="form-control" type="password" name="edit_new_password" id="edit_new_password" placeholder="New Password" minlength="6">
                                <label for="edit_new_password">New Password (kosongkan jika tidak diubah)</label>
                            </div>
                            <div class="form-floating mt-2">
                                <input class="form-control" type="password" name="edit_new_repassword" id="edit_new_repassword" placeholder="Retype Password" minlength="6">
                                <label for="edit_new_repassword">Retype Password</label>
                            </div>
                            <div class="form-floating mt-2">
                                <select class="form-select" name="edit_privilege" id="edit_privilege">
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                                <label for="edit_privilege">Privilege</label>
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
        <div class="modal" id="deleteUser">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <p></p>
                    </div>
                    <div class="modal-body">
                        <form class="d-flex justify-content-between" action="" method="POST">
                            <?= csrfField() ?>
                            <input type="hidden" name="id" value="">
                            <input type="hidden" name="delete_username">
                            <input type="submit" name="submit" value="Yes" class="mt-2 btn btn-danger">
                            <button data-bs-dismiss="modal" class="mt-2 btn btn-primary">No</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        <!-- End MODAL DELETE -->
        <div class="table-responsive">

            <table id="usersDataTables" class="table display">
                <thead>
                    <tr>
                        <th>Aksi</th>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Phone</th>
                        <th>Privilege</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $users = dbFetchAll("SELECT id, username, phone_number, privilege FROM users");
                    foreach ($users as $row):
                    ?>
                        <tr>
                            <td>
                                <button class="btn" data-bs-toggle="modal" data-id="<?= e($row['id']); ?>" data-bs-target="#editUser"><i class="fa-solid fa-edit"></i></button>
                                <button class="btn" data-bs-toggle="modal" data-id="<?= e($row['id']); ?>" data-bs-target="#deleteUser"><i class="fa-solid fa-trash"></i></button>
                            </td>
                            <td><?= e($row['id']); ?></td>
                            <td><?= e($row['username']); ?></td>
                            <td><?= e($row['phone_number']); ?></td>
                            <td><?= e($row['privilege']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Aksi</th>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Phone</th>
                        <th>Privilege</th>
                    </tr>
                </tfoot>
            </table>
        </div>
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
        new DataTable('#usersDataTables');
    </script>
    <script>
        // JavaScript to handle the modal show event
        $('#editUser').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var userId = button.data('id');

            // Fetch data using AJAX
            $.ajax({
                url: 'admin-users-fetch.php',
                type: 'GET',
                data: {
                    id: userId
                },
                dataType: 'json',
                success: function(data) {
                    if (!data.error) {
                        $('#editUser input[name="id"]').val(data.id);
                        $('#editUser input[name="edit_username"]').val(data.username);
                        $('#editUser select[name="edit_privilege"]').val(data.privilege);
                        // Clear password fields
                        $('#editUser input[name="edit_new_password"]').val('');
                        $('#editUser input[name="edit_new_repassword"]').val('');
                    } else {
                        console.log('Error fetching user:', data.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                }
            });
        });

        // JavaScript to handle the modal show event
        $('#deleteUser').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var userId = button.data('id');

            // Fetch data using AJAX
            $.ajax({
                url: 'admin-users-fetch.php',
                type: 'GET',
                data: {
                    id: userId
                },
                dataType: 'json',
                success: function(data) {
                    if (!data.error) {
                        $('#deleteUser .modal-header p').text('Apakah anda yakin ingin menghapus ' + data.username + '?');
                        $('#deleteUser input[name="id"]').val(data.id);
                        $('#deleteUser input[name="delete_username"]').val(data.username);
                    } else {
                        console.log('Error fetching user:', data.error);
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
