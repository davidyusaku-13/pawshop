<?php
include 'config.php';

if ($privilege != 'admin') {
    header('Location: index.php');
}

$status = '';
$wrongpass = '';

if (isset($_POST['submit'])) {
    if (isset($_POST['edit_privilege']) && $_POST['edit_privilege'] != null) {
        $id = $_POST['id'];
        $edit_privilege = $_POST['edit_privilege'];
        $sql = "UPDATE users SET privilege='$edit_privilege' WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            $status = '<div class="mt-3 alert alert-success" role="alert">Privilege berhasil diubah!</div>';
        } else {
            $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal mengganti privilege!</div>';
        }
    }
    if (isset($_POST['edit_new_password']) && $_POST['edit_new_password'] != null && isset($_POST['edit_new_repassword']) && $_POST['edit_new_repassword'] != null) {
        $id = $_POST['id'];
        $edit_username = $_POST['edit_username'];
        $edit_new_password = md5($_POST['edit_new_password']);
        $edit_new_repassword = md5($_POST['edit_new_repassword']);
        if ($edit_new_password != $edit_new_repassword) {
            $wrongpass = '<div class="mt-3 alert alert-danger" role="alert">Password does not match!</div>';
        } else {
            $sql = "UPDATE users SET username='$edit_username', password='$edit_new_password' WHERE id=$id";
            if (mysqli_query($conn, $sql)) {
                $status = '<div class="mt-3 alert alert-success" role="alert">Password berhasil diubah!</div>';
            } else {
                $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal mengganti password!</div>';
            }
        }
    }
    if (isset($_POST['delete_username']) && $_POST['delete_username'] != null) {
        $id = $_POST['id'];
        $sql = "DELETE FROM users WHERE id=$id";
        $status = "";
        if (mysqli_query($conn, $sql)) {
            $status = '<div class="mt-3 alert alert-success" role="alert">User has been deleted!</div>';
        } else {
            $status = '<div class="mt-3 alert alert-danger" role="alert">Failed to delete user!</div>';
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
                            <input type="hidden" name="id">
                            <input class="form-control" type="text" name="edit_username" required>
                            <input class="mt-2 form-control" type="password" name="edit_old_password" required>
                            <input class="mt-2 form-control" type="password" name="edit_new_password" placeholder="New Password">
                            <input class="mt-2 form-control" type="password" name="edit_new_repassword" placeholder="Retype Password">
                            <input class="mt-2 form-control" type="text" name="edit_privilege" placeholder="Privilege">
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
        <div class="modal" id="deleteUser">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <p></p>
                    </div>
                    <div class="modal-body">
                        <form class="d-flex justify-content-between" action="" method="POST">
                            <input type="hidden" name="id" value="">
                            <input type="hidden" name="delete_username">
                            <input type="submit" name="submit" value="Yes" class="mt-2 btn btn-danger"></input>
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
                        <th>Password</th>
                        <th>Privilege</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $fetch = "SELECT * FROM users";
                    $res = mysqli_query($conn, $fetch);

                    if (mysqli_num_rows($res) > 0) {
                        while ($row = mysqli_fetch_assoc($res)) {
                    ?>
                            <tr>
                                <td>
                                    <button class="btn" data-bs-toggle="modal" data-id="<?= $row['id']; ?>" data-bs-target="#editUser"><i class="fa-solid fa-edit"></i></button>
                                    <button class="btn" data-bs-toggle="modal" data-id="<?= $row['id']; ?>" data-bs-target="#deleteUser"><i class="fa-solid fa-trash"></i></button>
                                </td>
                                <td><?= $row['id']; ?></td>
                                <td><?= $row['username']; ?></td>
                                <td><?= $row['password']; ?></td>
                                <td><?= $row['privilege']; ?></td>
                            </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
                <tfoot>
                    <th>Aksi</th>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Privilege</th>
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
                    // Update the modal content with the fetched data
                    if (!data.error) {
                        $('#editUser input[name="id"]').val(data.id);
                        $('#editUser input[name="edit_username"]').val(data.username);
                        $('#editUser input[name="edit_old_password"]').val(data.password);
                        $('#editUser input[name="edit_privilege"]').val(data.privilege);
                        // You can update other modal fields as needed
                    } else {
                        // Handle errors
                        console.log('Error fetching user:', data.error);
                    }
                },
                error: function(xhr, status, error) {
                    // Handle AJAX errors
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
                    // Update the modal content with the fetched data
                    if (!data.error) {
                        // Set the text of the paragraph element in the modal header
                        $('#deleteUser .modal-header p').text('Apakah anda yakin ingin menghapus ' + data.username + '?');

                        $('#deleteUser input[name="id"]').val(data.id);
                        $('#deleteUser input[name="delete_username"]').val(data.username);
                        // You can update other modal fields as needed
                    } else {
                        // Handle errors
                        console.log('Error fetching user:', data.error);
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