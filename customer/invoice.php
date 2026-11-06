<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth-guard.php';

use App\AuthService;
use App\OrderService;

$id = isset($_GET['id']) ? trim($_GET['id']) : '';
$order = !empty($id) ? OrderService::getOrderById($id) : null;
$userId = AuthService::getCurrentUserId();

if (!$order || ((int)$order['user_id'] !== $userId && !AuthService::isAdmin())) {
    flash('danger', 'Nota pesanan tidak ditemukan.');
    redirect('customer/transactions.php');
}

$details = OrderService::getOrderDetails($id);
$pageTitle = "Pawshop | Nota Pesanan " . $order['id'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>assets/images/brand/logo-title.png">
  <link href="<?= BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .invoice-card {
      max-width: 800px;
      margin: 30px auto;
      background: white;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05);
      padding: 40px;
    }
    @media print {
      body { background-color: white; }
      .invoice-card { box-shadow: none; padding: 0; margin: 0; max-width: 100%; }
      .no-print { display: none !important; }
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="invoice-card">
      <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-4">
        <div>
          <img src="<?= BASE_URL ?>assets/images/brand/logo.png" alt="Pawshop" width="160">
          <p class="text-muted small mt-2 mb-0">Toko Kebutuhan Kucing Terpercaya</p>
        </div>
        <div class="text-end">
          <h2 class="fw-bold mb-1 text-primary">NOTA RESMI</h2>
          <span class="text-muted font-monospace">#<?= htmlspecialchars($order['id']) ?></span>
          <p class="mb-0 text-muted small">Tanggal: <?= htmlspecialchars($order['timestamp']) ?></p>
        </div>
      </div>

      <div class="row mb-4">
        <div class="col-6">
          <p class="text-muted small mb-1 fw-bold text-uppercase">Diterbitkan Untuk:</p>
          <h5 class="fw-bold mb-1"><?= htmlspecialchars($order['username']) ?></h5>
          <p class="text-muted small mb-0">No. Telepon: <?= htmlspecialchars($order['phone_number'] ?? '-') ?></p>
        </div>
        <div class="col-6 text-end">
          <p class="text-muted small mb-1 fw-bold text-uppercase">Detail Pembayaran:</p>
          <p class="mb-1">Metode: <strong><?= htmlspecialchars($order['payment_method']) ?></strong></p>
          <p class="mb-0">Status: <span class="badge bg-secondary"><?= htmlspecialchars($order['status_name']) ?></span></p>
        </div>
      </div>

      <div class="table-responsive mb-4">
        <table class="table table-bordered">
          <thead class="table-light">
            <tr>
              <th style="width: 50px;">No</th>
              <th>Nama Produk</th>
              <th class="text-center" style="width: 100px;">Jumlah</th>
              <th class="text-end" style="width: 160px;">Harga Satuan</th>
              <th class="text-end" style="width: 160px;">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            foreach ($details as $row):
            ?>
              <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td class="fw-semibold"><?= htmlspecialchars($row['nama_produk']) ?></td>
                <td class="text-center"><?= $row['quantity'] ?></td>
                <td class="text-end"><?= format_rupiah($row['unit_price']) ?></td>
                <td class="text-end fw-bold"><?= format_rupiah($row['subtotal']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="4" class="text-end">Total Pembayaran</th>
              <th class="text-end text-danger fs-5"><?= format_rupiah($order['total_amount']) ?></th>
            </tr>
          </tfoot>
        </table>
      </div>

      <div class="border-top pt-3 text-center text-muted small">
        <p class="mb-1">Terima kasih atas kepercayaan Anda telah berbelanja di <strong>Pawshop</strong>.</p>
        <p class="mb-0">Jika ada pertanyaan mengenai pesanan ini, silakan hubungi layanan pelanggan kami.</p>
      </div>

      <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-primary me-2">
          Cetak Nota
        </button>
        <a href="<?= BASE_URL ?>customer/transactions" class="btn btn-outline-secondary">
          Kembali
        </a>
      </div>
    </div>
  </div>
</body>

</html>
