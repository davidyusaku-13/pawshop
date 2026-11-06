<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-guard.php';

use App\ProductService;

$pageTitle = "Manajemen Kategori";

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

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-1">Daftar Kategori</h4>
    <p class="text-muted small mb-0">Kelola kategori produk untuk pengelompokan etalase</p>
  </div>
  <button type="button" class="btn btn-pawshop shadow-sm" data-bs-toggle="modal" data-bs-target="#createCategory">
    <i class="fa-solid fa-plus me-1"></i> Tambah Kategori
  </button>
</div>

<?= render_flash() ?>

<!-- MODAL TAMBAH KATEGORI -->
<div class="modal fade" id="createCategory" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Tambah Kategori</h5>
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
          <button type="submit" name="submit" class="btn btn-pawshop">Simpan</button>
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
        <h5 class="modal-title fw-bold">Ubah Kategori</h5>
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
          <button type="submit" name="edit" class="btn btn-pawshop">Simpan Perubahan</button>
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
        <h5 class="modal-title fw-bold text-danger">Hapus Kategori</h5>
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
<div class="card shadow-sm border-0">
  <div class="card-body p-4">
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle mb-0" id="categoryDataTables">
        <thead class="table-light">
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
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCategory" data-id="<?= $row['id']; ?>" title="Edit">
                  <i class="fa-solid fa-pen-to-square"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteCategory" data-id="<?= $row['id']; ?>" title="Hapus">
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
    new DataTable('#categoryDataTables');

    $('#editCategory').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var categoryId = button.data('id');
      $.ajax({
        url: '<?= BASE_URL ?>admin/ajax/category',
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
        url: '<?= BASE_URL ?>admin/ajax/category',
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
  });
</script>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
