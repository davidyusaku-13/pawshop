<?php
include 'config.php';
session_start();
if (!isset($_SESSION['idsession']) && !isset($_SESSION['usersession'])) {
    header('Location: login.php');
} else {
    $idsession = $_SESSION['idsession'];
}
if (isset($_POST['submit'])) {
    if (isset($_POST['id']) && isset($_POST['username']) && isset($_POST['oldPassForm']) && isset($_POST['newPassForm']) && isset($_POST['reNewPassForm'])) {
        $id = $_POST['id'];
        $username = $_POST['username'];
        $oldPassForm = md5($_POST['oldPassForm']);
        $newPassForm = md5($_POST['newPassForm']);
        $reNewPassForm = md5($_POST['reNewPassForm']);

        // CEK newPass dan re-newPass
        if ($newPassForm != $reNewPassForm) {
            header('Location: profile.php');
        }

        $sql = "SELECT * FROM users";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            // output data of each row
            while ($row = mysqli_fetch_assoc($result)) {
                // CEK OldPass dan NewPass
                if ($oldPassForm != $row['password']) {
                    header('Location: profile.php');
                } else {
                    $sql = "UPDATE users SET username='$username', password='$newPassForm' WHERE id='$id'";

                    if (mysqli_query($conn, $sql)) {
                        echo "Record updated successfully";
                        header('Location: index.php');
                    } else {
                        echo "Error updating record: " . mysqli_error($conn);
                    }
                }
            }
        } else {
            echo "0 results";
        }
    }


    mysqli_close($conn);
}
