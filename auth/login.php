<?php
require_once __DIR__ . '/../config/database.php';

use App\AuthService;

if (AuthService::isLoggedIn()) {
    if (AuthService::isAdmin()) {
        redirect('admin/');
    }
    redirect('');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $rememberMe = isset($_POST['remember-me']) && $_POST['remember-me'] === 'yes';

    if (!empty($username) && !empty($password)) {
        if (AuthService::login($username, $password, $rememberMe)) {
            flash('success', "Selamat datang kembali, " . htmlspecialchars($_SESSION['username']) . "!");
            if (AuthService::isAdmin()) {
                redirect('admin/');
            }
            redirect('');
        } else {
            $error = 'Username atau kata sandi yang Anda masukkan salah!';
        }
    } else {
        $error = 'Harap isi kolom username dan kata sandi!';
    }
}
?>
<!doctype html>
<html lang="id">

<head>
  <title>Pawshop | Masuk ke Akun</title>
  <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>assets/images/brand/logo-title.png">
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  
  <!-- Google Fonts: Montserrat & Preconnect -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://kit.fontawesome.com" crossorigin>
  <link rel="preconnect" href="https://ka-f.fontawesome.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700&display=swap" rel="stylesheet">
  
  <!-- Stylesheets -->
  <link href="<?= BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
  <script src="https://kit.fontawesome.com/ec712a3d01.js" crossorigin="anonymous" defer></script>
</head>

<body>
  <div class="auth-wrapper">
    <div class="auth-card">
      <div class="row g-0">
        <!-- Visual Banner Side -->
        <div class="col-md-5 d-none d-md-block">
          <div class="auth-banner h-100">
            <div>
              <span class="brand-badge">
                <i class="fa-solid fa-paw"></i> Pawshop Pet Care
              </span>
            </div>
            <div>
              <p class="banner-quote mb-2">Nutrisi dan perlengkapan terlengkap untuk kucing kesayangan Anda.</p>
              <span class="small opacity-75">Belanja aman, cepat, dan terpercaya</span>
            </div>
            <div class="small opacity-50">
              &copy; <?= date('Y') ?> Pawshop Indonesia
            </div>
          </div>
        </div>

        <!-- Form Side -->
        <div class="col-md-7">
          <div class="auth-form-side">
            <div class="mb-4">
              <a href="<?= BASE_URL ?>">
                <img src="<?= BASE_URL ?>assets/images/brand/logo.png" height="38" alt="Pawshop Logo">
              </a>
              <h3 class="fw-bold text-dark mt-3 mb-1">Masuk ke Akun</h3>
              <p class="text-muted small mb-0">Akses keranjang dan pantau riwayat pesanan Anda.</p>
            </div>

            <?= render_flash() ?>
            <?php if (!empty($error)): ?>
              <div class="alert alert-danger py-2 small" role="alert">
                <i class="fa-solid fa-circle-exclamation me-1"></i> <?= htmlspecialchars($error) ?>
              </div>
            <?php endif; ?>

            <form action="" method="POST">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="floatingUsername" name="username" placeholder="Username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                <label for="floatingUsername"><i class="fa-solid fa-user text-muted me-1"></i> Username</label>
              </div>

              <div class="form-floating mb-3">
                <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password" required>
                <label for="floatingPassword"><i class="fa-solid fa-lock text-muted me-1"></i> Kata Sandi</label>
              </div>

              <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="yes" id="rememberMe" name="remember-me">
                  <label class="form-check-label text-muted small" for="rememberMe">
                    Ingat sesi saya
                  </label>
                </div>
              </div>

              <button class="btn btn-pawshop w-100 py-2 fs-6 shadow-sm mb-3" type="submit" name="submit">
                <i class="fa-solid fa-right-to-bracket me-1"></i> Masuk Sekarang
              </button>
            </form>

            <div class="text-center pt-2 border-top">
              <p class="text-muted small mb-1">
                Belum memiliki akun? <a href="<?= BASE_URL ?>auth/register" class="fw-bold text-dark text-decoration-none">Daftar Akun Baru</a>
              </p>
              <a href="<?= BASE_URL ?>" class="text-muted small text-decoration-none">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Beranda Katalog
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="<?= BASE_URL ?>assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
