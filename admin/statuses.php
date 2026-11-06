<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-guard.php';

use App\OrderService;

// TAMBAH STATUS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    if (!empty($_POST['status_name'])) {
        if (OrderService::createStatus($_POST['status_name'])) {
            flash('success', 'Status baru berhasil ditambahkan!');
        } else {
            flash('danger', 'Gagal menambahkan status!');
        }
        redirect('admin/statuses.php');
    }
}

// EDIT STATUS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    if (!empty($_POST['edit_status_name']) && !empty($_POST['id'])) {
        $id = (int)$_POST['id'];
        if (OrderService::updateStatusName($id, $_POST['edit_status_name'])) {
            flash('success', 'Status berhasil diubah!');
        } else {
            flash('danger', 'Gagal mengubah status!');
        }
        redirect('admin/statuses.php');
    }
}

// HAPUS STATUS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    if (!empty($_POST['id'])) {
        $id = (int)$_POST['id'];
        if (OrderService::deleteStatus($id)) {
            flash('success', 'Status berhasil dihapus!');
        } else {
            flash('danger', 'Gagal menghapus status! Pastikan tidak ada transaksi yang menggunakan status ini.');
        }
        redirect('admin/statuses.php');
    }
}

$statuses = OrderService::getAllStatuses();
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

  <div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="fw-bold">Manajemen Status Transaksi</h3>
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createStatus">
        <i class="fa-solid fa-plus me-1"></i> Tambah Status
      </button>
    </div>

    <?= render_flash() ?>

    <!-- MODAL TAMBAH STATUS -->
    <div class="modal fade" id="createStatus" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Tambah Status Transaksi</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="" method="POST">
            <div class="modal-body">
              <div class="form-floating mb-3">
                <input class="form-control" type="text" name="status_name" placeholder="Nama Status" required>
                <label>Nama Status</label>
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
            <h4 class="modal-title">Ubah Status Transaksi</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="" method="POST">
            <div class="modal-body">
              <input type="hidden" name="id" value="">
              <div class="form-floating mb-3">
                <input class="form-control" type="text" name="edit_status_name" placeholder="Nama Status" required>
                <label>Nama Status</label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" name="edit" class="btn btn-primary">Simpan Perubahan</button>
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
              <p class="text-confirm text-muted">Apakah Anda yakin ingin menghapus status transaksi ini?</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" name="delete" class="btn btn-danger">Hapus</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- TABEL STATUS -->
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle" id="statusDataTables">
            <thead>
              <tr>
                <th style="width: 80px;">ID</th>
                <th>Nama Status</th>
                <th style="width: 140px;" class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($statuses as $row): ?>
                <tr>
                  <td><?= $row['id']; ?></td>
                  <td class="fw-semibold">
                    <span class="badge bg-light text-dark border"><?= htmlspecialchars($row['name']); ?></span>
                  </td>
                  <td class="text-center">
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editStatus" data-id="<?= $row['id']; ?>" title="Edit">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteStatus" data-id="<?= $row['id']; ?>" title="Hapus">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
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
            $('#deleteStatus .text-confirm').text('Apakah Anda yakin ingin menghapus status "' + data.name + '"?');
            $('#deleteStatus input[name="id"]').val(data.id);
          }
        }
      });
    });
  </script>
</body>
</html>
