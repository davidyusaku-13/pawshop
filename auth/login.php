<?php
require_once __DIR__ . '/../config/database.php';

use App\AuthService;

if (AuthService::isLoggedIn()) {
    if (AuthService::isAdmin()) {
        redirect('admin/index.php');
    }
    redirect('index.php');
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
                redirect('admin/index.php');
            }
            redirect('index.php');
        } else {
            $error = 'Username atau password yang Anda masukkan salah!';
        }
    } else {
        $error = 'Harap isi username dan password!';
    }
}
?>
<!doctype html>
<html lang="id">

<head>
  <title>Pawshop | Masuk</title>
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

          <h2 class="h4 mb-2 fw-bold text-center">Masuk ke Akun</h2>
          <p class="text-muted small text-center mb-4">Akses keranjang dan pantau riwayat belanja Anda</p>

          <?= render_flash() ?>
          <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="floatingInput" name="username" placeholder="Username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" />
            <label for="floatingInput"><i class="fa-solid fa-user me-1 text-muted"></i> Username</label>
          </div>
          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password" required />
            <label for="floatingPassword"><i class="fa-solid fa-lock me-1 text-muted"></i> Password</label>
          </div>

          <div class="form-check text-start my-3">
            <input class="form-check-input" type="checkbox" value="yes" id="flexCheckDefault" name="remember-me" />
            <label class="form-check-label text-muted small" for="flexCheckDefault">
              Ingat sesi saya di perangkat ini
            </label>
          </div>
          <button class="btn btn-pawshop w-100 py-2 fs-6 shadow-sm" type="submit" name="submit">
            <i class="fa-solid fa-right-to-bracket me-1"></i> Masuk
          </button>
        </form>

        <div class="mt-4 text-center">
          <p class="mb-1 text-muted small">Belum memiliki akun? <a href="<?= BASE_URL ?>auth/register.php" class="fw-bold text-decoration-none text-dark">Daftar sekarang</a></p>
          <a href="<?= BASE_URL ?>index.php" class="text-muted small text-decoration-none">&larr; Kembali ke Katalog Utama</a>
        </div>
      </div>
    </div>
  </main>

  <script src="<?= BASE_URL ?>assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
