<?php
require_once __DIR__ . '/../config/database.php';

use App\ProductService;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = $id > 0 ? ProductService::getById($id) : null;

if (!$product) {
    flash('danger', 'Produk yang Anda cari tidak ditemukan.');
    redirect('index.php');
}

$pageTitle = "Pawshop | " . $product['nama_produk'];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="container py-4">
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb small">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>index.php" class="text-decoration-none text-muted"><i class="fa-solid fa-house me-1"></i> Beranda</a></li>
      <?php if (!empty($product['kategori'])): ?>
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>index.php?k=<?= $product['category_id'] ?>" class="text-decoration-none text-muted"><?= htmlspecialchars($product['kategori']) ?></a></li>
      <?php endif; ?>
      <li class="breadcrumb-item active fw-semibold text-truncate" style="max-width: 320px;" aria-current="page"><?= htmlspecialchars($product['nama_produk']) ?></li>
    </ol>
  </nav>

  <?= render_flash() ?>

  <div class="row g-4 g-lg-5">
    <!-- Product Showcase Image -->
    <div class="col-md-6">
      <div class="card border rounded-4 shadow-sm p-4 text-center bg-white h-100 d-flex align-items-center justify-content-center">
        <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($product['gambar'] ?: 'x.jpg') ?>" 
             class="img-fluid rounded-3 object-fit-contain" 
             alt="<?= htmlspecialchars($product['nama_produk']) ?>" 
             style="max-height: 440px; width: 100%;" 
             width="500" 
             height="440" 
             fetchpriority="high" 
             onerror="this.src='<?= BASE_URL ?>assets/images/brand/logo.png'">
      </div>
    </div>

    <!-- Product Info & Purchase Form -->
    <div class="col-md-6 d-flex flex-column justify-content-between">
      <div>
        <div class="d-flex align-items-center gap-2 mb-2">
          <span class="badge bg-light text-dark border px-3 py-1 rounded-pill">
            <i class="fa-solid fa-tag me-1 text-warning"></i> <?= htmlspecialchars($product['kategori'] ?? 'Umum') ?>
          </span>
          <?php if ($product['stok'] <= 0): ?>
            <span class="badge bg-secondary px-3 py-1 rounded-pill">Stok Habis</span>
          <?php elseif ($product['stok'] <= 3): ?>
            <span class="badge bg-danger px-3 py-1 rounded-pill">Sisa <?= $product['stok'] ?> Unit!</span>
          <?php else: ?>
            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill">
              <i class="fa-solid fa-circle-check me-1"></i> Ready Stock
            </span>
          <?php endif; ?>
        </div>

        <h1 class="h3 fw-bold text-dark mb-3"><?= htmlspecialchars($product['nama_produk']) ?></h1>

        <!-- Price -->
        <div class="mb-4 p-3 bg-light rounded-3">
          <span class="text-muted small d-block mb-1">Harga Spesial:</span>
          <span class="fs-2 fw-bold text-danger"><?= format_rupiah($product['harga']) ?></span>
        </div>

        <!-- Description -->
        <div class="mb-4">
          <h6 class="fw-bold text-dark mb-2">Deskripsi Produk:</h6>
          <p class="text-muted small leading-relaxed mb-0">
            <?= nl2br(htmlspecialchars($product['detail'] ?? 'Belum ada deskripsi detail untuk produk ini.')) ?>
          </p>
        </div>

        <!-- Purchase Action Form -->
        <form action="<?= BASE_URL ?>cart/add.php" method="POST" autocomplete="off" class="mb-4">
          <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

          <div class="mb-3">
            <label class="form-label small fw-semibold text-muted">Jumlah Pesanan:</label>
            <div class="d-flex align-items-center gap-3">
              <div class="cart-stepper d-flex align-items-center gap-1">
                <button class="btn btn-outline-secondary" type="button" id="btnDetailDec" style="width: 36px; height: 36px;">−</button>
                <input type="number" class="form-control text-center fw-bold bg-white" id="quantityInput" name="quantity" min="1" max="<?= $product['stok'] ?>" value="1" style="width: 60px; height: 36px;" readonly>
                <button class="btn btn-outline-secondary" type="button" id="btnDetailInc" style="width: 36px; height: 36px;">+</button>
              </div>
              <span class="text-muted small">
                Tersedia: <strong><?= $product['stok'] ?></strong> buah
              </span>
            </div>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-pawshop btn-lg fw-bold flex-grow-1 shadow-sm py-2" <?= ($product['stok'] <= 0) ? 'disabled' : '' ?>>
              <i class="fa-solid fa-cart-plus me-2"></i>
              <?= ($product['stok'] > 0) ? 'Masukkan Keranjang' : 'Stok Sedang Habis' ?>
            </button>
          </div>
        </form>
      </div>

      <!-- Trust Perks Card -->
      <div class="card border rounded-3 p-3 bg-light">
        <div class="row g-2 text-muted" style="font-size: 0.8rem;">
          <div class="col-6 d-flex align-items-center gap-2">
            <i class="fa-solid fa-shield-halved text-success"></i>
            <span>100% Produk Original</span>
          </div>
          <div class="col-6 d-flex align-items-center gap-2">
            <i class="fa-solid fa-box-open text-primary"></i>
            <span>Packing Bubble Wrap Aman</span>
          </div>
          <div class="col-6 d-flex align-items-center gap-2">
            <i class="fa-solid fa-truck-fast text-warning"></i>
            <span>Kirim Hari yang Sama</span>
          </div>
          <div class="col-6 d-flex align-items-center gap-2">
            <i class="fa-solid fa-hand-holding-dollar text-info"></i>
            <span>Bisa Bayar di Tempat (COD)</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var qtyInput = document.getElementById('quantityInput');
    var btnInc = document.getElementById('btnDetailInc');
    var btnDec = document.getElementById('btnDetailDec');
    var maxStock = <?= (int)$product['stok'] ?>;

    if (btnInc && qtyInput) {
      btnInc.addEventListener('click', function() {
        var cur = parseInt(qtyInput.value, 10) || 1;
        if (cur < maxStock) {
          qtyInput.value = cur + 1;
        }
      });
    }

    if (btnDec && qtyInput) {
      btnDec.addEventListener('click', function() {
        var cur = parseInt(qtyInput.value, 10) || 1;
        if (cur > 1) {
          qtyInput.value = cur - 1;
        }
      });
    }
  });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
