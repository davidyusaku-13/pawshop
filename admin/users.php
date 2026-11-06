<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-guard.php';

$status = '';
$wrongpass = '';

if (isset($_POST['submit'])) {
  if (!empty($_POST['edit_privilege']) && !empty($_POST['id'])) {
    $id = (int)$_POST['id'];
    $edit_privilege = mysqli_real_escape_string($conn, $_POST['edit_privilege']);
    $sql = "UPDATE users SET privilege='$edit_privilege' WHERE id=$id";
    if (mysqli_query($conn, $sql)) {
      $status = '<div class="mt-3 alert alert-success" role="alert">Hak akses berhasil diubah!</div>';
    } else {
      $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal mengganti hak akses!</div>';
    }
  }

  if (!empty($_POST['edit_new_password']) && !empty($_POST['edit_new_repassword']) && !empty($_POST['id'])) {
    $id = (int)$_POST['id'];
    $edit_username = mysqli_real_escape_string($conn, $_POST['edit_username']);
    $edit_new_password = md5($_POST['edit_new_password']);
    $edit_new_repassword = md5($_POST['edit_new_repassword']);
    if ($edit_new_password !== $edit_new_repassword) {
      $wrongpass = '<div class="mt-3 alert alert-danger" role="alert">Password konfirmasi tidak cocok!</div>';
    } else {
      $sql = "UPDATE users SET username='$edit_username', password='$edit_new_password' WHERE id=$id";
      if (mysqli_query($conn, $sql)) {
        $status = '<div class="mt-3 alert alert-success" role="alert">Password pengguna berhasil diubah!</div>';
      } else {
        $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal mengganti password!</div>';
      }
    }
  }

  if (!empty($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    if ($id === (int)$userid) {
      $status = '<div class="mt-3 alert alert-danger" role="alert">Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif!</div>';
    } else {
      $sql = "DELETE FROM users WHERE id=$id";
      if (mysqli_query($conn, $sql)) {
        $status = '<div class="mt-3 alert alert-success" role="alert">Pengguna berhasil dihapus!</div>';
      } else {
        $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal menghapus pengguna!</div>';
      }
    }
  }
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pawshop | Kelola Pengguna</title>
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
      <h3 class="fw-bold">Manajemen Pengguna</h3>
    </div>

    <?= $status; ?>
    <?= $wrongpass; ?>

    <!-- MODAL EDIT PRIVILEGE -->
    <div class="modal fade" id="editUsers" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Ubah Hak Akses</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="" method="POST">
            <div class="modal-body">
              <input type="hidden" name="id" value="">
              <div class="form-floating mb-3">
                <select class="form-select" name="edit_privilege" required>
                  <option value="user">User (Pelanggan)</option>
                  <option value="admin">Admin (Pengelola)</option>
                </select>
                <label>Peran / Hak Akses</label>
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

    <!-- MODAL EDIT PASSWORD -->
    <div class="modal fade" id="editPasswordUsers" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Reset Password Pengguna</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="" method="POST">
            <div class="modal-body">
              <input type="hidden" name="id" value="">
              <div class="form-floating mb-3">
                <input class="form-control" type="text" name="edit_username" readonly>
                <label>Username</label>
              </div>
              <div class="form-floating mb-3">
                <input class="form-control" type="password" name="edit_new_password" placeholder="Password Baru" required>
                <label>Password Baru</label>
              </div>
              <div class="form-floating mb-3">
                <input class="form-control" type="password" name="edit_new_repassword" placeholder="Konfirmasi Password Baru" required>
                <label>Konfirmasi Password Baru</label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" name="submit" class="btn btn-primary">Simpan Password</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- MODAL HAPUS PENGGUNA -->
    <div class="modal fade" id="deleteUsers" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <p class="modal-title fw-bold text-danger">Hapus Pengguna</p>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="" method="POST">
            <div class="modal-body">
              <input type="hidden" name="delete_id" value="">
              <p class="text-confirm text-muted">Apakah Anda yakin ingin menghapus pengguna ini?</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" name="submit" class="btn btn-danger">Hapus</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- TABEL USERS -->
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle" id="usersDataTables">
            <thead>
              <tr>
                <th style="width: 80px;">ID</th>
                <th>Username</th>
                <th>Nomor Telepon</th>
                <th>Peran</th>
                <th style="width: 180px;" class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $fetch = "SELECT id, username, phone_number, privilege FROM users ORDER BY id ASC";
              $res = mysqli_query($conn, $fetch);
              if ($res && mysqli_num_rows($res) > 0) {
                while ($row = mysqli_fetch_assoc($res)) {
              ?>
                  <tr>
                    <td><?= $row['id']; ?></td>
                    <td class="fw-semibold"><?= htmlspecialchars($row['username']); ?></td>
                    <td><?= htmlspecialchars($row['phone_number'] ?? '-'); ?></td>
                    <td>
                      <span class="badge <?= ($row['privilege'] === 'admin') ? 'bg-danger' : 'bg-primary' ?>">
                        <?= strtoupper($row['privilege']); ?>
                      </span>
                    </td>
                    <td class="text-center">
                      <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#editUsers" data-id="<?= $row['id']; ?>" title="Ubah Peran">
                        <i class="fa-solid fa-user-shield"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-warning text-dark" data-bs-toggle="modal" data-bs-target="#editPasswordUsers" data-id="<?= $row['id']; ?>" title="Reset Password">
                        <i class="fa-solid fa-key"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUsers" data-id="<?= $row['id']; ?>" title="Hapus Pengguna">
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
    new DataTable('#usersDataTables');

    $('#editUsers').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var userId = button.data('id');
      $.ajax({
        url: '<?= BASE_URL ?>admin/ajax/user.php',
        type: 'GET',
        data: { id: userId },
        dataType: 'json',
        success: function(data) {
          if (!data.error) {
            $('#editUsers input[name="id"]').val(data.id);
            $('#editUsers select[name="edit_privilege"]').val(data.privilege);
          }
        }
      });
    });

    $('#editPasswordUsers').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var userId = button.data('id');
      $.ajax({
        url: '<?= BASE_URL ?>admin/ajax/user.php',
        type: 'GET',
        data: { id: userId },
        dataType: 'json',
        success: function(data) {
          if (!data.error) {
            $('#editPasswordUsers input[name="id"]').val(data.id);
            $('#editPasswordUsers input[name="edit_username"]').val(data.username);
          }
        }
      });
    });

    $('#deleteUsers').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var userId = button.data('id');
      $.ajax({
        url: '<?= BASE_URL ?>admin/ajax/user.php',
        type: 'GET',
        data: { id: userId },
        dataType: 'json',
        success: function(data) {
          if (!data.error) {
            $('#deleteUsers .text-confirm').text('Apakah Anda yakin ingin menghapus pengguna "' + data.username + '"?');
            $('#deleteUsers input[name="delete_id"]').val(data.id);
          }
        }
      });
    });
  </script>
</body>
</html>
