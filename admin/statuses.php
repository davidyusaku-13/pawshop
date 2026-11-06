<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-guard.php';

$status = '';

if (isset($_POST['submit'])) {
  if (!empty($_POST['status_name'])) {
    $status_name = mysqli_real_escape_string($conn, trim($_POST['status_name']));
    $sql = "INSERT INTO status (name) VALUES ('$status_name')";
    if (mysqli_query($conn, $sql)) {
      $status = '<div class="mt-3 alert alert-success" role="alert">Status berhasil ditambahkan!</div>';
    } else {
      $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal menambahkan status!</div>';
    }
  }
  if (!empty($_POST['edit_status_name']) && !empty($_POST['id'])) {
    $id = (int)$_POST['id'];
    $edit_status_name = mysqli_real_escape_string($conn, trim($_POST['edit_status_name']));
    $sql = "UPDATE status SET name='$edit_status_name' WHERE id=$id";
    if (mysqli_query($conn, $sql)) {
      $status = '<div class="mt-3 alert alert-success" role="alert">Edit status berhasil!</div>';
    } else {
      $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal mengedit status!</div>';
    }
  }
  if (!empty($_POST['delete_status_name']) && !empty($_POST['id'])) {
    $id = (int)$_POST['id'];
    $sql = "DELETE FROM status WHERE id=$id";
    if (mysqli_query($conn, $sql)) {
      $status = '<div class="mt-3 alert alert-success" role="alert">Status berhasil dihapus!</div>';
    } else {
      $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal menghapus status!</div>';
    }
  }
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pawshop | Kelola Status Transaksi</title>
  <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>assets/images/brand/logo-title.png">
  <link href="<?= BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/datatables.min.css">
  <script src="<?= BASE_URL ?>assets/js/jquery-3.7.1.min.js"></script>
  <script src="<?= BASE_URL ?>assets/js/datatables.min.js"></script>
  <script src="https://kit.fontawesome.com/ec712a3d01.js" crossorigin="anonymous"></script>
</head>

<body>
  <?php include __DIR__ . '/../includes/admin-navbar.php'; ?>

  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="fw-bold">Manajemen Status Transaksi</h3>
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newStatus">
        <i class="fa-solid fa-plus me-1"></i> Tambah Status
      </button>
    </div>

    <?= $status; ?>

    <!-- MODAL TAMBAH STATUS -->
    <div class="modal fade" id="newStatus" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Tambah Status Transaksi</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="" method="POST">
            <div class="modal-body">
              <div class="form-floating">
                <input class="form-control" type="text" name="status_name" placeholder="Nama Status" required>
                <label for="status_name">Nama Status</label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- MODAL EDIT STATUS -->
    <div class="modal fade" id="editStatus" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Edit Status</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="" method="POST">
            <div class="modal-body">
              <input type="hidden" name="id" value="">
              <div class="form-floating">
                <input class="form-control" type="text" name="edit_status_name" placeholder="Nama Status" required>
                <label for="edit_status_name">Nama Status</label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" name="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- MODAL HAPUS STATUS -->
    <div class="modal fade" id="deleteStatus" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <p class="modal-title fw-bold text-danger">Hapus Status</p>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="" method="POST">
            <div class="modal-body">
              <input type="hidden" name="id" value="">
              <input type="hidden" name="delete_status_name" value="">
              <p class="text-confirm text-muted">Apakah Anda yakin ingin menghapus status ini?</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" name="submit" class="btn btn-danger">Hapus</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- TABEL STATUS -->
    <div class="card shadow-sm mt-3">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle" id="statusDataTables">
            <thead>
              <tr>
                <th style="width: 80px;">ID</th>
                <th>Nama Status</th>
                <th style="width: 160px;" class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $fetch = "SELECT * FROM status ORDER BY id ASC";
              $res = mysqli_query($conn, $fetch);
              if ($res && mysqli_num_rows($res) > 0) {
                while ($row = mysqli_fetch_assoc($res)) {
              ?>
                  <tr>
                    <td><?= $row['id']; ?></td>
                    <td><span class="badge bg-secondary fs-6"><?= htmlspecialchars($row['name']); ?></span></td>
                    <td class="text-center">
                      <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#editStatus" data-id="<?= $row['id']; ?>">
                        <i class="fa-solid fa-pen-to-square"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteStatus" data-id="<?= $row['id']; ?>">
                        <i class="fa-solid fa-trash"></i>
                      </button>
                    </td>
                  </tr>
              <?php
                }
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script src="<?= BASE_URL ?>assets/js/bootstrap.bundle.min.js"></script>
  <script src="<?= BASE_URL ?>assets/js/jquery.dataTables.min.js"></script>
  <script>
    new DataTable('#statusDataTables');

    $('#editStatus').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var statusId = button.data('id');
      $.ajax({
        url: '<?= BASE_URL ?>admin/ajax/status.php',
        type: 'GET',
        data: { id: statusId },
        dataType: 'json',
        success: function(data) {
          if (!data.error) {
            $('#editStatus input[name="id"]').val(data.id);
            $('#editStatus input[name="edit_status_name"]').val(data.name);
          }
        }
      });
    });

    $('#deleteStatus').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var statusId = button.data('id');
      $.ajax({
        url: '<?= BASE_URL ?>admin/ajax/status.php',
        type: 'GET',
        data: { id: statusId },
        dataType: 'json',
        success: function(data) {
          if (!data.error) {
            $('#deleteStatus .text-confirm').text('Apakah anda yakin ingin menghapus status "' + data.name + '"?');
            $('#deleteStatus input[name="id"]').val(data.id);
            $('#deleteStatus input[name="delete_status_name"]').val(data.name);
          }
        }
      });
    });
  </script>
</body>
</html>
