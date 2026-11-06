  <!-- Start Cart -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="cart" aria-labelledby="cartLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="cartLabel">Keranjang</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <div>
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Aksi</th>
                <th>Nama</th>
                <th>Qty</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $cartTotal = 0;
              if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                  $itemSubtotal = $item['quantity'] * $item['price'];
                  $cartTotal += $itemSubtotal;
              ?>
                  <tr>
                    <td class="text-center">
                      <a href="<?= BASE_URL ?>cart/remove.php?product_id=<?= $item['id'] ?>"><i class="fa-solid fa-trash text-danger"></i></a>
                    </td>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td style="width: 7em;">
                      <form action="<?= BASE_URL ?>cart/update.php" method="POST">
                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                        <?php
                        $prodId = $item['id'];
                        $stockRes = mysqli_query($conn, "SELECT stok FROM produk WHERE id='$prodId'");
                        $maxStok = 999;
                        if ($stockRes && $stockRow = mysqli_fetch_assoc($stockRes)) {
                          $maxStok = $stockRow['stok'];
                        }
                        ?>
                        <input class="d-block form-control" type="number" name="quantity" max="<?= $maxStok ?>" value="<?= $item['quantity'] ?>" min="1">
                        <div class="d-block mt-1 text-end">
                          <button class="btn btn-primary btn-sm" type="submit"><i class="fa-solid fa-refresh"></i></button>
                        </div>
                      </form>
                    </td>
                    <td>Rp <?= number_format($itemSubtotal) ?></td>
                  </tr>
              <?php
                }
              } else {
                echo '<tr><td class="text-center" colspan="4">Keranjang anda kosong!</td></tr>';
              }
              ?>
            </tbody>
            <tfoot>
              <tr>
                <th class="text-end" colspan="3">Total</th>
                <th>Rp <?= number_format($cartTotal) ?></th>
              </tr>
            </tfoot>
          </table>
          <form action="<?= BASE_URL ?>cart/checkout.php" method="post">
            <input type="hidden" name="user_id" value="<?= isset($userid) ? $userid : '' ?>">
            <select class="form-select" name="payment_method" id="payment_method" required>
              <option value="" disabled selected>Metode Pembayaran</option>
              <option value="Transfer">Transfer</option>
              <option value="Tunai">Tunai</option>
            </select>
            <div class="d-flex justify-content-end">
              <button name="checkout" class="mt-3 btn btn-success">Checkout</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- End Cart -->
