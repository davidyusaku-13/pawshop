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

<main class="container py-5">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>index.php">Katalog</a></li>
      <?php if (!empty($product['kategori'])): ?>
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>index.php?k=<?= $product['category_id'] ?>"><?= htmlspecialchars($product['kategori']) ?></a></li>
      <?php endif; ?>
      <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['nama_produk']) ?></li>
    </ol>
  </nav>

  <?= render_flash() ?>

  <div class="row g-5 align-items-center">
    <div class="col-md-6 text-center">
      <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($product['gambar'] ?: 'x.jpg') ?>" class="img-fluid rounded shadow-sm" alt="<?= htmlspecialchars($product['nama_produk']) ?>" style="max-height: 450px; object-fit: contain;" width="500" height="450" fetchpriority="high" onerror="this.src='<?= BASE_URL ?>assets/images/brand/logo.png'">
    </div>
    <div class="col-md-6">
      <span class="badge bg-secondary mb-2"><?= htmlspecialchars($product['kategori'] ?? 'Umum') ?></span>
      <h1 class="display-6 fw-bold mb-3"><?= htmlspecialchars($product['nama_produk']) ?></h1>
      <p class="lead text-muted"><?= nl2br(htmlspecialchars($product['detail'] ?? '')) ?></p>
      
      <p class="fs-3 fw-bold text-danger mb-3">
        <?= format_rupiah($product['harga']) ?>
      </p>

      <form action="<?= BASE_URL ?>cart/add.php" method="POST" autocomplete="off" class="mt-4">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

        <div class="mb-3 d-flex align-items-center gap-3">
          <label for="quantity" class="form-label fw-semibold mb-0">Jumlah:</label>
          <input type="number" class="form-control" style="width: 100px;" id="quantity" min="1" value="1" max="<?= $product['stok'] ?>" name="quantity" <?= ($product['stok'] <= 0) ? 'disabled' : '' ?>>
          <span class="text-muted">tersisa <strong><?= $product['stok'] ?></strong> buah</span>
        </div>

        <button type="submit" class="btn btn-warning text-dark btn-lg fw-semibold" <?= ($product['stok'] <= 0) ? 'disabled' : '' ?>>
          <i class="fa-solid fa-cart-plus me-2"></i>
          <?= ($product['stok'] > 0) ? 'Masukkan Keranjang' : 'Stok Habis' ?>
        </button>
      </form>
    </div>
  </div>
</main>

<?php
include __DIR__ . '/../includes/cart.php';
include __DIR__ . '/../includes/footer.php';
?>
