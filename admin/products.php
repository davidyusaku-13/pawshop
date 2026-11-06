<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-guard.php';

$status = '';

// TAMBAH PRODUK
if (isset($_POST['submit'])) {
  if (!empty($_POST['product_name']) && !empty($_POST['category_id']) && isset($_POST['stok']) && !empty($_POST['harga'])) {
    $product_name = mysqli_real_escape_string($conn, trim($_POST['product_name']));
    $category_id = (int)$_POST['category_id'];
    $stok = (int)$_POST['stok'];
    $harga = (int)$_POST['harga'];
    $detail = mysqli_real_escape_string($conn, trim($_POST['detail'] ?? ''));

    $gambar = !empty($_FILES["gambar"]["name"]) ? basename($_FILES["gambar"]["name"]) : '';

    if (empty($gambar)) {
      $sql = "INSERT INTO produk (gambar, nama_produk, category_id, stok, harga, detail) VALUES ('x.jpg', '$product_name', $category_id, $stok, $harga, '$detail')";
    } else {
      $uniqueName = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", $gambar);
      $target_file = ROOT_PATH . "/assets/images/" . $uniqueName;
      move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file);
      $sql = "INSERT INTO produk (gambar, nama_produk, category_id, stok, harga, detail) VALUES ('$uniqueName', '$product_name', $category_id, $stok, $harga, '$detail')";
    }

    if (mysqli_query($conn, $sql)) {
      $status = '<div class="mt-3 alert alert-success" role="alert">Produk berhasil ditambahkan!</div>';
    } else {
      $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal menambahkan produk!</div>';
    }
  }
}

// EDIT PRODUK
if (isset($_POST['edit'])) {
  if (!empty($_POST['id']) && !empty($_POST['edit_product_name']) && !empty($_POST['edit_category_id']) && isset($_POST['edit_stok']) && !empty($_POST['edit_harga'])) {
    $id = (int)$_POST['id'];
    $edit_product_name = mysqli_real_escape_string($conn, trim($_POST['edit_product_name']));
    $edit_category_id = (int)$_POST['edit_category_id'];
    $edit_stok = (int)$_POST['edit_stok'];
    $edit_harga = (int)$_POST['edit_harga'];
    $edit_detail = mysqli_real_escape_string($conn, trim($_POST['edit_detail'] ?? ''));

    if (empty($_FILES["edit_gambar"]["name"])) {
      $sql = "UPDATE produk SET nama_produk='$edit_product_name', category_id='$edit_category_id', stok='$edit_stok', harga='$edit_harga', detail='$edit_detail' WHERE id=$id";
    } else {
      $edit_gambar = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", basename($_FILES["edit_gambar"]["name"]));
      $target_file = ROOT_PATH . "/assets/images/" . $edit_gambar;
      move_uploaded_file($_FILES["edit_gambar"]["tmp_name"], $target_file);
      $sql = "UPDATE produk SET gambar='$edit_gambar', nama_produk='$edit_product_name', category_id='$edit_category_id', stok='$edit_stok', harga='$edit_harga', detail='$edit_detail' WHERE id=$id";
    }

    if (mysqli_query($conn, $sql)) {
      $status = '<div class="mt-3 alert alert-success" role="alert">Produk berhasil diubah!</div>';
    } else {
      $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal mengubah produk!</div>';
    }
  }
}

