<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-guard.php';

$status = '';

if (isset($_POST['submit'])) {
  if (!empty($_POST['category_name'])) {
    $category_name = mysqli_real_escape_string($conn, trim($_POST['category_name']));
    $sql = "INSERT INTO kategori (name) VALUES ('$category_name')";
    if (mysqli_query($conn, $sql)) {
      $status = '<div class="mt-3 alert alert-success" role="alert">Kategori berhasil ditambahkan!</div>';
    } else {
      $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal menambah kategori!</div>';
    }
  }
  if (!empty($_POST['edit_category_name']) && !empty($_POST['id'])) {
    $id = (int)$_POST['id'];
    $edit_category_name = mysqli_real_escape_string($conn, trim($_POST['edit_category_name']));
    $sql = "UPDATE kategori SET name='$edit_category_name' WHERE id=$id";
    if (mysqli_query($conn, $sql)) {
      $status = '<div class="mt-3 alert alert-success" role="alert">Edit berhasil!</div>';
    } else {
      $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal mengedit kategori!</div>';
    }
  }
  if (!empty($_POST['delete_category_name']) && !empty($_POST['id'])) {
    $id = (int)$_POST['id'];
    $sql = "DELETE FROM kategori WHERE id=$id";
    if (mysqli_query($conn, $sql)) {
      $status = '<div class="mt-3 alert alert-success" role="alert">Kategori berhasil dihapus!</div>';
    } else {
      $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal menghapus kategori!</div>';
    }
  }
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pawshop | Kelola Kategori</title>
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
      <h3 class="fw-bold">Manajemen Kategori</h3>
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newCategory">
        <i class="fa-solid fa-plus me-1"></i> Tambah Kategori
      </button>
    </div>

    <?= $status; ?>

    <!-- MODAL TAMBAH KATEGORI -->
    <div class="modal fade" id="newCategory" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Tambah Kategori</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="" method="POST">
            <div class="modal-body">
              <div class="form-floating">
                <input class="form-control" type="text" name="category_name" placeholder="Nama Kategori" required>
                <label for="category_name">Nama Kategori</label>
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

    <!-- MODAL EDIT KATEGORI -->
    <div class="modal fade" id="editCategory" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Edit Kategori</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="" method="POST">
            <div class="modal-body">
              <input type="hidden" name="id" value="">
              <div class="form-floating">
                <input class="form-control" type="text" name="edit_category_name" placeholder="Nama Kategori" required>
                <label for="edit_category_name">Nama Kategori</label>
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

    <!-- MODAL HAPUS KATEGORI -->
    <div class="modal fade" id="deleteCategory" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <p class="modal-title fw-bold text-danger">Apakah Anda yakin ingin menghapus kategori ini?</p>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="" method="POST">
            <div class="modal-body">
              <input type="hidden" name="id" value="">
              <input type="hidden" name="delete_category_name" value="">
              <p class="text-muted">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" name="submit" class="btn btn-danger">Hapus</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- TABEL KATEGORI -->
    <div class="card shadow-sm mt-3">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle" id="categoriesDataTables">
            <thead>
              <tr>
                <th style="width: 80px;">ID</th>
                <th>Nama Kategori</th>
                <th style="width: 160px;" class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $fetch = "SELECT * FROM kategori ORDER BY id ASC";
              $res = mysqli_query($conn, $fetch);
              if ($res && mysqli_num_rows($res) > 0) {
                while ($row = mysqli_fetch_assoc($res)) {
              ?>
                  <tr>
                    <td><?= $row['id']; ?></td>
                    <td class="fw-semibold"><?= htmlspecialchars($row['name']); ?></td>
                    <td class="text-center">
                      <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#editCategory" data-id="<?= $row['id']; ?>">
                        <i class="fa-solid fa-pen-to-square"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteCategory" data-id="<?= $row['id']; ?>">
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
    new DataTable('#categoriesDataTables');

    $('#editCategory').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var categoryId = button.data('id');
      $.ajax({
        url: '<?= BASE_URL ?>admin/ajax/category.php',
        type: 'GET',
        data: { id: categoryId },
        dataType: 'json',
        success: function(data) {
          if (!data.error) {
            $('#editCategory input[name="id"]').val(data.id);
            $('#editCategory input[name="edit_category_name"]').val(data.name);
          }
        }
      });
    });

    $('#deleteCategory').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var categoryId = button.data('id');
      $.ajax({
        url: '<?= BASE_URL ?>admin/ajax/category.php',
        type: 'GET',
        data: { id: categoryId },
        dataType: 'json',
        success: function(data) {
          if (!data.error) {
            $('#deleteCategory .modal-header p').text('Apakah anda yakin ingin menghapus ' + data.name + '?');
            $('#deleteCategory input[name="id"]').val(data.id);
            $('#deleteCategory input[name="delete_category_name"]').val(data.name);
          }
        }
      });
    });
  </script>
</body>
</html>
