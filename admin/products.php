<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-guard.php';

use App\ProductService;

$pageTitle = "Manajemen Produk";

// TAMBAH PRODUK
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    if (!empty($_POST['product_name']) && !empty($_POST['category_id']) && isset($_POST['stok']) && !empty($_POST['harga'])) {
        $error = null;
        $success = ProductService::create([
            'nama_produk' => trim($_POST['product_name']),
            'category_id' => (int)$_POST['category_id'],
            'stok'        => (int)$_POST['stok'],
            'harga'       => (int)$_POST['harga'],
            'detail'      => trim($_POST['detail'] ?? ''),
        ], $_FILES['gambar'] ?? null, $error);

        if ($success) {
            flash('success', 'Produk berhasil ditambahkan!');
        } else {
            flash('danger', $error ?: 'Gagal menambahkan produk!');
        }
        redirect('admin/products.php');
    }
}

// EDIT PRODUK
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    if (!empty($_POST['id']) && !empty($_POST['edit_product_name']) && !empty($_POST['edit_category_id']) && isset($_POST['edit_stok']) && !empty($_POST['edit_harga'])) {
        $id = (int)$_POST['id'];
        $error = null;
        $success = ProductService::update($id, [
            'nama_produk' => trim($_POST['edit_product_name']),
            'category_id' => (int)$_POST['edit_category_id'],
            'stok'        => (int)$_POST['edit_stok'],
            'harga'       => (int)$_POST['edit_harga'],
            'detail'      => trim($_POST['edit_detail'] ?? ''),
        ], $_FILES['edit_gambar'] ?? null, $error);

        if ($success) {
            flash('success', 'Produk berhasil diperbarui!');
        } else {
            flash('danger', $error ?: 'Gagal memperbarui produk!');
        }
        redirect('admin/products.php');
    }
}

// HAPUS PRODUK
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete']) && !empty($_POST['id'])) {
    $id = (int)$_POST['id'];
    if (ProductService::delete($id)) {
        flash('success', 'Produk berhasil dihapus!');
    } else {
        flash('danger', 'Gagal menghapus produk!');
    }
    redirect('admin/products.php');
}

$categories = ProductService::getAllCategories();
$products = ProductService::getAll();

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-1">Daftar Produk</h4>
    <p class="text-muted small mb-0">Kelola katalog stok, harga, dan informasi produk</p>
  </div>
  <button type="button" class="btn btn-pawshop shadow-sm" data-bs-toggle="modal" data-bs-target="#createProduct">
    <i class="fa-solid fa-plus me-1"></i> Tambah Produk Baru
  </button>
</div>

<?= render_flash() ?>

<!-- MODAL TAMBAH PRODUK -->
<div class="modal fade" id="createProduct" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Tambah Produk Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="form-floating mb-3">
            <input class="form-control" type="text" name="product_name" placeholder="Nama Produk" required>
            <label>Nama Produk</label>
          </div>
          <div class="form-floating mb-3">
            <select class="form-select" name="category_id" required>
              <option value="" disabled selected>Pilih Kategori</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id']; ?>"><?= htmlspecialchars($cat['name']); ?></option>
              <?php endforeach; ?>
            </select>
            <label>Kategori</label>
          </div>
          <div class="row g-2 mb-3">
            <div class="col">
              <div class="form-floating">
                <input class="form-control" type="number" name="stok" placeholder="Stok" min="0" required>
                <label>Stok</label>
              </div>
            </div>
            <div class="col">
              <div class="form-floating">
                <input class="form-control" type="number" name="harga" placeholder="Harga" min="0" required>
                <label>Harga (Rp)</label>
              </div>
            </div>
          </div>
          <div class="form-floating mb-3">
            <textarea class="form-control" name="detail" placeholder="Detail Produk" style="height: 100px"></textarea>
            <label>Detail / Deskripsi Produk</label>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Foto Produk (Maks 5MB: JPG, PNG, WEBP)</label>
            <input class="form-control" type="file" name="gambar" accept="image/jpeg,image/png,image/webp">
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

