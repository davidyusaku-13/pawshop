<?php
require_once __DIR__ . '/../config/database.php';

use App\AuthService;

if (AuthService::isLoggedIn()) {
    redirect('index.php');
}

$status = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $repassword = $_POST['repassword'] ?? '';
    $phone_number = trim($_POST['phone_number'] ?? '');

    if (empty($username) || empty($password) || empty($repassword) || empty($phone_number)) {
        $status = '<div class="alert alert-danger" role="alert">Harap lengkapi semua kolom pendaftaran!</div>';
    } elseif ($password !== $repassword) {
        $status = '<div class="alert alert-danger" role="alert">Konfirmasi password tidak cocok!</div>';
    } elseif (strlen($password) < 4) {
        $status = '<div class="alert alert-danger" role="alert">Password minimal 4 karakter!</div>';
    } else {
        if (AuthService::register($username, $password, $phone_number, 'user')) {
            flash('success', 'Pendaftaran akun berhasil! Silakan masuk dengan akun baru Anda.');
            redirect('auth/login.php');
        } else {
            $status = '<div class="alert alert-danger" role="alert">Username sudah terdaftar. Silakan pilih username lain!</div>';
        }
    }
}
?>
<!doctype html>
<html lang="id">

<head>
  <title>Pawshop | Daftar Akun</title>
  <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>assets/images/brand/logo-title.png">
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  
  <!-- Google Fonts: Montserrat -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700&display=swap" rel="stylesheet">
  
  <link href="<?= BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/sign-in.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
  <script src="https://kit.fontawesome.com/ec712a3d01.js" crossorigin="anonymous"></script>
  <style>
    .bg-image {
      background: url('<?= BASE_URL ?>assets/images/carousel2.jpg');
      background-position: center;
      background-size: cover;
      background-repeat: no-repeat;
    }
  </style>
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary">
  <main class="w-100 m-auto form-signin">
    <div class="row shadow-sm rounded-4 overflow-hidden bg-white border">
      <div class="col-md-5 d-none d-md-block bg-image"></div>
      <div class="col-md-7 p-4 p-lg-5">
        <form action="" method="POST">
          <div class="d-flex justify-content-center mb-3">
            <a href="<?= BASE_URL ?>index.php">
              <img src="<?= BASE_URL ?>assets/images/brand/logo.png" alt="Pawshop Logo" width="160" />
            </a>
          </div>

          <h2 class="h4 mb-2 fw-bold text-center">Buat Akun Pelanggan</h2>
          <p class="text-muted small text-center mb-4">Daftar untuk menikmati belanja kebutuhan anabul lebih mudah</p>

          <?= $status; ?>

          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="floatingUsername" name="username" placeholder="Username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" />
            <label for="floatingUsername"><i class="fa-solid fa-user me-1 text-muted"></i> Username</label>
          </div>

          <div class="form-floating mb-3">
            <input type="tel" class="form-control" id="floatingPhone" name="phone_number" placeholder="Nomor Telepon" required value="<?= htmlspecialchars($_POST['phone_number'] ?? '') ?>" />
            <label for="floatingPhone"><i class="fa-solid fa-phone me-1 text-muted"></i> Nomor WhatsApp / HP</label>
          </div>

          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password" required />
            <label for="floatingPassword"><i class="fa-solid fa-lock me-1 text-muted"></i> Password</label>
          </div>

          <div class="form-floating mb-4">
            <input type="password" class="form-control" id="floatingRepassword" name="repassword" placeholder="Ulangi Password" required />
            <label for="floatingRepassword"><i class="fa-solid fa-shield-check me-1 text-muted"></i> Konfirmasi Password</label>
          </div>

          <button class="btn btn-pawshop w-100 py-2 fs-6 shadow-sm" type="submit" name="submit">
            <i class="fa-solid fa-user-plus me-1"></i> Daftar Sekarang
          </button>
        </form>

        <div class="mt-4 text-center">
          <p class="mb-1 text-muted small">Sudah memiliki akun? <a href="<?= BASE_URL ?>auth/login.php" class="fw-bold text-decoration-none text-dark">Masuk di sini</a></p>
          <a href="<?= BASE_URL ?>index.php" class="text-muted small text-decoration-none">&larr; Kembali ke Katalog Utama</a>
        </div>
      </div>
    </div>
  </main>

  <script src="<?= BASE_URL ?>assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
