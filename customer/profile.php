<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth-guard.php';

use App\AuthService;
use App\OrderService;

$pageTitle = "Pawshop | Profil Pengguna";
$status = '';
$userId = AuthService::getCurrentUserId();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $oldpassword = $_POST['oldpassword'] ?? '';
    $newpassword = $_POST['newpassword'] ?? '';
    $newrepassword = $_POST['newrepassword'] ?? '';

    if (empty($oldpassword) || empty($newpassword) || empty($newrepassword)) {
        $status = '<div class="alert alert-info py-2 small" role="alert"><i class="fa-solid fa-circle-info me-1"></i> Silakan isi semua kolom perubahan password.</div>';
    } elseif ($newpassword !== $newrepassword) {
        $status = '<div class="alert alert-danger py-2 small" role="alert"><i class="fa-solid fa-circle-exclamation me-1"></i> Konfirmasi password baru tidak cocok!</div>';
    } elseif (strlen($newpassword) < 4) {
        $status = '<div class="alert alert-danger py-2 small" role="alert"><i class="fa-solid fa-circle-exclamation me-1"></i> Password baru minimal 4 karakter!</div>';
    } else {
        if (AuthService::changePassword($userId, $oldpassword, $newpassword)) {
            $status = '<div class="alert alert-success py-2 small" role="alert"><i class="fa-solid fa-circle-check me-1"></i> Password berhasil diperbarui!</div>';
        } else {
            $status = '<div class="alert alert-danger py-2 small" role="alert"><i class="fa-solid fa-circle-exclamation me-1"></i> Password lama yang Anda masukkan salah!</div>';
        }
    }
}

$userData = AuthService::getUserById($userId);
$userOrders = OrderService::getOrdersByUser($userId);
$totalOrders = count($userOrders);
$pendingOrders = count(array_filter($userOrders, fn($o) => (int)$o['status_id'] === 1));
$completedOrders = count(array_filter($userOrders, fn($o) => (int)$o['status_id'] === 4));

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="container py-4">
  <!-- Page Header -->
  <div class="mb-4">
    <h3 class="fw-bold mb-1">Pengaturan Akun</h3>
    <p class="text-muted small mb-0">Kelola informasi profil pribadi dan keamanan kata sandi Anda</p>
  </div>

  <?= render_flash() ?>

  <div class="row g-4">
    <!-- Left Column: User Profile Card & Stats -->
    <div class="col-lg-4">
      <div class="card border rounded-4 shadow-sm bg-white p-4 text-center mb-4">
        <div class="mx-auto mb-3 position-relative" style="width: 96px; height: 96px;">
          <div class="bg-warning text-dark rounded-circle w-100 h-100 d-flex align-items-center justify-content-center fs-1 fw-bold border border-3 border-white shadow-sm">
            <?= strtoupper(substr($userData['username'] ?? 'U', 0, 1)) ?>
          </div>
        </div>
        <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($userData['username'] ?? '') ?></h5>
        <div>
          <span class="badge <?= ($userData['privilege'] === 'admin') ? 'bg-danger' : 'bg-primary' ?> px-3 py-1 rounded-pill small">
            <?= strtoupper($userData['privilege'] ?? 'USER') ?>
          </span>
        </div>

        <div class="border-top mt-4 pt-3 text-start">
          <div class="mb-2">
            <span class="text-muted small d-block">Nomor WhatsApp / HP:</span>
            <span class="fw-semibold text-dark"><?= htmlspecialchars($userData['phone_number'] ?? '-') ?></span>
          </div>
          <div>
            <span class="text-muted small d-block">Status Akun:</span>
            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
              <i class="fa-solid fa-circle-check me-1"></i> Aktif Terverifikasi
            </span>
          </div>
        </div>
      </div>

      <!-- Quick Stats Card -->
      <div class="card border rounded-4 shadow-sm bg-white p-4">
        <h6 class="fw-bold text-dark mb-3">Ringkasan Aktivitas</h6>
        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
          <span class="text-muted small">Total Pesanan:</span>
          <span class="fw-bold text-dark"><?= $totalOrders ?> Transaksi</span>
        </div>
        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
          <span class="text-muted small">Menunggu Bayar:</span>
          <span class="fw-bold text-warning"><?= $pendingOrders ?> Pesanan</span>
        </div>
        <div class="d-flex justify-content-between align-items-center py-2">
          <span class="text-muted small">Pesanan Selesai:</span>
          <span class="fw-bold text-success"><?= $completedOrders ?> Pesanan</span>
        </div>
        <a href="<?= BASE_URL ?>customer/transactions.php" class="btn btn-outline-secondary btn-sm rounded-pill w-100 mt-3">
          <i class="fa-solid fa-clock-rotate-left me-1"></i> Buka Riwayat Transaksi
        </a>
      </div>
    </div>

    <!-- Right Column: Password Management -->
    <div class="col-lg-8">
      <div class="card border rounded-4 shadow-sm bg-white p-4">
        <div class="d-flex align-items-center gap-2 mb-3 pb-3 border-bottom">
          <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
            <i class="fa-solid fa-lock"></i>
          </div>
          <div>
            <h5 class="fw-bold mb-0">Keamanan & Ganti Kata Sandi</h5>
            <span class="text-muted small">Perbarui kata sandi secara berkala untuk menjaga keamanan akun Anda</span>
          </div>
        </div>

        <?= $status; ?>

        <form action="" method="POST" class="mt-3">
          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="oldpassword" name="oldpassword" placeholder="Password Lama" required>
            <label for="oldpassword"><i class="fa-solid fa-key text-muted me-1"></i> Kata Sandi Saat Ini</label>
          </div>

          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="newpassword" name="newpassword" placeholder="Password Baru" required>
            <label for="newpassword"><i class="fa-solid fa-lock text-muted me-1"></i> Kata Sandi Baru (Min. 4 Karakter)</label>
          </div>

          <div class="form-floating mb-4">
            <input type="password" class="form-control" id="newrepassword" name="newrepassword" placeholder="Konfirmasi Password Baru" required>
            <label for="newrepassword"><i class="fa-solid fa-shield-halved text-muted me-1"></i> Konfirmasi Kata Sandi Baru</label>
          </div>

          <button type="submit" name="submit" class="btn btn-pawshop px-4 py-2 fw-bold rounded-pill shadow-sm">
            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Kata Sandi Baru
          </button>
        </form>
      </div>

      <!-- Quick Help & Shopping CTA -->
      <div class="card border rounded-4 bg-light p-4 mt-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
          <div>
            <h6 class="fw-bold text-dark mb-1">Butuh Bantuan dengan Pesanan Anda?</h6>
            <p class="text-muted small mb-0">Tim Pawshop siap membantu setiap hari mulai pukul 08:00 - 21:00 WIB.</p>
          </div>
          <a href="<?= BASE_URL ?>index.php" class="btn btn-outline-dark btn-sm rounded-pill px-3 flex-shrink-0">
            <i class="fa-solid fa-basket-shopping me-1"></i> Jelajahi Toko
          </a>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
