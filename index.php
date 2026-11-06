<?php
require_once __DIR__ . '/config/database.php';

use App\ProductService;

$pageTitle = "Pawshop - Kebutuhan Kucing Terlengkap";
$search = isset($_GET['s']) ? trim($_GET['s']) : null;
$kategoriId = isset($_GET['k']) && is_numeric($_GET['k']) ? (int)$_GET['k'] : null;
$itemPerPage = isset($_GET['itemsPerPage']) ? max(1, (int)$_GET['itemsPerPage']) : 12;
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

$totalItems = ProductService::count($kategoriId, $search);
$totalPages = max(1, (int)ceil($totalItems / $itemPerPage));
$offset = ($currentPage - 1) * $itemPerPage;

$products = ProductService::getAll($kategoriId, $search, $itemPerPage, $offset);
$allCategories = ProductService::getAllCategories();

// Respond immediately with catalog markup if requested via AJAX
if (isset($_GET['ajax_catalog'])) {
    include __DIR__ . '/includes/catalog-list.php';
    exit();
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main>
  <!-- Hero Carousel -->
  <div id="carouselPawshop" class="carousel slide container mt-3" data-bs-ride="carousel" data-bs-touch="true">
    <div class="carousel-inner rounded-4 shadow-sm overflow-hidden">
      <div class="carousel-item active">
        <img src="<?= BASE_URL ?>assets/images/carousel1.jpg" class="d-block w-100 img-fluid" alt="Promo 1" width="1600" height="800" fetchpriority="high">
        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded-3 p-3">
          <h3 class="fw-bold mb-1">Nutrisi Terbaik untuk Anabul Kesayangan</h3>
          <p class="mb-0">Pilihan makanan kering, basah, dan vitamin kucing berkualitas tinggi</p>
        </div>
      </div>
      <div class="carousel-item">
        <img src="<?= BASE_URL ?>assets/images/carousel2.jpg" class="d-block w-100 img-fluid" alt="Promo 2" width="1600" height="800" loading="lazy">
      </div>
      <div class="carousel-item">
        <img src="<?= BASE_URL ?>assets/images/carousel3.jpg" class="d-block w-100 img-fluid" alt="Promo 3" width="1600" height="800" loading="lazy">
      </div>
      <div class="carousel-item">
        <img src="<?= BASE_URL ?>assets/images/carousel4.jpg" class="d-block w-100 img-fluid" alt="Promo 4" width="1600" height="800" loading="lazy">
      </div>
      <div class="carousel-item">
        <img src="<?= BASE_URL ?>assets/images/carousel5.jpg" class="d-block w-100 img-fluid" alt="Promo 5" width="1600" height="800" loading="lazy">
      </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselPawshop" data-bs-slide="prev" aria-label="Sebelumnya">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselPawshop" data-bs-slide="next" aria-label="Selanjutnya">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </button>
  </div>

  <!-- Trust Value Proposition Bar -->
  <div class="container mt-4">
    <div class="row g-3">
      <div class="col-6 col-lg-3">
        <div class="trust-badge-card">
          <div class="trust-badge-icon bg-warning bg-opacity-10 text-warning">
            <i class="fa-solid fa-truck-fast"></i>
          </div>
          <div>
            <div class="fw-bold small text-dark">Pengiriman Cepat</div>
            <div class="text-muted" style="font-size: 0.75rem;">Diproses di hari yang sama</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="trust-badge-card">
          <div class="trust-badge-icon bg-success bg-opacity-10 text-success">
            <i class="fa-solid fa-shield-check"></i>
          </div>
          <div>
            <div class="fw-bold small text-dark">100% Produk Asli</div>
            <div class="text-muted" style="font-size: 0.75rem;">Garansi tanggal kedaluwarsa aman</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="trust-badge-card">
          <div class="trust-badge-icon bg-info bg-opacity-10 text-info">
            <i class="fa-solid fa-comments"></i>
          </div>
          <div>
            <div class="fw-bold small text-dark">Konsultasi Ramah</div>
            <div class="text-muted" style="font-size: 0.75rem;">Bantuan memilih pakan tepat</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="trust-badge-card">
          <div class="trust-badge-icon bg-primary bg-opacity-10 text-primary">
            <i class="fa-solid fa-wallet"></i>
          </div>
          <div>
            <div class="fw-bold small text-dark">Bayar Fleksibel</div>
            <div class="text-muted" style="font-size: 0.75rem;">COD, Transfer Bank & QRIS</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Product Catalog Section (Dynamic AJAX container) -->
  <div class="container my-5 pb-5" id="catalogSection">
    <?php include __DIR__ . '/includes/catalog-list.php'; ?>
  </div>
</main>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var catalogSection = document.getElementById('catalogSection');
    if (!catalogSection) return;

    function loadCatalog(url, pushState) {
      if (typeof pushState === 'undefined') pushState = true;

      catalogSection.style.transition = 'opacity 0.15s ease';
      catalogSection.style.opacity = '0.5';

      var ajaxUrl = url + (url.indexOf('?') !== -1 ? '&' : '?') + 'ajax_catalog=1';

      fetch(ajaxUrl, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(function(res) {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.text();
      })
      .then(function(html) {
        catalogSection.innerHTML = html;
        catalogSection.style.opacity = '1';
        if (pushState) {
          history.pushState(null, '', url);
        }
        bindEvents();
      })
      .catch(function(err) {
        console.error('Catalog fetch error:', err);
        window.location.href = url;
      });
    }

    function bindEvents() {
      // Category chips
      catalogSection.querySelectorAll('.category-chip').forEach(function(chip) {
        chip.addEventListener('click', function(e) {
          e.preventDefault();
          loadCatalog(this.getAttribute('href'), true);
        });
      });

      // Reset filter button
      var resetBtn = catalogSection.querySelector('.btn-reset-filter');
      if (resetBtn) {
        resetBtn.addEventListener('click', function(e) {
          e.preventDefault();
          loadCatalog(this.getAttribute('href'), true);
        });
      }

      // Pagination links
      catalogSection.querySelectorAll('.pagination .page-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          loadCatalog(this.getAttribute('href'), true);
        });
      });
    }

    bindEvents();

    // Browser back/forward navigation support
    window.addEventListener('popstate', function() {
      loadCatalog(window.location.href, false);
    });

    // Also support top navbar category links when on homepage
    document.querySelectorAll('#mynavbar .dropdown-menu a').forEach(function(navLink) {
      var href = navLink.getAttribute('href');
      if (href && (href.indexOf('?k=') !== -1 || href === '/' || href.endsWith('index.php'))) {
        navLink.addEventListener('click', function(e) {
          if (window.location.pathname.endsWith('index.php') || window.location.pathname === '/' || window.location.pathname === '') {
            e.preventDefault();
            loadCatalog(href, true);
            catalogSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        });
      }
    });
  });
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