<!-- MODAL EDIT PRODUK -->
<div class="modal fade" id="editProduct" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Ubah Data Produk</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" name="id" value="">
          <div class="form-floating mb-3">
            <input class="form-control" type="text" name="edit_product_name" placeholder="Nama Produk" required>
            <label>Nama Produk</label>
          </div>
          <div class="form-floating mb-3">
            <select class="form-select" name="edit_category_id" required>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id']; ?>"><?= htmlspecialchars($cat['name']); ?></option>
              <?php endforeach; ?>
            </select>
            <label>Kategori</label>
          </div>
          <div class="row g-2 mb-3">
            <div class="col">
              <div class="form-floating">
                <input class="form-control" type="number" name="edit_stok" placeholder="Stok" min="0" required>
                <label>Stok</label>
              </div>
            </div>
            <div class="col">
              <div class="form-floating">
                <input class="form-control" type="number" name="edit_harga" placeholder="Harga" min="0" required>
                <label>Harga (Rp)</label>
              </div>
            </div>
          </div>
          <div class="form-floating mb-3">
            <textarea class="form-control" name="edit_detail" placeholder="Detail Produk" style="height: 100px"></textarea>
            <label>Detail Produk</label>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Ganti Foto Produk (Kosongkan jika tidak diubah)</label>
            <input class="form-control" type="file" name="edit_gambar" accept="image/jpeg,image/png,image/webp">
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

<!-- MODAL HAPUS PRODUK -->
<div class="modal fade" id="deleteProduct" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold text-danger">Hapus Produk</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="" method="POST">
        <div class="modal-body">
          <input type="hidden" name="id" value="">
          <p class="text-confirm text-muted">Apakah Anda yakin ingin menghapus produk ini?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="delete" class="btn btn-danger">Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- TABEL PRODUK -->
<div class="card shadow-sm border-0">
  <div class="card-body p-4">
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle mb-0" id="productDataTables">
        <thead class="table-light">
          <tr>
            <th style="width: 50px;">ID</th>
            <th style="width: 70px;">Foto</th>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th>Stok</th>
            <th>Harga</th>
            <th style="width: 120px;" class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $row): ?>
            <tr>
              <td><?= $row['id']; ?></td>
              <td>
                <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($row['gambar'] ?: 'x.jpg'); ?>" class="rounded object-fit-cover border" width="46" height="46" alt="" onerror="this.src='<?= BASE_URL ?>assets/images/brand/logo.png'">
              </td>
              <td class="fw-semibold"><?= htmlspecialchars($row['nama_produk']); ?></td>
              <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['kategori_name'] ?? '-'); ?></span></td>
              <td>
                <span class="badge <?= ($row['stok'] < 20) ? 'bg-danger' : 'bg-success' ?>">
                  <?= $row['stok']; ?>
                </span>
              </td>
              <td class="fw-bold"><?= format_rupiah($row['harga']); ?></td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProduct" data-id="<?= $row['id']; ?>" title="Edit">
                  <i class="fa-solid fa-pen-to-square"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteProduct" data-id="<?= $row['id']; ?>" title="Hapus">
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
    new DataTable('#productDataTables');

    $('#editProduct').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var productId = button.data('id');
      $.ajax({
        url: '<?= BASE_URL ?>admin/ajax/product',
        type: 'GET',
        data: { id: productId },
        dataType: 'json',
        success: function(data) {
          if (!data.error) {
            $('#editProduct input[name="id"]').val(data.id);
            $('#editProduct input[name="edit_product_name"]').val(data.nama_produk);
            $('#editProduct select[name="edit_category_id"]').val(data.category_id);
            $('#editProduct input[name="edit_stok"]').val(data.stok);
            $('#editProduct input[name="edit_harga"]').val(data.harga);
            $('#editProduct textarea[name="edit_detail"]').val(data.detail);
          }
        }
      });
    });

    $('#deleteProduct').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var productId = button.data('id');
      $.ajax({
        url: '<?= BASE_URL ?>admin/ajax/product',
        type: 'GET',
        data: { id: productId },
        dataType: 'json',
        success: function(data) {
          if (!data.error) {
            $('#deleteProduct .text-confirm').text('Apakah Anda yakin ingin menghapus produk "' + data.nama_produk + '"?');
            $('#deleteProduct input[name="id"]').val(data.id);
          }
        }
      });
    });
  });
</script>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
