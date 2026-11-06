<?php
require_once __DIR__ . '/../config/database.php';

use App\AuthService;

if (AuthService::isLoggedIn()) {
    redirect('index.php');
}

$status = "";
$adminSecret = "2023";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $repassword = $_POST['repassword'] ?? '';
    $phone_number = trim($_POST['phone_number'] ?? '');
    $secretKey = trim($_POST['secretKey'] ?? '');

    if (empty($username) || empty($password) || empty($repassword) || empty($phone_number)) {
        $status = '<div class="alert alert-danger" role="alert">Harap lengkapi semua data pendaftaran!</div>';
    } elseif ($password !== $repassword) {
        $status = '<div class="alert alert-danger" role="alert">Konfirmasi password tidak cocok!</div>';
    } elseif (strlen($password) < 4) {
        $status = '<div class="alert alert-danger" role="alert">Password minimal 4 karakter!</div>';
    } else {
        $privilege = ($secretKey === $adminSecret) ? 'admin' : 'user';
        if (AuthService::register($username, $password, $phone_number, $privilege)) {
            flash('success', 'Akun berhasil didaftarkan! Silakan masuk dengan akun baru Anda.');
            redirect('auth/login.php');
        } else {
            $status = '<div class="alert alert-danger" role="alert">Username sudah digunakan oleh akun lain!</div>';
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
  <title>Pawshop | Daftar</title>
  <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>assets/images/brand/logo-title.png">
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <link href="<?= BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/sign-in.css">
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
    <div class="row shadow rounded">
      <div class="col d-none d-md-block bg-image rounded-start"></div>
      <div class="col p-4">
        <form action="" method="POST">
          <div class="d-flex justify-content-center">
            <a href="<?= BASE_URL ?>index.php">
              <img class="mb-4" src="<?= BASE_URL ?>assets/images/brand/logo.png" alt="Pawshop Logo" width="150" />
            </a>
          </div>

          <?= $status; ?>

          <h1 class="h3 mb-3 fw-bold text-center">Buat Akun Baru</h1>

          <div class="form-floating mb-2">
            <input type="text" class="form-control" id="floatingUsername" name="username" placeholder="Username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" />
            <label for="floatingUsername">Username</label>
          </div>
          <div class="form-floating mb-2">
            <input type="tel" class="form-control" id="floatingPhone" name="phone_number" placeholder="Nomor Telepon" required value="<?= htmlspecialchars($_POST['phone_number'] ?? '') ?>" />
            <label for="floatingPhone">Nomor Telepon</label>
          </div>
          <div class="form-floating mb-2">
            <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password" required />
            <label for="floatingPassword">Password</label>
          </div>
          <div class="form-floating mb-2">
            <input type="password" class="form-control" id="floatingRepassword" name="repassword" placeholder="Ulangi Password" required />
            <label for="floatingRepassword">Konfirmasi Password</label>
          </div>
          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="floatingSecretKey" name="secretKey" placeholder="Secret Key (Opsional)" />
            <label for="floatingSecretKey">Secret Key Admin (Opsional)</label>
          </div>

          <button class="btn btn-warning w-100 py-2 fw-semibold" type="submit" name="submit">Daftar Sekarang</button>
        </form>

        <p class="mt-4 mb-1 text-center">Sudah punya akun? <a href="<?= BASE_URL ?>auth/login.php" class="text-decoration-none fw-bold">Masuk di sini</a></p>
        <p class="mt-1 text-center"><a href="<?= BASE_URL ?>index.php" class="text-muted small text-decoration-none">&larr; Kembali ke Katalog</a></p>
      </div>
    </div>
  </main>

  <script src="<?= BASE_URL ?>assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
