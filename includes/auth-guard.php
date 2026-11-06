<?php
if (!isset($userid) || empty($userid)) {
    header('Location: ' . BASE_URL . 'auth/login.php');
    exit();
}
