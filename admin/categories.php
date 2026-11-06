<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-guard.php';

use App\ProductService;

// TAMBAH KATEGORI
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    if (!empty($_POST['category_name'])) {
        if (ProductService::createCategory($_POST['category_name'])) {
            flash('success', 'Kategori baru berhasil ditambahkan!');
        } else {
            flash('danger', 'Gagal menambahkan kategori!');
        }
        redirect('admin/categories.php');
    }
}

// EDIT KATEGORI
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    if (!empty($_POST['edit_category_name']) && !empty($_POST['id'])) {
        $id = (int)$_POST['id'];
        if (ProductService::updateCategory($id, $_POST['edit_category_name'])) {
            flash('success', 'Kategori berhasil diubah!');
        } else {
            flash('danger', 'Gagal mengubah kategori!');
        }
        redirect('admin/categories.php');
    }
}

// HAPUS KATEGORI
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    if (!empty($_POST['id'])) {
        $id = (int)$_POST['id'];
        if (ProductService::deleteCategory($id)) {
            flash('success', 'Kategori berhasil dihapus!');
        } else {
            flash('danger', 'Gagal menghapus kategori! Pastikan tidak ada produk yang terhubung ke kategori ini.');
        }
        redirect('admin/categories.php');
    }
}

$categories = ProductService::getAllCategories();
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

  <div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="fw-bold">Manajemen Kategori</h3>
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategory">
        <i class="fa-solid fa-plus me-1"></i> Tambah Kategori
      </button>
    </div>

    <?= render_flash() ?>

    <!-- MODAL TAMBAH KATEGORI -->
    <div class="modal fade" id="createCategory" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Tambah Kategori</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="" method="POST">
            <div class="modal-body">
              <div class="form-floating mb-3">
                <input class="form-control" type="text" name="category_name" placeholder="Nama Kategori" required>
                <label>Nama Kategori</label>
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
            <h4 class="modal-title">Ubah Kategori</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="" method="POST">
            <div class="modal-body">
              <input type="hidden" name="id" value="">
              <div class="form-floating mb-3">
                <input class="form-control" type="text" name="edit_category_name" placeholder="Nama Kategori" required>
                <label>Nama Kategori</label>
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

    <!-- MODAL HAPUS KATEGORI -->
    <div class="modal fade" id="deleteCategory" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <p class="modal-title fw-bold text-danger">Hapus Kategori</p>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="" method="POST">
            <div class="modal-body">
              <input type="hidden" name="id" value="">
              <p class="text-confirm text-muted">Apakah Anda yakin ingin menghapus kategori ini?</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" name="delete" class="btn btn-danger">Hapus</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- TABEL KATEGORI -->
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle" id="categoryDataTables">
            <thead>
              <tr>
                <th style="width: 80px;">ID</th>
                <th>Nama Kategori</th>
                <th style="width: 140px;" class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories as $row): ?>
                <tr>
                  <td><?= $row['id']; ?></td>
                  <td class="fw-semibold"><?= htmlspecialchars($row['name']); ?></td>
                  <td class="text-center">
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editCategory" data-id="<?= $row['id']; ?>" title="Edit">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteCategory" data-id="<?= $row['id']; ?>" title="Hapus">
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
    new DataTable('#categoryDataTables');

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
            $('#deleteCategory .text-confirm').text('Apakah Anda yakin ingin menghapus kategori "' + data.name + '"?');
            $('#deleteCategory input[name="id"]').val(data.id);
          }
        }
      });
    });
  </script>
</body>
</html>
