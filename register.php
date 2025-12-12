<?php
include 'config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$record = "";
$wrongpass = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!csrfValidate()) {
        $record = '<div class="alert alert-danger" role="alert">Sesi tidak valid. Silakan coba lagi.</div>';
    } else {
        $username = post('username');
        $password = $_POST['password'] ?? '';
        $repassword = $_POST['repassword'] ?? '';
        $phone_number = post('phone_number');
        $secretKey = post('secretKey');

        if (!empty($username) && !empty($password) && !empty($repassword) && !empty($phone_number)) {
            if ($password !== $repassword) {
                $wrongpass = '<div class="alert alert-danger" role="alert">Password tidak sama!</div>';
            } else {
                // Determine privilege based on secret key
                $privilege = ($secretKey === ADMIN_SECRET_KEY) ? 'admin' : 'user';

                // Use the registerUser function from auth.php
                $result = registerUser($username, $password, $phone_number, $privilege);

                if ($result['success']) {
                    $record = '<div class="alert alert-success" role="alert">Akun berhasil dibuat! <a href="login.php">Masuk sekarang</a></div>';
                } else {
                    $record = '<div class="alert alert-danger" role="alert">' . e($result['error']) . '</div>';
                }
            }
        } else {
            $record = '<div class="alert alert-danger" role="alert">Semua field harus diisi!</div>';
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
            <?= csrfField() ?>
            <div class="text-center">
                <a href="./index.php" tabindex="-1">
                    <img class="img-fluid text-center mb-4" src="./logo.png" alt="logo.png">
                </a>
                <h1 class="h3 mb-3 fw-bold">Daftar</h1>
            </div>

            <div class="mb-3 form-floating">
                <input type="text" class="form-control" id="username" placeholder="" name="username" required maxlength="40" pattern="[a-zA-Z0-9_]{3,40}">
                <label for="username">Username</label>
            </div>
            <div class="mb-3 form-floating">
                <input type="password" class="form-control" id="password" placeholder="" name="password" required minlength="6">
                <label for="password">Password</label>
            </div>
            <div class="mb-3 form-floating">
                <input type="password" class="form-control" id="repassword" placeholder="" name="repassword" required minlength="6">
                <label for="repassword">Retype Password</label>
            </div>
            <div class="mb-3 form-floating">
                <input type="tel" class="form-control" id="phone_number" placeholder="" name="phone_number" required pattern="[\+]?[0-9]{8,15}">
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
