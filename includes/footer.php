  <script src="<?= BASE_URL ?>assets/js/bootstrap.bundle.min.js"></script>
  <script>
    <?php
    if (isset($_SESSION['cart_status']) && $_SESSION['cart_status'] == 1) {
      echo 'alert("Berhasil menambah keranjang!!");';
      unset($_SESSION['cart_status']);
    }
    if (isset($_SESSION['transaction_status']) && $_SESSION['transaction_status'] == 1) {
      echo 'alert("Transaksi berhasil dibuat!!");';
      unset($_SESSION['transaction_status']);
    }
    ?>
  </script>
</body>
</html>
