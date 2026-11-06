<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-guard.php';

use App\AuthService;

$pageTitle = "Manajemen Pengguna";
$currentUserId = AuthService::getCurrentUserId();

// UBAH HAK AKSES
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_privilege'])) {
    if (!empty($_POST['edit_privilege']) && !empty($_POST['id'])) {
        $id = (int)$_POST['id'];
        $privilege = trim($_POST['edit_privilege']);
        if (AuthService::updatePrivilege($id, $privilege)) {
            flash('success', 'Hak akses pengguna berhasil diubah!');
        } else {
            flash('danger', 'Gagal mengganti hak akses!');
        }
        redirect('admin/users.php');
    }
}

// RESET PASSWORD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_password'])) {
    if (!empty($_POST['edit_new_password']) && !empty($_POST['edit_new_repassword']) && !empty($_POST['id'])) {
        $id = (int)$_POST['id'];
        $pwd = $_POST['edit_new_password'];
        $repwd = $_POST['edit_new_repassword'];

        if ($pwd !== $repwd) {
            flash('danger', 'Konfirmasi password baru tidak cocok!');
        } elseif (strlen($pwd) < 4) {
            flash('danger', 'Password baru minimal 4 karakter!');
        } else {
            if (AuthService::adminResetPassword($id, $pwd)) {
                flash('success', 'Password pengguna berhasil direset!');
            } else {
                flash('danger', 'Gagal mereset password pengguna!');
            }
        }
        redirect('admin/users.php');
    }
}

// HAPUS USER
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_delete'])) {
    if (!empty($_POST['delete_id'])) {
        $id = (int)$_POST['delete_id'];
        if ($id === $currentUserId) {
            flash('danger', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif!');
        } else {
            if (AuthService::deleteUser($id, $currentUserId)) {
                flash('success', 'Pengguna berhasil dihapus!');
            } else {
                flash('danger', 'Gagal menghapus pengguna!');
            }
        }
        redirect('admin/users.php');
    }
}

$users = AuthService::getAllUsers();

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-1">Daftar Akun Pengguna</h4>
    <p class="text-muted small mb-0">Kelola akun terdaftar, hak akses peran, dan reset kata sandi</p>
  </div>
</div>

<?= render_flash() ?>

<!-- MODAL EDIT PRIVILEGE -->
<div class="modal fade" id="editUsers" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Ubah Hak Akses</h5>
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
          <button type="submit" name="submit_privilege" class="btn btn-pawshop">Simpan</button>
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
        <h5 class="modal-title fw-bold">Reset Password Pengguna</h5>
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
          <button type="submit" name="submit_password" class="btn btn-pawshop">Simpan Password</button>
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
        <h5 class="modal-title fw-bold text-danger">Hapus Pengguna</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="" method="POST">
        <div class="modal-body">
          <input type="hidden" name="delete_id" value="">
          <p class="text-confirm text-muted">Apakah Anda yakin ingin menghapus pengguna ini?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="submit_delete" class="btn btn-danger">Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- TABEL USERS -->
<div class="card shadow-sm border-0">
  <div class="card-body p-4">
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle mb-0" id="usersDataTables">
        <thead class="table-light">
          <tr>
            <th style="width: 80px;">ID</th>
            <th>Username</th>
            <th>Nomor Telepon</th>
            <th>Peran</th>
            <th style="width: 140px;" class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $row): ?>
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
                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#editUsers" data-id="<?= $row['id']; ?>" title="Ubah Peran">
                  <i class="fa-solid fa-user-shield"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-warning text-dark" data-bs-toggle="modal" data-bs-target="#editPasswordUsers" data-id="<?= $row['id']; ?>" title="Reset Password">
                  <i class="fa-solid fa-key"></i>
                </button>
                <?php if ((int)$row['id'] !== (int)$currentUserId): ?>
                  <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteUsers" data-id="<?= $row['id']; ?>" title="Hapus Pengguna">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                <?php endif; ?>
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
    new DataTable('#usersDataTables');

    $('#editUsers').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var userId = button.data('id');
      $.ajax({
        url: '<?= BASE_URL ?>admin/ajax/user',
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
        url: '<?= BASE_URL ?>admin/ajax/user',
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
        url: '<?= BASE_URL ?>admin/ajax/user',
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
  });
</script>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
