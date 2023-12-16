<?php
include "config.php";
session_start();
if (isset($_SESSION['idsession']) && isset($_SESSION['usersession'])) {
    header('Location: index.php');
} else {
    if (isset($_POST['submit'])) {
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $rePassword = md5($_POST['rePassword']);
        if ($password != $rePassword) {
            header('Location: register-kasir.php');
        } else {
            $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";

            if (mysqli_query($conn, $sql)) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
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
                <h1>Register Here!</h1>
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
                <p>
                    <label class="w3-text-teal"><b>Retype Password</b></label>
                    <input class="w3-input w3-border w3-light-grey" type="password" name="rePassword" required>
                </p>
                <center>
                    <p> <button class="w3-button w3-blue-grey w3-hover-teal" type="submit" name="submit">Register</button> </p>
                    <p>Already have an account? <a href="./login.php">Login</a></p>
                </center>
            </form>
        </div>
    </div>
</body>

</html>