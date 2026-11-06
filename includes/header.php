<?php
$pageTitle = isset($pageTitle) ? $pageTitle : "Pawshop";
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>assets/images/brand/logo-title.png">
  
  <!-- Google Fonts: Montserrat & FontAwesome Preconnect -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://kit.fontawesome.com" crossorigin>
  <link rel="preconnect" href="https://ka-f.fontawesome.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
  
  <!-- CSS Stylesheets -->
  <link href="<?= BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
  
  <!-- FontAwesome (Deferred to prevent render blocking) -->
  <script src="https://kit.fontawesome.com/ec712a3d01.js" crossorigin="anonymous" defer></script>
</head>
<body class="d-flex flex-column min-vh-100">
