<?php
include 'config.php';

if ($privilege != 'admin') {
  header('Location: index.php');
}

$status = '';
if (isset($_POST['submit'])) {
  // EDIT STATUS TRANSAKSI
  if (isset($_POST['editID']) && $_POST['editID'] != null && isset($_POST['edit_status']) && $_POST['edit_status'] != null) {
    $editID = $_POST['editID'];
    $edit_status = $_POST['edit_status'];
    $sql = "UPDATE transaksi SET status_id='$edit_status' WHERE id='$editID'";
    if (mysqli_query($conn, $sql)) {
      $status = '<div class="mt-3 alert alert-success" role="alert">Status berhasil diubah!</div>';
    } else {
      $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal mengubah status!</div>';
    }
  }

  // DELETE TRANSAKSI
  if (isset($_POST['deleteID']) && $_POST['deleteID'] != null) {
    $deleteID = $_POST['deleteID'];
    $transactions = "DELETE FROM transaksi WHERE id='$deleteID'";
    $transaction_details = "DELETE FROM transaksi_detail WHERE transactions_id='$deleteID'";
    if (mysqli_query($conn, $transactions) && mysqli_query($conn, $transaction_details)) {
      $status = '<div class="mt-3 alert alert-success" role="alert">Penghapusan transaksi berhasil!</div>';
    } else {
      $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal menghapus transaksi!</div>';
    }
  }
}

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

  <!-- Start MODAL EDIT -->
  <div class="modal" id="editTransactions">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Edit Status Transaksi</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form action="" method="POST" enctype="multipart/form-data" autocomplete="off">
            <input type="hidden" name="editID">
            <select class="mt-2 form-select" name="edit_status" aria-label="Default select example" required>
              <option selected disabled>Pilih status</option>
              <?php
              $sql = 'SELECT * FROM status';
              $res = mysqli_query($conn, $sql);
              if (mysqli_num_rows($res) > 0) {
                while ($row = mysqli_fetch_assoc($res)) {
              ?>
                  <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
              <?php
                }
              }
              ?>
              <option value="1">Menunggu Konfirmasi</option>
            </select>
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
  <div class="modal" id="deleteTransactions">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <p></p>
        </div>
        <div class="modal-body">
          <form class="d-flex justify-content-between" action="" method="POST">
            <input type="hidden" name="deleteID" value="">
            <input type="submit" name="submit" value="Yes" class="mt-2 btn btn-danger"></input>
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

      <table id="example" class="table display" style="width:100%">
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
          $fetch = "SELECT tr.id, tr.timestamp, u.username, tr.total_amount, tr.payment_method, tr.bukti_pembayaran, st.name, tr.expiry_date FROM transaksi tr JOIN users u ON tr.user_id=u.id JOIN status st ON tr.status_id=st.id ORDER BY tr.id DESC";
          $res = mysqli_query($conn, $fetch);

          if (mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_assoc($res)) {
          ?>
              <tr>
                <td style="vertical-align: middle;">
                  <button class="btn" data-bs-toggle="modal" data-id="<?= $row['id']; ?>" data-bs-target="#deleteTransactions"><i class="fa-solid fa-trash"></i></button>
                </td>
                <td style="vertical-align: middle;"><?= $row['id']; ?></td>
                <td style="vertical-align: middle;"><?= $row['timestamp']; ?></td>
                <td style="vertical-align: middle;"><?= $row['username']; ?></td>
                <td style="vertical-align: middle;">Rp <?= number_format($row['total_amount']); ?></td>
                <td style="vertical-align: middle;"><?= $row['payment_method']; ?></td>
                <td style="vertical-align: middle;">
                  <?= $row['name']; ?>
                  <button class="btn" data-bs-toggle="modal" data-id="<?= $row['id']; ?>" data-bs-target="#editTransactions"><i class="fa-solid fa-edit"></i></button>
                </td>
                <td style="vertical-align: middle;">
                  <a href="./img/<?= $row['bukti_pembayaran'] ?>"><?= $row['bukti_pembayaran'] ?></a>
                </td>
                <td style="vertical-align: middle;">
                  <?= $row['expiry_date'] ?>
                </td>
              </tr>
          <?php
            }
          }
          ?>

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
  <script src="./js/jquery-3.7.0.js"></script>
  <script src="./js/jquery.dataTables.min.js"></script>
  <script>
    new DataTable('#example');

    // JavaScript to handle the modal show event
    $('#editTransactions').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var transactionId = button.data('id');

      // Fetch data using AJAX
      $.ajax({
        url: 'admin-transaksi-fetch.php',
        type: 'GET',
        data: {
          id: transactionId
        },
        dataType: 'json',
        success: function(data) {
          // Update the modal content with the fetched data
          if (!data.error) {
            $('#editTransactions input[name="editID"]').val(data.id);
            $('#editTransactions input[name="edit_status"]').val(data.status);
            // You can update other modal fields as needed
          } else {
            // Handle errors
            console.log('Error fetching transaction:', data.error);
          }
        },
        error: function(xhr, status, error) {
          // Handle AJAX errors
          console.error('AJAX error:', status, error);
        }
      });
    });

    // JavaScript to handle the modal show event
    $('#deleteTransactions').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var transactionId = button.data('id');

      // Fetch data using AJAX
      $.ajax({
        url: 'admin-transaksi-fetch.php',
        type: 'GET',
        data: {
          id: transactionId
        },
        dataType: 'json',
        success: function(data) {
          // Update the modal content with the fetched data
          if (!data.error) {
            // Set the text of the paragraph element in the modal header
            $('#deleteTransactions .modal-header p').text('Apakah anda yakin ingin menghapus ' + data.id + '?');

            $('#deleteTransactions input[name="deleteID"]').val(data.id);
            // You can update other modal fields as needed
          } else {
            // Handle errors
            console.log('Error fetching transaction:', data.error);
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