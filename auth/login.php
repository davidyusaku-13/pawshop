<?php
require_once __DIR__ . '/../config/database.php';

if ((isset($_SESSION['userid']) && $_SESSION['userid'] != null) || (isset($_COOKIE['userid']) && $_COOKIE['userid'] != null)) {
  if (isset($_SESSION['privilege']) && $_SESSION['privilege'] === 'admin') {
    header('Location: ' . BASE_URL . 'admin/index.php');
  } else {
    header('Location: ' . BASE_URL . 'index.php');
  }
  exit();
}

$wrongpass = '';
if (isset($_POST['submit'])) {
  if (!empty($_POST['username']) && !empty($_POST['password'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if ($result && $result->num_rows > 0) {
      $rows = $result->fetch_assoc();
      if (isset($_POST['remember-me']) && $_POST['remember-me'] == "yes") {
        setcookie('userid', $rows['id'], time() + (86400 * 30), "/");
        setcookie('privilege', $rows['privilege'], time() + (86400 * 30), "/");
        setcookie('remember-me', 'yes', time() + (86400 * 30), "/");
      }
      $_SESSION['userid'] = $rows['id'];
      $_SESSION['privilege'] = $rows['privilege'];

      if ($rows['privilege'] === 'admin') {
        header('Location: ' . BASE_URL . 'admin/index.php');
      } else {
        header('Location: ' . BASE_URL . 'index.php');
      }
      exit();
    } else {
      $wrongpass = '<div class="alert alert-danger" role="alert">Username atau password salah!</div>';
    }
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
      filter: blur(6px);
      -webkit-filter: blur(6px);
      width: 100%;
      height: 100%;
      position: absolute;
      z-index: -1;
    }

    .form-signin {
      background-color: rgba(255, 255, 255, 0.85);
      border-radius: 8px;
      padding: 24px;
      max-width: 400px;
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
        <h1 class="text-center h3 mb-3 fw-bold">Masuk</h1>
      </div>

      <div class="mb-3 form-floating">
        <input type="text" class="form-control" id="username" placeholder="Username" name="username" required>
        <label for="username">Username</label>
      </div>
      <div class="mb-3 form-floating">
        <input type="password" class="form-control" id="password" placeholder="Password" name="password" required>
        <label for="password">Password</label>
      </div>

      <div class="form-check text-start my-3">
        <input class="form-check-input" type="checkbox" name="remember-me" value="yes" id="flexCheckDefault">
        <label class="form-check-label" for="flexCheckDefault">
          Ingat saya
        </label>
      </div>
      <?= $wrongpass; ?>
      <button class="btn btn-primary w-100 py-2" name="submit" type="submit">Masuk</button>
      <p class="mt-3 text-center">Belum punya akun? <a href="<?= BASE_URL ?>auth/register.php">Daftar di sini</a></p>
      <p class="text-center text-body-secondary">&copy; Pawshop</p>
    </form>
  </main>
  <script src="<?= BASE_URL ?>assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
