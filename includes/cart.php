<!-- Start Cart Offcanvas -->
<div class="offcanvas offcanvas-end cart-drawer shadow-lg border-0" tabindex="-1" id="cart" aria-labelledby="cartLabel">
  <!-- Header -->
  <div class="offcanvas-header border-bottom px-4 py-3 bg-white">
    <div class="d-flex align-items-center gap-2">
      <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
        <i class="fa-solid fa-cart-shopping"></i>
      </div>
      <div>
        <h5 class="offcanvas-title fw-bold mb-0" id="cartLabel">Keranjang Belanja</h5>
        <span class="text-muted small" id="cartCountLabel">
          <?= !empty($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?> jenis barang
        </span>
      </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
  </div>

  <!-- Body -->
  <div class="offcanvas-body p-4 overflow-y-auto">
    <?php
    $cartTotal = 0;
    $cartCount = 0;
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])):
    ?>
      <div class="d-flex flex-column gap-3" id="cartItemsContainer">
        <?php
        foreach ($_SESSION['cart'] as $item):
          $itemSubtotal = $item['quantity'] * $item['price'];
          $cartTotal += $itemSubtotal;
          $cartCount += $item['quantity'];
          $prodData = \App\ProductService::getById($item['id']);
          $maxStok = $prodData ? (int)$prodData['stok'] : 999;
          $imgFile = (!empty($prodData['gambar']) && file_exists(ROOT_PATH . '/assets/images/' . $prodData['gambar']))
                     ? $prodData['gambar'] : 'logo-title.png';
        ?>
          <div class="card cart-item-card border p-3" id="cart-item-<?= $item['id'] ?>">
            <div class="d-flex align-items-center gap-3">
              <!-- Thumbnail -->
              <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($imgFile) ?>" 
                   alt="<?= htmlspecialchars($item['name']) ?>" 
                   class="rounded-3 border object-fit-cover flex-shrink-0" 
                   style="width: 64px; height: 64px;"
                   onerror="this.src='<?= BASE_URL ?>assets/images/brand/logo.png'">
              
              <!-- Details -->
              <div class="flex-grow-1 overflow-hidden">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                  <h6 class="fw-bold text-dark text-truncate mb-0 small" title="<?= htmlspecialchars($item['name']) ?>">
                    <?= htmlspecialchars($item['name']) ?>
                  </h6>
                  <a href="<?= BASE_URL ?>cart/remove.php?product_id=<?= $item['id'] ?>" class="text-danger small opacity-75 hover-opacity-100 flex-shrink-0" title="Hapus produk">
                    <i class="fa-solid fa-trash-can"></i>
                  </a>
                </div>
                <div class="text-muted small mb-2"><?= format_rupiah($item['price']) ?> / pcs</div>

                <!-- Stepper & Subtotal -->
                <div class="d-flex justify-content-between align-items-center mt-2">
                  <div class="cart-stepper d-flex align-items-center gap-1">
                    <form action="<?= BASE_URL ?>cart/update.php" method="POST" class="d-flex align-items-center gap-1 cart-update-form" data-id="<?= $item['id'] ?>">
                      <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                      <button class="btn btn-sm btn-outline-secondary btn-step-dec" type="button" data-id="<?= $item['id'] ?>" title="Kurangi">−</button>
                      <input class="form-control form-control-sm text-center fw-bold bg-white cart-qty-val" type="number" name="quantity" min="1" max="<?= $maxStok ?>" value="<?= $item['quantity'] ?>" readonly>
                      <button class="btn btn-sm btn-outline-secondary btn-step-inc" type="button" data-id="<?= $item['id'] ?>" data-max="<?= $maxStok ?>" title="Tambah">+</button>
                    </form>
                  </div>
                  <div class="fw-bold text-danger small item-subtotal-val" id="subtotal-<?= $item['id'] ?>">
                    <?= format_rupiah($itemSubtotal) ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <!-- Empty State -->
      <div class="text-center py-5 my-4">
        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
          <i class="fa-solid fa-basket-shopping text-muted fs-2"></i>
        </div>
        <h6 class="fw-bold text-dark">Keranjang Belanja Kosong</h6>
        <p class="text-muted small px-3">Yuk jelajahi produk makanan & perlengkapan terbaik untuk kucing kesayangan Anda!</p>
        <a href="<?= BASE_URL ?>index.php" class="btn btn-pawshop btn-sm rounded-pill px-4 mt-2" data-bs-dismiss="offcanvas">
          Mulai Belanja
        </a>
      </div>
    <?php endif; ?>
  </div>

  <!-- Pinned Footer -->
  <?php if (!empty($_SESSION['cart'])): ?>
    <div class="offcanvas-footer p-4 border-top bg-light" id="cartFooterSection">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted small fw-semibold">Total Tagihan:</span>
        <span class="fs-5 fw-bold text-danger" id="cartTotalVal"><?= format_rupiah($cartTotal) ?></span>
      </div>

      <form action="<?= BASE_URL ?>cart/checkout.php" method="POST">
        <div class="mb-3">
          <label class="form-label small fw-semibold text-muted">Metode Pembayaran:</label>
          <select class="form-select" name="payment_method" required>
            <option value="" disabled selected>Pilih Pembayaran</option>
            <option value="Transfer Bank">Transfer Bank (BCA / Mandiri / BRI)</option>
            <option value="COD / Bayar di Tempat">COD / Bayar di Tempat</option>
            <option value="QRIS / E-Wallet">QRIS / GoPay / OVO / Dana</option>
          </select>
        </div>
        <button class="btn btn-pawshop w-100 py-2 fw-bold shadow-sm" type="submit" name="checkout">
          <i class="fa-solid fa-credit-card me-1"></i> Checkout Sekarang
        </button>
      </form>
      <div class="text-center mt-2">
        <span class="text-muted" style="font-size: 0.72rem;">
          <i class="fa-solid fa-shield-halved text-success me-1"></i> Pembayaran Aman & Terverifikasi
        </span>
      </div>
    </div>
  <?php endif; ?>
</div>
<!-- End Cart Offcanvas -->
