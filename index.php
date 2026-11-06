<?php
require_once __DIR__ . '/config/database.php';

use App\ProductService;

$pageTitle = "Pawshop - Kebutuhan Kucing Terlengkap";
$search = isset($_GET['s']) ? trim($_GET['s']) : null;
$kategoriId = isset($_GET['k']) && is_numeric($_GET['k']) ? (int)$_GET['k'] : null;
$itemPerPage = isset($_GET['itemsPerPage']) ? max(1, (int)$_GET['itemsPerPage']) : 4;
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

$totalItems = ProductService::count($kategoriId, $search);
$totalPages = max(1, (int)ceil($totalItems / $itemPerPage));
$offset = ($currentPage - 1) * $itemPerPage;

$products = ProductService::getAll($kategoriId, $search, $itemPerPage, $offset);

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
        if ($kategoriId) {
            $cat = ProductService::getCategoryById($kategoriId);
            echo htmlspecialchars($cat['name'] ?? 'Katalog Produk');
        } elseif (!empty($search)) {
            echo "Hasil Pencarian: \"" . htmlspecialchars($search) . "\"";
        } else {
            echo "Semua Produk";
        }
        ?>
      </h3>
      <?php if ($kategoriId || !empty($search)): ?>
        <a href="<?= BASE_URL ?>index.php" class="btn btn-outline-secondary btn-sm">Lihat Semua</a>
      <?php endif; ?>
    </div>

    <?= render_flash() ?>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
      <?php if (!empty($products)): ?>
        <?php foreach ($products as $row): ?>
          <div class="col">
            <div class="card h-100 shadow-sm border-0">
              <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($row['gambar'] ?: 'x.jpg') ?>" class="card-img-top object-fit-cover" style="height: 220px;" alt="<?= htmlspecialchars($row['nama_produk']) ?>" onerror="this.src='<?= BASE_URL ?>assets/images/brand/logo.png'">
              <div class="card-body d-flex flex-column justify-content-between">
                <div>
                  <span class="badge bg-light text-muted border mb-2"><?= htmlspecialchars($row['kategori_name'] ?? 'Umum') ?></span>
                  <h5 class="card-title fw-bold text-truncate" title="<?= htmlspecialchars($row['nama_produk']) ?>"><?= htmlspecialchars($row['nama_produk']) ?></h5>
                  <p class="text-danger fw-semibold fs-5 mb-1"><?= format_rupiah($row['harga']) ?></p>
                  <p class="text-muted small">Sisa stok: <strong><?= $row['stok'] ?></strong></p>
                </div>
                <div class="mt-3">
                  <a href="<?= BASE_URL ?>customer/product-detail.php?id=<?= $row['id'] ?>" class="btn btn-outline-warning text-dark w-100 fw-semibold">
                    <i class="fa-solid fa-eye me-1"></i> Lihat Detail
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12 text-center py-5">
          <p class="text-muted fs-5">Tidak ada produk ditemukan.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
      <nav class="mt-5" aria-label="Navigasi Halaman">
        <ul class="pagination justify-content-center">
          <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <?php
            $query = $_GET;
            $query['page'] = $p;
            $pageUrl = '?' . http_build_query($query);
            ?>
            <li class="page-item <?= ($currentPage == $p) ? 'active' : '' ?>">
              <a class="page-link" href="<?= $pageUrl ?>"><?= $p ?></a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
    <?php endif; ?>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
