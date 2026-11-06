<?php
require_once __DIR__ . '/../config/database.php';

$detailID = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sql = "SELECT p.*, k.name AS kategori_nama FROM produk p LEFT JOIN kategori k ON p.category_id=k.id WHERE p.id='$detailID'";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
  header('Location: ' . BASE_URL . 'index.php');
  exit();
}

$product = mysqli_fetch_assoc($result);
$pageTitle = "Pawshop | " . $product['nama_produk'];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="container py-5">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>index.php">Katalog</a></li>
      <?php if (!empty($product['kategori_nama'])): ?>
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>index.php?k=<?= $product['category_id'] ?>"><?= htmlspecialchars($product['kategori_nama']) ?></a></li>
      <?php endif; ?>
      <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['nama_produk']) ?></li>
    </ol>
  </nav>

  <div class="row g-5 align-items-center">
    <div class="col-md-6 text-center">
      <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($product['gambar']) ?>" class="img-fluid rounded shadow-sm" alt="<?= htmlspecialchars($product['nama_produk']) ?>" style="max-height: 450px; object-fit: contain;">
    </div>
    <div class="col-md-6">
      <h1 class="display-6 fw-bold mb-3"><?= htmlspecialchars($product['nama_produk']) ?></h1>
      <p class="lead text-muted"><?= nl2br(htmlspecialchars($product['detail'] ?? '')) ?></p>
      
      <p class="fs-4 fw-bold text-primary mb-3">
        Rp <?= number_format($product['harga'], 0, ',', '.'); ?>
      </p>

      <form action="<?= BASE_URL ?>cart/add.php" method="POST" autocomplete="off" class="mt-4">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
        <input type="hidden" name="nama_produk" value="<?= htmlspecialchars($product['nama_produk']) ?>">
        <input type="hidden" name="harga" value="<?= $product['harga'] ?>">

        <div class="mb-3 d-flex align-items-center gap-3">
          <label for="quantity" class="form-label fw-semibold mb-0">Jumlah:</label>
          <input type="number" class="form-control" style="width: 100px;" id="quantity" min="1" value="1" max="<?= $product['stok'] ?>" name="quantity" <?= ($product['stok'] <= 0) ? 'disabled' : '' ?>>
          <span class="text-muted">tersisa <strong><?= $product['stok'] ?></strong> buah</span>
        </div>

        <button type="submit" class="btn btn-primary btn-lg" <?= ($product['stok'] <= 0) ? 'disabled' : '' ?>>
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
