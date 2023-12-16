<?php
include "config.php";
session_start();
if (!isset($_SESSION['idsession']) && !isset($_SESSION['usersession'])) {
    header('Location: login.php');
} else {
    $idsession = $_SESSION['idsession'];
}
if (isset($_POST['submit'])) {
    var_dump($_POST);
    if (isset($_POST['id']) && isset($_FILES) && isset($_POST['nama_produk']) && isset($_POST['stok'])) {
        // AMBIL DATA DARI EDIT.PHP
        $id = $_POST['id'];
        $gambar = basename($_FILES["gambar"]["name"]);
        $nama_produk = $_POST['nama_produk'];
        $stok = $_POST['stok'];
        $harga = $_POST['harga'];


        // UPDATE DATABASE
        if ($gambar == "") {
            $sql = "UPDATE product SET nama_produk='$nama_produk', stok='$stok', harga='$harga' WHERE id=$id";
        } else {
            $sql = "UPDATE product SET gambar='$gambar', nama_produk='$nama_produk', stok='$stok', harga='$harga' WHERE id=$id";

            // AMBIL DATA GAMBAR
            $target_dir = "./img/";
            $target_file = $target_dir . basename($_FILES["gambar"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["gambar"]["tmp_name"]);
            if ($check !== false) {
                echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }

            if ($uploadOk == 0) {
                echo "Sorry, your file was not uploaded.";
                // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                    // FINAL UPLOAD FILE SETELAH CHECK MACEM2
                    echo "The file " . htmlspecialchars(basename($_FILES["gambar"]["name"])) . " has been uploaded.";
                    header('Location: index.php');
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
        }

        if (mysqli_query($conn, $sql)) {
            echo "Record updated successfully";
            header('Location: index.php');
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }
    }
} else {
    header('Location: index.php');
}

mysqli_close($conn);
