<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth-guard.php';

$transID = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';
if (empty($transID)) {
  header('Location: ' . BASE_URL . 'customer/transactions.php');
  exit();
}

$pageTitle = "Pawshop | Detail Transaksi " . $transID;

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="container py-4">
  <div class="mb-3 d-flex justify-content-between align-items-center">
    <a class="btn btn-outline-secondary btn-sm" href="<?= BASE_URL ?>customer/transactions.php">
      <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Transaksi
    </a>
    <a href="<?= BASE_URL ?>customer/invoice.php?id=<?= urlencode($transID) ?>" class="btn btn-primary btn-sm">
      <i class="fa-solid fa-print me-1"></i> Cetak Invoice
    </a>
  </div>

  <div class="card shadow-sm">
    <div class="card-header bg-light">
      <h5 class="mb-0 fw-bold">Detail Pesanan: <?= htmlspecialchars($transID) ?></h5>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Gambar</th>
            <th>Nama Produk</th>
            <th class="text-center">Jumlah</th>
            <th class="text-end">Harga Satuan</th>
            <th class="text-end">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sql = "SELECT trd.*, p.nama_produk, p.gambar 
                  FROM transaksi_detail trd 
                  JOIN produk p ON trd.product_id=p.id 
                  WHERE transactions_id='$transID'";
          $result = mysqli_query($conn, $sql);
          $grandTotal = 0;

          if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
              $grandTotal += $row['subtotal'];
          ?>
              <tr>
                <td style="width: 80px;">
                  <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($row['gambar']) ?>" alt="" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: contain;">
                </td>
                <td class="fw-semibold"><?= htmlspecialchars($row['nama_produk']) ?></td>
                <td class="text-center"><?= $row['quantity'] ?></td>
                <td class="text-end">Rp <?= number_format($row['unit_price']) ?></td>
                <td class="text-end fw-bold">Rp <?= number_format($row['subtotal']) ?></td>
              </tr>
          <?php
            }
          } else {
            echo '<tr><td colspan="5" class="text-center py-4 text-muted">Detail item transaksi tidak ditemukan.</td></tr>';
          }
          ?>
        </tbody>
        <tfoot class="table-light">
          <tr>
            <th colspan="4" class="text-end">Total Pembayaran</th>
            <th class="text-end fs-5 text-primary">Rp <?= number_format($grandTotal) ?></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</main>

<?php
include __DIR__ . '/../includes/cart.php';
include __DIR__ . '/../includes/footer.php';
?>
