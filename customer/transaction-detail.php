<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth-guard.php';

use App\AuthService;
use App\OrderService;

$id = isset($_GET['id']) ? trim($_GET['id']) : '';
$order = !empty($id) ? OrderService::getOrderById($id) : null;
$userId = AuthService::getCurrentUserId();

if (!$order || ((int)$order['user_id'] !== $userId && !AuthService::isAdmin())) {
    flash('danger', 'Transaksi tidak ditemukan atau Anda tidak memiliki akses.');
    redirect('customer/transactions.php');
}

$details = OrderService::getOrderDetails($id);
$pageTitle = "Pawshop | Detail Transaksi " . $order['id'];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold">Rincian Transaksi #<?= htmlspecialchars($order['id']) ?></h3>
    <a href="<?= BASE_URL ?>customer/transactions.php" class="btn btn-outline-secondary btn-sm">
      <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Riwayat
    </a>
  </div>

  <div class="row g-4">
    <div class="col-md-8">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-bold">Daftar Produk yang Dipesan</div>
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th style="width: 70px;">Foto</th>
                <th>Nama Produk</th>
                <th class="text-center">Jumlah</th>
                <th class="text-end">Harga Satuan</th>
                <th class="text-end">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($details as $row): ?>
                <tr>
                  <td>
                    <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($row['gambar'] ?: 'x.jpg') ?>" class="rounded object-fit-cover" width="50" height="50" alt="" onerror="this.src='<?= BASE_URL ?>assets/images/brand/logo.png'">
                  </td>
                  <td class="fw-semibold"><?= htmlspecialchars($row['nama_produk']) ?></td>
                  <td class="text-center"><?= $row['quantity'] ?></td>
                  <td class="text-end"><?= format_rupiah($row['unit_price']) ?></td>
                  <td class="text-end fw-bold"><?= format_rupiah($row['subtotal']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot class="table-light">
              <tr>
                <th colspan="4" class="text-end">Total Pembayaran:</th>
                <th class="text-end text-danger fs-5"><?= format_rupiah($order['total_amount']) ?></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-bold">Informasi Pembelian</div>
        <div class="card-body">
          <?php
          $statusId = (int)$order['status_id'];
          $badgeClass = ($statusId === 1) ? 'bg-warning text-dark' : (($statusId === 4) ? 'bg-success' : 'bg-primary');
          ?>
          <p class="mb-1 text-muted small">Status Pesanan:</p>
          <span class="badge <?= $badgeClass ?> mb-3 px-3 py-2 rounded-pill small"><?= htmlspecialchars($order['status_name']) ?></span>

          <p class="mb-1 text-muted small">Waktu Pemesanan:</p>
          <p class="fw-semibold mb-3"><?= htmlspecialchars($order['timestamp']) ?></p>

          <p class="mb-1 text-muted small">Metode Pembayaran:</p>
          <p class="fw-semibold mb-3"><?= htmlspecialchars($order['payment_method']) ?></p>

          <p class="mb-1 text-muted small">Nama Pelanggan:</p>
          <p class="fw-semibold mb-3"><?= htmlspecialchars($order['username']) ?></p>

          <a href="<?= BASE_URL ?>customer/invoice.php?id=<?= $order['id'] ?>" target="_blank" class="btn btn-outline-primary w-100 mt-2">
            <i class="fa-solid fa-receipt me-1"></i> Cetak Bukti / Nota
          </a>
        </div>
      </div>

      <?php if (!empty($order['bukti_pembayaran'])): ?>
        <div class="card shadow-sm">
          <div class="card-header bg-white fw-bold">Bukti Transfer</div>
          <div class="card-body text-center">
            <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($order['bukti_pembayaran']) ?>" class="img-fluid rounded border" alt="Bukti Pembayaran">
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php
include __DIR__ . '/../includes/footer.php';
?>
