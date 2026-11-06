<?php
require_once __DIR__ . '/../config/database.php';

if (isset($_SESSION['userid']) && $_SESSION['userid'] != null) {
  header('Location: ' . BASE_URL . 'index.php');
  exit();
}

$record = "";
$wrongpass = "";
$key = md5("2023");

if (isset($_POST['submit'])) {
  if (!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['repassword']) && !empty($_POST['phone_number'])) {
    if ($_POST['password'] === $_POST['repassword']) {
      $username = mysqli_real_escape_string($conn, $_POST['username']);
      $password = md5($_POST['password']);
      $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
      $secretKey = md5($_POST['secretKey']);

      $fetch = "SELECT * FROM users WHERE username='$username'";
      $res = mysqli_query($conn, $fetch);
      if ($res && mysqli_num_rows($res) > 0) {
        $record = '<div class="alert alert-danger" role="alert">Username sudah digunakan!</div>';
      } else {
        $privilege = ($secretKey === $key) ? 'admin' : 'user';
        $insertUser = "INSERT INTO users (username, password, phone_number, remember_token, privilege) VALUES ('$username', '$password', '$phone_number', '', '$privilege')";
        if (mysqli_query($conn, $insertUser)) {
          $record = '<div class="alert alert-success" role="alert">Akun berhasil dibuat! Silakan <a href="' . BASE_URL . 'auth/login.php">masuk</a>.</div>';
        } else {
          $record = '<div class="alert alert-danger" role="alert">Akun gagal dibuat!</div>';
        }
      }
    } else {
      $wrongpass = '<div class="alert alert-danger" role="alert">Password tidak sama!</div>';
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
      filter: blur(4px);
      -webkit-filter: blur(4px);
      width: 100%;
      height: 100%;
      position: absolute;
      z-index: -1;
    }

    .form-signin {
      background-color: rgba(255, 255, 255, 0.85);
      border-radius: 8px;
      padding: 24px;
      max-width: 420px;
    }
  </style>
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary">
  <div class="bg-image"></div>
  <main class="form-signin w-100 m-auto shadow-sm">
    <form action="" method="POST" autocomplete="off">
      <div class="text-center">
        <a href="<?= BASE_URL ?>index.php" tabindex="-1">
          <img class="img-fluid text-center mb-4" src="<?= BASE_URL ?>assets/images/brand/logo.png" alt="Pawshop" style="max-height: 48px;">
        </a>
        <h1 class="text-center h3 mb-3 fw-bold">Daftar Akun</h1>
      </div>

      <div class="mb-3 form-floating">
        <input type="text" class="form-control" id="username" placeholder="Username" name="username" required>
        <label for="username">Username</label>
      </div>
      <div class="mb-3 form-floating">
        <input type="password" class="form-control" id="password" placeholder="Password" name="password" required>
        <label for="password">Password</label>
      </div>
      <div class="mb-3 form-floating">
        <input type="password" class="form-control" id="repassword" placeholder="Ulangi Password" name="repassword" required>
        <label for="repassword">Ulangi Password</label>
      </div>
      <div class="mb-3 form-floating">
        <input type="tel" class="form-control" id="phone_number" placeholder="Nomor Telepon" name="phone_number" required>
        <label for="phone_number">Nomor Telepon</label>
      </div>
      <div class="mb-3 form-floating">
        <input type="text" class="form-control" id="secretKey" placeholder="Secret Key (Khusus Admin)" name="secretKey">
        <label for="secretKey">Secret Key (Khusus Admin)</label>
      </div>
      <?= $wrongpass; ?>
      <?= $record; ?>
      <button class="btn btn-primary w-100 py-2" name="submit" type="submit">Daftar</button>
      <p class="mt-3 text-center">Sudah punya akun? <a href="<?= BASE_URL ?>auth/login.php">Masuk di sini</a></p>
      <p class="text-center text-body-secondary">&copy; Pawshop</p>
    </form>
  </main>
  <script src="<?= BASE_URL ?>assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
