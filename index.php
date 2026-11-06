<?php
require_once __DIR__ . '/config/database.php';

$pageTitle = "Pawshop - Kebutuhan Kucing Terlengkap";
$search = isset($_GET['s']) ? mysqli_real_escape_string($conn, trim($_GET['s'])) : '';
$kategori = isset($_GET['k']) ? mysqli_real_escape_string($conn, trim($_GET['k'])) : '';
$item_per_page = isset($_GET['itemsPerPage']) ? max(1, intval($_GET['itemsPerPage'])) : 4;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Query building
$whereClauses = array();
if (!empty($search)) {
  $whereClauses[] = "nama_produk LIKE '%$search%'";
}
if (!empty($kategori)) {
  $whereClauses[] = "category_id = '$kategori'";
}

$whereSql = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

$countRes = mysqli_query($conn, "SELECT COUNT(*) AS total FROM produk $whereSql");
$total_item = ($countRes && $r = mysqli_fetch_assoc($countRes)) ? (int)$r['total'] : 0;
$total_page = max(1, ceil($total_item / $item_per_page));
$offset = ($page - 1) * $item_per_page;

$sql = "SELECT p.*, k.name AS kategori_nama FROM produk p LEFT JOIN kategori k ON p.category_id=k.id $whereSql LIMIT $item_per_page OFFSET $offset";
$result = mysqli_query($conn, $sql);

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main>
  <!-- Carousel -->
  <div id="carouselPawshop" class="carousel slide container mt-3" data-bs-ride="carousel" data-bs-touch="true">
    <div class="carousel-inner rounded shadow-sm">
      <div class="carousel-item active">
        <img src="<?= BASE_URL ?>assets/images/carousel1.jpg" class="d-block w-100 img-fluid" alt="Promo 1">
        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded">
          <h3>Selamat datang di Pawshop</h3>
          <p>Menjual beragam kebutuhan kucing terlengkap dan terpercaya</p>
        </div>
      </div>
      <div class="carousel-item">
        <img src="<?= BASE_URL ?>assets/images/carousel2.jpg" class="d-block w-100 img-fluid" alt="Promo 2">
      </div>
      <div class="carousel-item">
        <img src="<?= BASE_URL ?>assets/images/carousel3.jpg" class="d-block w-100 img-fluid" alt="Promo 3">
      </div>
      <div class="carousel-item">
        <img src="<?= BASE_URL ?>assets/images/carousel4.jpg" class="d-block w-100 img-fluid" alt="Promo 4">
      </div>
      <div class="carousel-item">
        <img src="<?= BASE_URL ?>assets/images/carousel5.jpg" class="d-block w-100 img-fluid" alt="Promo 5">
      </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselPawshop" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselPawshop" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>

  <!-- Product Catalog -->
  <div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="fw-bold">
        <?php
        if (!empty($search)) {
          echo 'Hasil pencarian: "' . htmlspecialchars($search) . '"';
        } else if (!empty($kategori)) {
          $cnameRes = mysqli_query($conn, "SELECT name FROM kategori WHERE id='$kategori'");
          $cname = ($cnameRes && $cn = mysqli_fetch_assoc($cnameRes)) ? $cn['name'] : 'Kategori';
          echo htmlspecialchars($cname);
        } else {
          echo 'Katalog Produk';
        }
        ?>
      </h3>
      <span class="text-muted"><?= $total_item ?> Produk</span>
    </div>

    <div class="row g-4">
      <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-6 col-sm-6">
            <div class="card h-100 shadow-sm">
              <a href="<?= BASE_URL ?>customer/product-detail.php?id=<?= $row['id'] ?>">
                <img class="card-img-top p-2" src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['nama_produk']) ?>" style="height: 220px; object-fit: contain;">
              </a>
              <div class="card-body d-flex flex-col justify-content-between flex-column">
                <div>
                  <h5 class="card-title text-truncate" title="<?= htmlspecialchars($row['nama_produk']) ?>">
                    <a href="<?= BASE_URL ?>customer/product-detail.php?id=<?= $row['id'] ?>" class="text-decoration-none text-dark">
                      <?= htmlspecialchars($row['nama_produk']) ?>
                    </a>
                  </h5>
                  <p class="text-end fs-5 fw-bold text-primary mb-2">
                    Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
                  </p>
                </div>
                <form action="<?= BASE_URL ?>cart/add.php" method="POST" autocomplete="off">
                  <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                  <input type="hidden" name="nama_produk" value="<?= htmlspecialchars($row['nama_produk']) ?>">
                  <input type="hidden" name="harga" value="<?= $row['harga'] ?>">
                  <div class="mb-2">
                    <div class="d-flex justify-content-between text-muted small mb-1">
                      <span>Stok: <?= $row['stok'] ?></span>
                    </div>
                    <input type="number" class="form-control form-control-sm" id="quantity" min="1" value="1" max="<?= $row['stok'] ?>" name="quantity" <?= ($row['stok'] <= 0) ? 'disabled' : '' ?>>
                  </div>
                  <button type="submit" class="btn btn-primary w-100 btn-sm" <?= ($row['stok'] <= 0) ? 'disabled' : '' ?>>
                    <?= ($row['stok'] > 0) ? '<i class="fa-solid fa-cart-plus me-1"></i> Masukkan Keranjang' : 'Stok Habis' ?>
                  </button>
                </form>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col-12 py-5 text-center">
          <p class="text-muted fs-5">Tidak ada produk yang ditemukan.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Pagination -->
  <?php if ($total_page > 1): ?>
    <div class="container mt-4 d-flex justify-content-center">
      <nav aria-label="Page navigation">
        <ul class="pagination">
          <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="?s=<?= urlencode($search) ?>&k=<?= urlencode($kategori) ?>&page=<?= $page - 1 ?>&itemsPerPage=<?= $item_per_page ?>">Prev</a>
          </li>
          <?php for ($i = 1; $i <= $total_page; $i++): ?>
            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
              <a class="page-link" href="?s=<?= urlencode($search) ?>&k=<?= urlencode($kategori) ?>&page=<?= $i ?>&itemsPerPage=<?= $item_per_page ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>
          <li class="page-item <?= ($page >= $total_page) ? 'disabled' : '' ?>">
            <a class="page-link" href="?s=<?= urlencode($search) ?>&k=<?= urlencode($kategori) ?>&page=<?= $page + 1 ?>&itemsPerPage=<?= $item_per_page ?>">Next</a>
          </li>
        </ul>
      </nav>
    </div>
  <?php endif; ?>
</main>

<?php
include __DIR__ . '/includes/cart.php';
include __DIR__ . '/includes/footer.php';
?>
