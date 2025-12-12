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
        // Edit transaction status
        if (isset($_POST['submit']) && isset($_POST['editID']) && isset($_POST['edit_status'])) {
            $editID = postPositiveInt('editID');
            $edit_status = postPositiveInt('edit_status');

            if ($editID > 0 && $edit_status > 0) {
                // Verify the status exists
                $statusCheck = dbFetchOne("SELECT id FROM status WHERE id = ?", 'i', [$edit_status]);
                if ($statusCheck) {
                    $result = dbQuery(
                        "UPDATE transaksi SET status_id = ? WHERE id = ?",
                        'ii',
                        [$edit_status, $editID]
                    );
                    if ($result) {
                        $status = '<div class="mt-3 alert alert-success" role="alert">Status berhasil diubah!</div>';
                    } else {
                        $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal mengubah status!</div>';
                    }
                } else {
                    $status = '<div class="mt-3 alert alert-danger" role="alert">Status tidak valid!</div>';
                }
            }
        }

        // Delete transaction
        if (isset($_POST['submit']) && isset($_POST['deleteID']) && !isset($_POST['editID'])) {
            $deleteID = postPositiveInt('deleteID');

            if ($deleteID > 0) {
                // Use transaction for atomic delete
                $success = dbTransaction(function($conn) use ($deleteID) {
                    // Delete transaction details first (foreign key constraint)
                    $stmt1 = mysqli_prepare($conn, "DELETE FROM transaksi_detail WHERE transactions_id = ?");
                    mysqli_stmt_bind_param($stmt1, 'i', $deleteID);
                    mysqli_stmt_execute($stmt1);

                    // Delete main transaction
                    $stmt2 = mysqli_prepare($conn, "DELETE FROM transaksi WHERE id = ?");
                    mysqli_stmt_bind_param($stmt2, 'i', $deleteID);
                    return mysqli_stmt_execute($stmt2);
                });

                if ($success) {
                    $status = '<div class="mt-3 alert alert-success" role="alert">Penghapusan transaksi berhasil!</div>';
                } else {
                    $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal menghapus transaksi!</div>';
                }
            }
        }
    }
}

// Fetch statuses for dropdown
$statuses = dbFetchAll("SELECT id, name FROM status");
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pawshop | Transaksi</title>
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
                        <a class="nav-link active" href="./admin-transaksi.php">Transaksi</a>
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

    <!-- Start MODAL EDIT -->
    <div class="modal" id="editTransactions">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Status Transaksi</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" autocomplete="off">
                        <?= csrfField() ?>
                        <input type="hidden" name="editID">
                        <select class="mt-2 form-select" name="edit_status" required>
                            <option value="" selected disabled>Pilih status</option>
                            <?php foreach ($statuses as $st): ?>
                                <option value="<?= e($st['id']) ?>"><?= e($st['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
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
    <div class="modal" id="deleteTransactions">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <p></p>
                </div>
                <div class="modal-body">
                    <form class="d-flex justify-content-between" action="" method="POST">
                        <?= csrfField() ?>
                        <input type="hidden" name="deleteID" value="">
                        <input type="submit" name="submit" value="Yes" class="mt-2 btn btn-danger">
                        <button data-bs-dismiss="modal" class="mt-2 btn btn-primary">No</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <!-- End MODAL DELETE -->

    <div class="container mt-3">

        <?= $status; ?>
        <div class="table-responsive">

            <table id="transactionsDataTables" class="table display" style="width:100%">
                <thead>
                    <tr>
                        <th>Aksi</th>
                        <th>ID</th>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Total</th>
                        <th>Metode Pembayaran</th>
                        <th>Status</th>
                        <th>Bukti Pembayaran</th>
                        <th>Tanggal Kedaluarsa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $transactions = dbFetchAll(
                        "SELECT tr.id, tr.timestamp, u.username, tr.total_amount, tr.payment_method, tr.bukti_pembayaran, st.name, tr.expiry_date
                         FROM transaksi tr
                         JOIN users u ON tr.user_id = u.id
                         JOIN status st ON tr.status_id = st.id
                         ORDER BY tr.id DESC"
                    );
                    foreach ($transactions as $row):
                    ?>
                        <tr>
                            <td style="vertical-align: middle;">
                                <button class="btn" data-bs-toggle="modal" data-id="<?= e($row['id']); ?>" data-bs-target="#deleteTransactions"><i class="fa-solid fa-trash"></i></button>
                            </td>
                            <td style="vertical-align: middle;"><?= e($row['id']); ?></td>
                            <td style="vertical-align: middle;"><?= e($row['timestamp']); ?></td>
                            <td style="vertical-align: middle;"><?= e($row['username']); ?></td>
                            <td style="vertical-align: middle;">Rp <?= e(number_format($row['total_amount'])); ?></td>
                            <td style="vertical-align: middle;"><?= e($row['payment_method']); ?></td>
                            <td style="vertical-align: middle;">
                                <?= e($row['name']); ?>
                                <button class="btn" data-bs-toggle="modal" data-id="<?= e($row['id']); ?>" data-bs-target="#editTransactions"><i class="fa-solid fa-edit"></i></button>
                            </td>
                            <td style="vertical-align: middle;">
                                <?php if (!empty($row['bukti_pembayaran'])): ?>
                                    <a href="./img/<?= e($row['bukti_pembayaran']) ?>"><?= e($row['bukti_pembayaran']) ?></a>
                                <?php endif; ?>
                            </td>
                            <td style="vertical-align: middle;">
                                <?= e($row['expiry_date']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
                <tfoot>
                    <tr>
                        <th>Aksi</th>
                        <th>ID</th>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Total</th>
                        <th>Metode Pembayaran</th>
                        <th>Status</th>
                        <th>Bukti Pembayaran</th>
                        <th>Tanggal Kedaluarsa</th>
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
        new DataTable('#transactionsDataTables');

        // JavaScript to handle the modal show event
        $('#editTransactions').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var transactionId = button.data('id');

            $.ajax({
                url: 'admin-transaksi-fetch.php',
                type: 'GET',
                data: { id: transactionId },
                dataType: 'json',
                success: function(data) {
                    if (!data.error) {
                        $('#editTransactions input[name="editID"]').val(data.id);
                        $('#editTransactions select[name="edit_status"]').val(data.status_id);
                    } else {
                        console.log('Error fetching transaction:', data.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                }
            });
        });

        $('#deleteTransactions').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var transactionId = button.data('id');

            $.ajax({
                url: 'admin-transaksi-fetch.php',
                type: 'GET',
                data: { id: transactionId },
                dataType: 'json',
                success: function(data) {
                    if (!data.error) {
                        $('#deleteTransactions .modal-header p').text('Apakah anda yakin ingin menghapus transaksi #' + data.id + '?');
                        $('#deleteTransactions input[name="deleteID"]').val(data.id);
                    } else {
                        console.log('Error fetching transaction:', data.error);
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
