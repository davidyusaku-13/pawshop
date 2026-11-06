<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth-guard.php';

use App\AuthService;

$pageTitle = "Pawshop | Profil Pengguna";
$status = '';
$userId = AuthService::getCurrentUserId();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $oldpassword = $_POST['oldpassword'] ?? '';
    $newpassword = $_POST['newpassword'] ?? '';
    $newrepassword = $_POST['newrepassword'] ?? '';

    if (empty($oldpassword) || empty($newpassword) || empty($newrepassword)) {
        $status = '<div class="alert alert-info" role="alert">Silakan isi semua kolom perubahan password.</div>';
    } elseif ($newpassword !== $newrepassword) {
        $status = '<div class="alert alert-danger" role="alert">Konfirmasi password baru tidak cocok!</div>';
    } elseif (strlen($newpassword) < 4) {
        $status = '<div class="alert alert-danger" role="alert">Password baru minimal 4 karakter!</div>';
    } else {
        if (AuthService::changePassword($userId, $oldpassword, $newpassword)) {
            $status = '<div class="alert alert-success" role="alert">Password berhasil diperbarui!</div>';
        } else {
            $status = '<div class="alert alert-danger" role="alert">Password lama yang Anda masukkan salah!</div>';
        }
    }
}

$userData = AuthService::getUserById($userId);

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

          <div class="text-start bg-light p-3 rounded mb-4">
            <p class="mb-1 text-muted small">Nomor Telepon:</p>
            <p class="fw-semibold mb-0"><?= htmlspecialchars($userData['phone_number'] ?? '-') ?></p>
          </div>

          <?= $status; ?>

          <h5 class="fw-bold mb-3 text-start">Ganti Password</h5>
          <form action="" method="POST" class="text-start">
            <div class="form-floating mb-2">
              <input type="password" class="form-control" id="oldpassword" name="oldpassword" placeholder="Password Lama" required>
              <label for="oldpassword">Password Lama</label>
            </div>
            <div class="form-floating mb-2">
              <input type="password" class="form-control" id="newpassword" name="newpassword" placeholder="Password Baru" required>
              <label for="newpassword">Password Baru</label>
            </div>
            <div class="form-floating mb-3">
              <input type="password" class="form-control" id="newrepassword" name="newrepassword" placeholder="Konfirmasi Password Baru" required>
              <label for="newrepassword">Ulangi Password Baru</label>
            </div>
            <button type="submit" name="submit" class="btn btn-warning w-100 fw-semibold">Simpan Perubahan</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
