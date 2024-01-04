<?php
include 'config.php';
session_start();

if (isset($_SESSION['userid']) && $_SESSION['userid'] != null) {
  header('Location: index.php');
}

$wrongpass = '';
if (isset($_POST['submit'])) {
  if ($_POST['username'] != null && $_POST['password'] != null) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
      while ($rows = mysqli_fetch_assoc($result)) {
        $_SESSION['userid'] = $rows['id'];
        $_SESSION['privilege'] = $rows['privilege'];
      }
      header('Location: index.php');
    } else {
      $wrongpass = '<div class="alert alert-danger" role="alert">Username atau password salah!</div>';
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <link rel="stylesheet" href="./css/sign-in.css">
  <style>
    .bg-image {
      background: url('./img/carousel2.jpg');
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
      background-color: rgba(255, 255, 255, 0.5);
      padding: 20px;
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
        <h1 class="text-center h3 mb-3 fw-bold">Masuk</h1>
      </div>

      <div class="mb-3 form-floating">
        <input type="text" class="form-control" id="username" placeholder="" name="username" required>
        <label for="username">Username</label>
      </div>
      <div class="mb-3 form-floating">
        <input type="password" class="form-control" id="password" placeholder="" name="password" required>
        <label for="username">Password</label>
      </div>

      <div class="form-check text-start my-3">
        <input class="form-check-input" type="checkbox" value="remember-me" id="flexCheckDefault">
        <label class="form-check-label" for="flexCheckDefault">
          Ingat saya
        </label>
      </div>
      <?= $wrongpass; ?>
      <button class="btn btn-primary w-100 py-2" name="submit" type="submit">Masuk</button>
      <p class="mt-1 text-center">Belum punya akun? <a href="./register.php">Daftar di sini</a></p>
      <p class="text-center text-body-secondary">&copy; 2023 Pawshop, Inc</p>
    </form>
  </main>
  <footer>
    <!-- place footer here -->
  </footer>
  <!-- Bootstrap JavaScript Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>

</html>