<?php require __DIR__ . '/cart.php'; ?>

  <footer class="mt-auto py-5 bg-white border-top text-center text-muted small">
    <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
      <div class="d-flex align-items-center gap-2">
        <img src="<?= BASE_URL ?>assets/images/brand/logo.png" height="24" alt="Pawshop">
        <span>&copy; <?= date('Y') ?> Pawshop Pet Supplies. Seluruh hak cipta dilindungi.</span>
      </div>
      <div>
        <a href="<?= BASE_URL ?>index.php" class="text-muted text-decoration-none me-3">Katalog</a>
        <a href="<?= BASE_URL ?>customer/transactions.php" class="text-muted text-decoration-none">Riwayat Belanja</a>
      </div>
    </div>
  </footer>

  <script src="<?= BASE_URL ?>assets/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      <?php if (isset($_SESSION['cart_status']) && $_SESSION['cart_status'] == 1): ?>
        var cartEl = document.getElementById('cart');
        if (cartEl) {
          var offcanvasCart = new bootstrap.Offcanvas(cartEl);
          offcanvasCart.show();
        }
        <?php unset($_SESSION['cart_status']); ?>
      <?php endif; ?>

      // Modern Cart Stepper AJAX Interactions
      function updateCartItem(productId, newQty, cardEl) {
        var formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', newQty);
        formData.append('ajax', '1');

        fetch('<?= BASE_URL ?>cart/update.php', {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
          if (data && data.success) {
            var qtyInput = cardEl.querySelector('.cart-qty-val');
            if (qtyInput) qtyInput.value = data.quantity;

            var subtotalEl = cardEl.querySelector('.item-subtotal-val');
            if (subtotalEl) subtotalEl.textContent = data.itemSubtotal;

            var totalEl = document.getElementById('cartTotalVal');
            if (totalEl) totalEl.textContent = data.cartTotal;

            var badgeEl = document.getElementById('navCartBadge');
            if (badgeEl) badgeEl.textContent = data.cartCount;
          }
        })
        .catch(function(err) {
          console.error('Error updating cart:', err);
        });
      }

      document.querySelectorAll('.btn-step-inc').forEach(function(btn) {
        btn.addEventListener('click', function() {
          var card = this.closest('.cart-item-card');
          var input = card.querySelector('.cart-qty-val');
          var max = parseInt(this.getAttribute('data-max') || '999', 10);
          var cur = parseInt(input.value, 10);
          if (cur < max) {
            updateCartItem(this.getAttribute('data-id'), cur + 1, card);
          }
        });
      });

      document.querySelectorAll('.btn-step-dec').forEach(function(btn) {
        btn.addEventListener('click', function() {
          var card = this.closest('.cart-item-card');
          var input = card.querySelector('.cart-qty-val');
          var cur = parseInt(input.value, 10);
          var prodId = this.getAttribute('data-id');
          if (cur > 1) {
            updateCartItem(prodId, cur - 1, card);
          } else {
            if (confirm('Hapus produk ini dari keranjang belanja?')) {
              window.location.href = '<?= BASE_URL ?>cart/remove.php?product_id=' + prodId;
            }
          }
        });
      });
    });
  </script>
</body>
</html>
