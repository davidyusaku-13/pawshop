<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-guard.php';

use App\OrderService;

$pageTitle = "Status Transaksi";

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

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-1">Daftar Status Pesanan</h4>
    <p class="text-muted small mb-0">Kelola tahapan status pemrosesan pesanan pelanggan</p>
  </div>
  <button type="button" class="btn btn-pawshop shadow-sm" data-bs-toggle="modal" data-bs-target="#createStatus">
    <i class="fa-solid fa-plus me-1"></i> Tambah Status
  </button>
</div>

<?= render_flash() ?>

<!-- MODAL TAMBAH STATUS -->
<div class="modal fade" id="createStatus" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Tambah Status Transaksi</h5>
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
          <button type="submit" name="submit" class="btn btn-pawshop">Simpan</button>
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
        <h5 class="modal-title fw-bold">Ubah Status Transaksi</h5>
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
          <button type="submit" name="edit" class="btn btn-pawshop">Simpan Perubahan</button>
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
        <h5 class="modal-title fw-bold text-danger">Hapus Status</h5>
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
<div class="card shadow-sm border-0">
  <div class="card-body p-4">
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle mb-0" id="statusDataTables">
        <thead class="table-light">
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
                <span class="badge bg-light text-dark border px-3 py-2"><?= htmlspecialchars($row['name']); ?></span>
              </td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editStatus" data-id="<?= $row['id']; ?>" title="Edit">
                  <i class="fa-solid fa-pen-to-square"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteStatus" data-id="<?= $row['id']; ?>" title="Hapus">
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

<script>
  $(document).ready(function() {
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
  });
</script>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
