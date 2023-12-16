<?php
include "config.php";
session_start();
if (isset($_SESSION['idsession']) && isset($_SESSION['usersession'])) {
    header('Location: index.php');
} else {
    if (isset($_POST['submit'])) {
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $sql = "SELECT * FROM users";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            // output data of each row
            while ($row = mysqli_fetch_assoc($result)) {
                if ($password == $row['password']) {
                    $_SESSION['idsession'] = $row['id'];
                    $_SESSION['usersession'] = $row['username'];
                    header('Location: index.php');
                } else {
                    header('Location: login.php');
                }
            }
        } else {
            echo "0 results";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<title>Pawshop</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="./css/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

<body>
    <!-- Header -->
    <div class="w3-teal">
        <div class="w3-container">
            <center>
                <h1>Please Login First!</h1>
            </center>
        </div>
    </div>

    <!-- Content -->
    <div class="w3-container w3-margin-top">
        <div class="w3-card" style="width: 25%; margin-left: 37.5%;">
            <form class="w3-container" method="POST" action="" enctype="multipart/form-data">
                <p>
                    <label class="w3-text-teal"><b>Username</b></label>
                    <input class="w3-input w3-border w3-light-grey" type="text" name="username" required>
                </p>
                <p>
                    <label class="w3-text-teal"><b>Password</b></label>
                    <input class="w3-input w3-border w3-light-grey" type="password" name="password" required>
                </p>
                <center>
                    <p> <button class="w3-button w3-blue-grey w3-hover-teal" type="submit" name="submit">Login</button> </p>
                    <p>Don't have an account? <a href="register.php">Register</a></p>
                </center>
            </form>
        </div>
    </div>
</body>

</html>