<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth-guard.php';

$pageTitle = "Pawshop | Profil Pengguna";
$status = '';

if (isset($_POST['submit'])) {
  if (!empty($_POST['id']) && !empty($_POST['oldpassword']) && !empty($_POST['newpassword']) && !empty($_POST['newrepassword'])) {
    $id = (int)$_POST['id'];
    $oldpassword = md5($_POST['oldpassword']);
    $newpassword = md5($_POST['newpassword']);
    $newrepassword = md5($_POST['newrepassword']);

    $sql = "SELECT * FROM users WHERE id=$id AND password='$oldpassword'";
    $res = mysqli_query($conn, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
      if ($newpassword !== $newrepassword) {
        $status = '<div class="mt-3 alert alert-danger" role="alert">Password baru tidak cocok!</div>';
      } else {
        $updateSql = "UPDATE users SET password='$newpassword' WHERE id=$id";
        if (mysqli_query($conn, $updateSql)) {
          $status = '<div class="mt-3 alert alert-success" role="alert">Password berhasil diperbarui!</div>';
        } else {
          $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal memperbarui password!</div>';
        }
      }
    } else {
      $status = '<div class="mt-3 alert alert-danger" role="alert">Password lama salah!</div>';
    }
  } else {
    $status = '<div class="mt-3 alert alert-info" role="alert">Silakan isi seluruh kolom password.</div>';
  }
}

$userRes = mysqli_query($conn, "SELECT * FROM users WHERE id=$userid");
$userData = ($userRes) ? mysqli_fetch_assoc($userRes) : null;

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-body text-center p-4">
          <img class="rounded-circle mb-3 border p-1" src="<?= BASE_URL ?>assets/images/brand/avatar.png" alt="Avatar" width="120" height="120">
          <h4 class="card-title fw-bold"><?= htmlspecialchars($userData['username'] ?? '') ?></h4>
          <span class="badge bg-secondary mb-3"><?= strtoupper($userData['privilege'] ?? 'USER') ?></span>

          <hr>

          <h5 class="fw-semibold text-start mb-3">Ubah Password</h5>
          <form action="" method="POST" autocomplete="off" class="text-start">
            <input type="hidden" name="id" value="<?= $userData['id'] ?? '' ?>">
            
            <div class="mb-3">
              <label class="form-label small text-muted">Username</label>
              <input class="form-control" type="text" value="<?= htmlspecialchars($userData['username'] ?? '') ?>" readonly>
            </div>

            <div class="mb-3">
              <label class="form-label small text-muted">Password Lama</label>
              <input class="form-control" type="password" name="oldpassword" placeholder="Password Lama" required>
            </div>

            <div class="mb-3">
              <label class="form-label small text-muted">Password Baru</label>
              <input class="form-control" type="password" name="newpassword" placeholder="Password Baru" required>
            </div>

            <div class="mb-3">
              <label class="form-label small text-muted">Ulangi Password Baru</label>
              <input class="form-control" type="password" name="newrepassword" placeholder="Konfirmasi Password Baru" required>
            </div>

            <?= $status ?>

            <div class="d-flex justify-content-end gap-2 mt-4">
              <button class="btn btn-secondary" type="reset">Reset</button>
              <button class="btn btn-primary" type="submit" name="submit">Simpan Perubahan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</main>

<?php
include __DIR__ . '/../includes/cart.php';
include __DIR__ . '/../includes/footer.php';
?>
