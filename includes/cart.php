  <!-- Start Cart Offcanvas -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="cart" aria-labelledby="cartLabel">
    <div class="offcanvas-header border-bottom">
      <h5 class="offcanvas-title fw-bold" id="cartLabel"><i class="fa-solid fa-cart-shopping me-2"></i> Keranjang Belanja</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead class="table-light">
            <tr>
              <th style="width: 40px;" class="text-center">Hapus</th>
              <th>Produk</th>
              <th style="width: 90px;">Qty</th>
              <th class="text-end">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $cartTotal = 0;
            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
              foreach ($_SESSION['cart'] as $item) {
                $itemSubtotal = $item['quantity'] * $item['price'];
                $cartTotal += $itemSubtotal;
                $prodData = \App\ProductService::getById($item['id']);
                $maxStok = $prodData ? $prodData['stok'] : 999;
            ?>
                <tr>
                  <td class="text-center">
                    <a href="<?= BASE_URL ?>cart/remove.php?product_id=<?= $item['id'] ?>" class="text-danger" title="Hapus"><i class="fa-solid fa-trash"></i></a>
                  </td>
                  <td class="fw-semibold small"><?= htmlspecialchars($item['name']) ?></td>
                  <td>
                    <form action="<?= BASE_URL ?>cart/update.php" method="POST" class="d-flex gap-1">
                      <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                      <input class="form-control form-control-sm px-1 text-center" type="number" name="quantity" max="<?= $maxStok ?>" value="<?= $item['quantity'] ?>" min="1">
                      <button class="btn btn-outline-primary btn-sm p-1" type="submit" title="Perbarui"><i class="fa-solid fa-arrows-rotate"></i></button>
                    </form>
                  </td>
                  <td class="text-end small fw-bold"><?= format_rupiah($itemSubtotal) ?></td>
                </tr>
            <?php
              }
            } else {
              echo '<tr><td class="text-center py-4 text-muted" colspan="4">Keranjang belanja kosong!</td></tr>';
            }
            ?>
          </tbody>
          <tfoot class="table-light">
            <tr>
              <th class="text-end" colspan="3">Total Tagihan</th>
              <th class="text-end text-danger"><?= format_rupiah($cartTotal) ?></th>
            </tr>
          </tfoot>
        </table>

        <?php if (!empty($_SESSION['cart'])): ?>
          <form action="<?= BASE_URL ?>cart/checkout.php" method="POST" class="mt-3">
            <div class="mb-3">
              <label class="form-label small fw-semibold">Pilih Metode Pembayaran:</label>
              <select class="form-select" name="payment_method" required>
                <option value="" disabled selected>Pilih Metode Pembayaran</option>
                <option value="Transfer Bank">Transfer Bank</option>
                <option value="COD / Bayar di Tempat">COD / Bayar di Tempat</option>
                <option value="QRIS / E-Wallet">QRIS / E-Wallet</option>
              </select>
            </div>
            <button class="btn btn-warning text-dark w-100 fw-bold py-2 shadow-sm" type="submit" name="checkout">
              <i class="fa-solid fa-credit-card me-1"></i> Checkout Sekarang
            </button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <!-- End Cart Offcanvas -->