// HAPUS PRODUK
if (isset($_POST['delete']) && !empty($_POST['id'])) {
  $id = (int)$_POST['id'];
  $sql = "DELETE FROM produk WHERE id=$id";
  if (mysqli_query($conn, $sql)) {
    $status = '<div class="mt-3 alert alert-success" role="alert">Produk berhasil dihapus!</div>';
  } else {
    $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal menghapus produk!</div>';
  }
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pawshop | Kelola Produk</title>
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
      <h3 class="fw-bold">Manajemen Produk</h3>
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newProduct">
        <i class="fa-solid fa-plus me-1"></i> Tambah Produk
      </button>
    </div>

    <?= $status; ?>

    <!-- MODAL TAMBAH PRODUK -->
    <div class="modal fade" id="newProduct" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Tambah Produk Baru</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
              <div class="mb-3">
                <label for="gambar" class="form-label small fw-semibold">Foto Produk</label>
                <input class="form-control" type="file" name="gambar" accept="image/*">
              </div>
              <div class="form-floating mb-3">
                <input class="form-control" type="text" name="product_name" placeholder="Nama Produk" required>
                <label for="product_name">Nama Produk</label>
              </div>
              <div class="form-floating mb-3">
                <select class="form-select" name="category_id" required>
                  <option value="" disabled selected>Pilih Kategori</option>
                  <?php
                  $catQ = mysqli_query($conn, 'SELECT * FROM kategori');
                  while ($crow = mysqli_fetch_assoc($catQ)) {
                    echo "<option value='{$crow['id']}'>" . htmlspecialchars($crow['name']) . "</option>";
                  }
                  ?>
                </select>
                <label>Kategori</label>
              </div>
              <div class="form-floating mb-3">
                <input class="form-control" type="number" name="stok" min="0" placeholder="Stok" required>
                <label>Stok</label>
              </div>
              <div class="form-floating mb-3">
                <input class="form-control" type="number" name="harga" min="0" placeholder="Harga" required>
                <label>Harga (Rp)</label>
              </div>
              <div class="form-floating mb-3">
                <textarea class="form-control" rows="3" name="detail" placeholder="Detail Produk" style="height: 100px;"></textarea>
                <label>Detail Produk</label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" name="submit" class="btn btn-primary">Simpan Produk</button>
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
            <h4 class="modal-title">Edit Produk</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
              <input type="hidden" name="id" value="">
              <div class="mb-3">
                <label class="form-label small fw-semibold">Ubah Foto (Kosongkan jika tidak diubah)</label>
                <input class="form-control" type="file" name="edit_gambar" accept="image/*">
              </div>
              <div class="form-floating mb-3">
                <input class="form-control" type="text" name="edit_product_name" placeholder="Nama Produk" required>
                <label>Nama Produk</label>
              </div>
              <div class="form-floating mb-3">
                <select class="form-select" name="edit_category_id" required>
                  <option value="" disabled>Pilih Kategori</option>
                  <?php
                  $catQ2 = mysqli_query($conn, 'SELECT * FROM kategori');
                  while ($crow2 = mysqli_fetch_assoc($catQ2)) {
                    echo "<option value='{$crow2['id']}'>" . htmlspecialchars($crow2['name']) . "</option>";
                  }
                  ?>
                </select>
                <label>Kategori</label>
              </div>
              <div class="form-floating mb-3">
                <input class="form-control" type="number" name="edit_stok" min="0" placeholder="Stok" required>
                <label>Stok</label>
              </div>
              <div class="form-floating mb-3">
                <input class="form-control" type="number" name="edit_harga" min="0" placeholder="Harga" required>
                <label>Harga (Rp)</label>
              </div>
              <div class="form-floating mb-3">
                <textarea class="form-control" rows="3" name="edit_detail" placeholder="Detail Produk" style="height: 100px;"></textarea>
                <label>Detail Produk</label>
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

    <!-- MODAL HAPUS PRODUK -->
    <div class="modal fade" id="deleteProduct" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <p class="modal-title fw-bold text-danger">Hapus Produk</p>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="" method="POST">
            <div class="modal-body">
              <input type="hidden" name="id" value="">
              <p class="text-confirm mb-0">Apakah Anda yakin ingin menghapus produk ini?</p>
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
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle" id="productsDataTables">
            <thead>
              <tr>
                <th style="width: 60px;">ID</th>
                <th style="width: 70px;">Gambar</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Harga</th>
                <th style="width: 130px;" class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $fetch = "SELECT p.id, p.gambar, p.nama_produk, c.name AS category_name, p.stok, p.harga, p.detail 
                        FROM produk p 
                        LEFT JOIN kategori c ON p.category_id=c.id 
                        ORDER BY p.id DESC";
              $res = mysqli_query($conn, $fetch);
              if ($res && mysqli_num_rows($res) > 0) {
                while ($row = mysqli_fetch_assoc($res)) {
              ?>
                  <tr>
                    <td><?= $row['id']; ?></td>
                    <td>
                      <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($row['gambar']) ?>" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: contain;">
                    </td>
                    <td class="fw-semibold"><?= htmlspecialchars($row['nama_produk']); ?></td>
                    <td><?= htmlspecialchars($row['category_name'] ?? '-'); ?></td>
                    <td>
                      <span class="badge <?= ($row['stok'] < 20) ? 'bg-danger' : 'bg-success' ?>">
                        <?= $row['stok']; ?>
                      </span>
                    </td>
                    <td>Rp <?= number_format($row['harga']); ?></td>
                    <td class="text-center">
                      <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#editProduct" data-id="<?= $row['id']; ?>">
                        <i class="fa-solid fa-pen-to-square"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteProduct" data-id="<?= $row['id']; ?>">
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
    new DataTable('#productsDataTables');

    $('#editProduct').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var productId = button.data('id');
      $.ajax({
        url: '<?= BASE_URL ?>admin/ajax/product.php',
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
        url: '<?= BASE_URL ?>admin/ajax/product.php',
        type: 'GET',
        data: { id: productId },
        dataType: 'json',
        success: function(data) {
          if (!data.error) {
            $('#deleteProduct .text-confirm').text('Apakah Anda yakin ingin menghapus "' + data.nama_produk + '"?');
            $('#deleteProduct input[name="id"]').val(data.id);
          }
        }
      });
    });
  </script>
</body>
</html>
