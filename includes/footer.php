  <footer class="mt-auto py-4 bg-white border-top text-center text-muted small">
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
    });
  </script>
</body>
</html>
