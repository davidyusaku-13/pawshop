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
<html lang="en">

<head>
  <title>Pawshop | Masuk</title>
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

          <?= render_flash() ?>
          <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <h1 class="h3 mb-3 fw-bold text-center">Masuk ke Akun</h1>

          <div class="form-floating mb-2">
            <input type="text" class="form-control" id="floatingInput" name="username" placeholder="Username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" />
            <label for="floatingInput">Username</label>
          </div>
          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password" required />
            <label for="floatingPassword">Password</label>
          </div>

          <div class="form-check text-start my-3">
            <input class="form-check-input" type="checkbox" value="yes" id="flexCheckDefault" name="remember-me" />
            <label class="form-check-label" for="flexCheckDefault">
              Ingat saya
            </label>
          </div>
          <button class="btn btn-warning w-100 py-2 fw-semibold" type="submit" name="submit">Masuk</button>
        </form>

        <p class="mt-4 mb-1 text-center">Belum punya akun? <a href="<?= BASE_URL ?>auth/register.php" class="text-decoration-none fw-bold">Daftar sekarang</a></p>
        <p class="mt-1 text-center"><a href="<?= BASE_URL ?>index.php" class="text-muted small text-decoration-none">&larr; Kembali ke Katalog</a></p>
      </div>
    </div>
  </main>

  <script src="<?= BASE_URL ?>assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
