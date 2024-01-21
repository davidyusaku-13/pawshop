<?php
include 'config.php';

if (isset($_SESSION['userid']) && $_SESSION['userid'] != null) {
  header('Location: index.php');
}

$record = "";
$wrongpass = "";
$key = md5(2023);
if (isset($_POST['submit'])) {
  if ($_POST['username'] != null && $_POST['password'] != null && $_POST['repassword'] != null && $_POST['phone_number'] != null) {
    if ($_POST['password'] == $_POST['repassword']) {
      $username = $_POST['username'];
      $password = md5($_POST['password']);
      $repassword = md5($_POST['repassword']);
      $phone_number = $_POST['phone_number'];
      $secretKey = md5($_POST['secretKey']);

      $fetch = "SELECT * FROM users WHERE username='$username'";
      $res = mysqli_query($conn, $fetch);
      if (mysqli_num_rows($res) > 0) {
        $record = '<div class="alert alert-danger" role="alert">Username is already used!</div>';
      } else {
        if ($secretKey == $key) {
          $insertUser = "INSERT INTO users (username, password, phone_number, remember_token, privilege) VALUES ('$username', '$password', '$phone_number', '', 'admin')";
        } else {
          $insertUser = "INSERT INTO users (username, password, phone_number, remember_token, privilege) VALUES ('$username', '$password', '$phone_number', '', 'user')";
        }
        if (mysqli_query($conn, $insertUser)) {
          $record = '<div class="alert alert-success" role="alert">Akun berhasil dibuat!</div>';
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
  <title>Pawshop</title>
  <link rel="icon" type="image/x-icon" href="./logo-title.png">
  <!-- Required meta tags -->
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

  <!-- Bootstrap CSS v5.2.1 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
  <link rel="stylesheet" href="./css/sign-in.css">
  <style>
    .bg-image {
      background: url('./img/carousel2.jpg');
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
      background-color: rgba(255, 255, 255, 0.5) !important;
      padding: 20px !important;
    }
  </style>
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary">
  <div class="bg-image"></div>
  <main class="form-signin w-100 m-auto">
    <form action="" method="POST" autocomplete="off">
      <div class="text-center">
        <a href="./index.php" tabindex="-1">
          <img class="img-fluid text-center mb-4" src="./logo.png" alt="logo.png">
        </a>
        <h1 class="h3 mb-3 fw-bold">Daftar</h1>
      </div>

      <div class="mb-3 form-floating">
        <input type="text" class="form-control" id="username" placeholder="" name="username" required>
        <label for="username">Username</label>
      </div>
      <div class="mb-3 form-floating">
        <input type="password" class="form-control" id="password" placeholder="" name="password" required>
        <label for="password">Password</label>
      </div>
      <div class="mb-3 form-floating">
        <input type="password" class="form-control" id="repassword" placeholder="" name="repassword" required>
        <label for="repassword">Retype Password</label>
      </div>
      <div class="mb-3 form-floating">
        <input type="tel" class="form-control" id="phone_number" placeholder="" name="phone_number" required>
        <label for="phone_number">Phone Number</label>
      </div>
      <div class="mb-3 form-floating">
        <input type="password" class="form-control" id="secretKey" placeholder="" name="secretKey">
        <label for="secretKey">Secret Key (to become Admin)</label>
      </div>
      <?= $wrongpass; ?>
      <?= $record; ?>
      <button class="btn btn-primary w-100 py-2" name="submit" type="submit">Daftar</button>
      <p class="mt-1 text-center">Sudah punya akun? <a href="./login.php">Masuk</a></p>
      <p class="text-center text-body-secondary">&copy; 2023 Pawshop, Inc</p>
    </form>
  </main>
  <footer>
    <!-- place footer here -->
  </footer>
  <!-- Bootstrap JavaScript Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>

</html>