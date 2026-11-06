<?php
require_once __DIR__ . '/../config/database.php';

session_unset();
session_destroy();

$cookie_names = array('userid', 'privilege', 'remember-me');
foreach ($cookie_names as $cookie_name) {
  setcookie($cookie_name, '', time() - 3600, '/');
  unset($_COOKIE[$cookie_name]);
}

header('Location: ' . BASE_URL . 'auth/login.php');
exit();
