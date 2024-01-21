<?php
include 'config.php';

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
