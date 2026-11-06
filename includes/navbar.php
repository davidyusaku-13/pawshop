  <!-- Start Navbar -->
  <header>
    <nav class="navbar navbar-expand-sm navbar-light bg-light sticky-top">
      <div class="container-fluid d-flex">
        <a class="navbar-brand" href="<?= BASE_URL ?>index.php">
          <img src="<?= BASE_URL ?>assets/images/brand/logo.png" height="30" alt="Pawshop">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mynavbar">
          <ul class="navbar-nav me-auto">
            <ul class="navbar-nav">
              <li class="nav-item dropdown" id="profileDropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="fa-solid fa-user-tie"></i>
                </a>
                <ul class="dropdown-menu">
                  <?php if (isset($userid) && $userid != ''): ?>
                    <?php if (isset($privilege) && $privilege == 'admin'): ?>
                      <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/index.php">Dasbor Admin</a></li>
                    <?php endif; ?>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>customer/profile.php">Profil</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>customer/transactions.php">Transaksi</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li class="nav-item">
                      <a class="dropdown-item" href="<?= BASE_URL ?>auth/logout.php">Keluar</a>
                    </li>
                  <?php else: ?>
                    <li class="nav-item">
                      <a class="dropdown-item" href="<?= BASE_URL ?>auth/login.php">Masuk</a>
                    </li>
                  <?php endif; ?>
                </ul>
              </li>
            </ul>
            <li class="nav-item dropdown" id="kategoriDropdown">
              <button class="nav-link dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Kategori
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item <?= empty($kategori) ? 'active' : '' ?>" href="<?= BASE_URL ?>index.php?k=">Semua Produk</a></li>
                <li><hr class="dropdown-divider"></li>
                <?php
                $catSql = "SELECT * FROM kategori";
                $catRes = mysqli_query($conn, $catSql);
                if ($catRes && mysqli_num_rows($catRes) > 0) {
                  while ($catRow = mysqli_fetch_assoc($catRes)) {
                    $catActive = (isset($kategori) && $kategori == $catRow['id']) ? 'active' : '';
                    echo '<li><a class="dropdown-item ' . $catActive . '" href="' . BASE_URL . 'index.php?k=' . $catRow['id'] . '">' . htmlspecialchars($catRow['name']) . '</a></li>';
                  }
                }
                ?>
              </ul>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="offcanvas" href="#cart" role="button" aria-controls="cart">
                <i class="fa-solid fa-cart-shopping"></i>
              </a>
            </li>
          </ul>
          <form class="d-flex" action="<?= BASE_URL ?>index.php" method="GET">
            <input class="form-control me-2" type="search" placeholder="Cari Produk" name="s" value="<?= isset($search) ? htmlspecialchars($search) : ''; ?>">
            <button class="btn btn-primary" type="submit">Cari</button>
          </form>
        </div>
      </div>
    </nav>
  </header>
  <!-- End Navbar -->
