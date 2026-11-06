<?php
if (!isset($_SESSION['privilege']) || $_SESSION['privilege'] !== 'admin') {
    header('Location: ' . BASE_URL . 'auth/login.php');
    exit();
}
