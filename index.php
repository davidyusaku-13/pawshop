<?php
include 'config.php';
session_start();

$privilege = '';
if (isset($_SESSION) && isset($_SESSION['userid']) && isset($_SESSION['privilege'])) {
  $privilege = $_SESSION['privilege'];
  $userid = $_SESSION['userid'];
}

$search = '';
$kategori = '';
$total = 0;

if ($privilege == 'admin') {
  // TAMPILAN ADMIN
  include 'admin.php';
  // TAMPILAN ADMIN
} else {
  // TAMPILAN USER
  include 'user.php';
  // TAMPILAN USER
}
