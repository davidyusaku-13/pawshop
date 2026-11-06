<?php
$pageTitle = isset($pageTitle) ? $pageTitle : "Pawshop | Panel Admin";
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>assets/images/brand/logo-title.png">
  
  <!-- Preconnect for external CDNs -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://kit.fontawesome.com" crossorigin>
  <link rel="preconnect" href="https://ka-f.fontawesome.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800&display=swap" rel="stylesheet">
  
  <!-- Bootstrap 5 & DataTables CSS -->
  <link href="<?= BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/datatables.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
  
  <!-- Scripts -->
  <script src="<?= BASE_URL ?>assets/js/jquery-3.7.1.min.js"></script>
  <script src="<?= BASE_URL ?>assets/js/datatables.min.js"></script>
  <script src="https://kit.fontawesome.com/ec712a3d01.js" crossorigin="anonymous" defer></script>
</head>
<body>

<div class="admin-wrapper">
  <!-- SIDEBAR -->
  <?php include __DIR__ . '/admin-sidebar.php'; ?>

  <!-- MAIN CONTENT AREA -->
  <div class="admin-content">
    <header class="admin-topbar">
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-outline-secondary btn-sm d-lg-none" type="button" id="sidebarToggleBtn">
          <i class="fa-solid fa-bars"></i>
        </button>
        <h5 class="mb-0 fw-bold text-dark"><?= htmlspecialchars($pageTitle) ?></h5>
      </div>
      <div class="d-flex align-items-center gap-3">
        <a href="<?= BASE_URL ?>index.php" target="_blank" class="btn btn-outline-dark btn-sm">
          <i class="fa-solid fa-store me-1"></i> Toko Utama
        </a>
      </div>
    </header>

    <main class="p-3 p-lg-4">
