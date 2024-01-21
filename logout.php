<?php
session_start();
session_destroy();
$cookie_names = array('userid', 'privilege', 'remember-me');
foreach ($cookie_names as $cookie_name) {
  setcookie($cookie_name, '', time() - 3600, '/');
  // Unset the cookie variable in the current request
  unset($_COOKIE[$cookie_name]);
}
header('Location: login.php');
