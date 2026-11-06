<?php
use App\ProductService;
?>
<!-- Header & Category Filter Chips -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
  <div>
    <h3 class="fw-bold mb-1">
      <?php
      if ($kategoriId) {
          $cat = ProductService::getCategoryById($kategoriId);
          echo htmlspecialchars($cat['name'] ?? 'Katalog Produk');
      } elseif (!empty($search)) {
          echo "Hasil Pencarian: \"" . htmlspecialchars($search) . "\"";
      } else {
          echo "Katalog Produk Pilihan";
      }
      ?>
    </h3>
    <p class="text-muted small mb-0">Menampilkan <?= count($products) ?> dari <?= $totalItems ?> produk tersedia</p>
  </div>

  <?php if ($kategoriId || !empty($search)): ?>
    <a href="<?= BASE_URL ?>index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 btn-reset-filter">
      <i class="fa-solid fa-rotate-left me-1"></i> Reset Filter
    </a>
  <?php endif; ?>
</div>

<!-- Category Pills (Instant Filter) -->
<div class="d-flex align-items-center gap-2 overflow-x-auto pb-2 mb-4">
  <a href="<?= BASE_URL ?>index.php" class="category-chip <?= empty($kategoriId) ? 'active' : '' ?>">
    <i class="fa-solid fa-border-all"></i> Semua Produk
  </a>
  <?php foreach ($allCategories as $c): ?>
    <a href="<?= BASE_URL ?>index.php?k=<?= $c['id'] ?>" class="category-chip <?= ($kategoriId == $c['id']) ? 'active' : '' ?>">
      <i class="fa-solid fa-paw"></i> <?= htmlspecialchars($c['name']) ?>
    </a>
  <?php endforeach; ?>
</div>

<?= render_flash() ?>

<!-- Product Grid -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4" id="productGrid">
  <?php if (!empty($products)): ?>
    <?php foreach ($products as $row): ?>
      <div class="col">
        <div class="card product-card h-100 border bg-white shadow-none">
          <!-- Thumbnail Wrapper with Badge -->
          <div class="product-thumb-wrapper position-relative">
            <a href="<?= BASE_URL ?>customer/product-detail.php?id=<?= $row['id'] ?>">
              <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($row['gambar'] ?: 'x.jpg') ?>" 
                   class="card-img-top object-fit-cover w-100" 
                   style="height: 220px;" 
                   alt="<?= htmlspecialchars($row['nama_produk']) ?>" 
                   loading="lazy" 
                   width="300" 
                   height="220" 
                   onerror="this.src='<?= BASE_URL ?>assets/images/brand/logo.png'">
            </a>

            <!-- Stock & Category Badges -->
            <div class="position-absolute top-0 start-0 m-2">
              <span class="badge bg-white text-dark shadow-sm border" style="font-size: 0.7rem;">
                <?= htmlspecialchars($row['kategori_name'] ?? 'Umum') ?>
              </span>
            </div>
            <div class="position-absolute top-0 end-0 m-2">
              <?php if ($row['stok'] <= 0): ?>
                <span class="badge bg-secondary shadow-sm" style="font-size: 0.7rem;">Habis</span>
              <?php elseif ($row['stok'] <= 3): ?>
                <span class="badge bg-danger shadow-sm" style="font-size: 0.7rem;">Sisa <?= $row['stok'] ?>!</span>
              <?php else: ?>
                <span class="badge bg-success shadow-sm" style="font-size: 0.7rem;">Ready</span>
              <?php endif; ?>
            </div>
          </div>

          <!-- Card Body -->
          <div class="card-body p-3 d-flex flex-column justify-content-between">
            <div>
              <h6 class="card-title fw-bold text-truncate mb-1" title="<?= htmlspecialchars($row['nama_produk']) ?>">
                <a href="<?= BASE_URL ?>customer/product-detail.php?id=<?= $row['id'] ?>" class="text-dark text-decoration-none">
                  <?= htmlspecialchars($row['nama_produk']) ?>
                </a>
              </h6>
              <p class="text-danger fw-bold fs-5 mb-1"><?= format_rupiah($row['harga']) ?></p>
              <p class="text-muted small mb-0" style="font-size: 0.78rem;">
                <i class="fa-solid fa-cubes me-1"></i> Tersedia: <strong><?= $row['stok'] ?></strong> buah
              </p>
            </div>

            <!-- Action Buttons: Detail & Quick Add to Cart -->
            <div class="d-flex gap-2 mt-3 pt-2 border-top">
              <a href="<?= BASE_URL ?>customer/product-detail.php?id=<?= $row['id'] ?>" class="btn btn-outline-secondary btn-sm flex-grow-1" title="Lihat Rincian">
                Detail
              </a>
              <form action="<?= BASE_URL ?>cart/add.php" method="POST" class="d-inline">
                <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-pawshop btn-sm fw-semibold" <?= ($row['stok'] <= 0) ? 'disabled' : '' ?> title="Masukkan ke Keranjang">
                  <i class="fa-solid fa-cart-plus me-1"></i> + Keranjang
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="col-12 text-center py-5">
      <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
        <i class="fa-solid fa-magnifying-glass text-muted fs-2"></i>
      </div>
      <h5 class="fw-bold text-dark">Tidak Ada Produk Ditemukan</h5>
      <p class="text-muted small">Coba cari dengan kata kunci lain atau pilih kategori yang berbeda.</p>
      <a href="<?= BASE_URL ?>index.php" class="btn btn-pawshop btn-sm rounded-pill px-4 mt-2 btn-reset-filter">
        Lihat Semua Produk
      </a>
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
        unset($query['ajax_catalog']);
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
