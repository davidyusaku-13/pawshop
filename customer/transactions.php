<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth-guard.php';

use App\AuthService;
use App\OrderService;

$pageTitle = "Pawshop | Riwayat Transaksi";
$userId = AuthService::getCurrentUserId();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit']) && !empty($_POST['id'])) {
    $orderId = trim($_POST['id']);
    $error = null;

    if (empty($_FILES['gambar']['name'])) {
        flash('danger', 'File bukti pembayaran tidak boleh kosong!');
    } else {
        if (OrderService::uploadPaymentProof($orderId, $userId, $_FILES['gambar'], $error)) {
            flash('success', 'Bukti pembayaran berhasil diunggah! Status telah diperbarui menjadi "Dikirim".');
        } else {
            flash('danger', $error ?: 'Gagal mengunggah bukti pembayaran!');
        }
    }
    redirect('customer/transactions.php');
}

$orders = OrderService::getOrdersByUser($userId);

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold">Riwayat Transaksi Saya</h3>
    <a href="<?= BASE_URL ?>index.php" class="btn btn-outline-warning text-dark btn-sm fw-semibold">Belanja Lagi</a>
  </div>

  <?= render_flash() ?>

  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>ID Transaksi</th>
            <th>Tanggal</th>
            <th>Total Tagihan</th>
            <th>Metode Pembayaran</th>
            <th>Status</th>
            <th>Batas Pembayaran</th>
            <th class="text-center" style="width: 220px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($orders)): foreach ($orders as $row): ?>
            <tr>
              <td class="font-monospace fw-bold"><?= $row['id'] ?></td>
              <td><?= $row['timestamp'] ?></td>
              <td class="fw-semibold text-danger"><?= format_rupiah($row['total_amount']) ?></td>
              <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['payment_method']) ?></span></td>
              <td>
                <span class="badge <?= ($row['status_id'] == 1 || $row['status_id'] == 3) ? 'bg-warning text-dark' : 'bg-success' ?>">
                  <?= htmlspecialchars($row['status_name']) ?>
                </span>
              </td>
              <td>
                <?php
                if (!empty($row['expiry_date'])) {
                  $formattedExpiry = substr($row['expiry_date'], 0, 2) . '-' . substr($row['expiry_date'], 2, 2) . '-20' . substr($row['expiry_date'], 4, 2);
                  echo htmlspecialchars($formattedExpiry);
                } else {
                  echo "-";
                }
                ?>
              </td>
              <td class="text-center">
                <a href="<?= BASE_URL ?>customer/transaction-detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-secondary">
                  <i class="fa-solid fa-list me-1"></i> Rincian
                </a>
                <a href="<?= BASE_URL ?>customer/invoice.php?id=<?= $row['id'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                  <i class="fa-solid fa-receipt me-1"></i> Nota
                </a>
                <?php if ($row['status_id'] == 1): ?>
                  <button type="button" class="btn btn-sm btn-warning fw-semibold" data-bs-toggle="modal" data-bs-target="#uploadProofModal" data-id="<?= $row['id'] ?>">
                    <i class="fa-solid fa-upload me-1"></i> Bayar
                  </button>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; else: ?>
            <tr>
              <td colspan="7" class="text-center py-5 text-muted">
                Belum ada transaksi pembelian. <a href="<?= BASE_URL ?>index.php" class="fw-bold">Mulai belanja sekarang!</a>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- MODAL UPLOAD BUKTI PEMBAYARAN -->
  <div class="modal fade" id="uploadProofModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold">Unggah Bukti Pembayaran</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="" method="POST" enctype="multipart/form-data">
          <div class="modal-body">
            <input type="hidden" name="id" id="uploadOrderId">
            <p class="text-muted small">Pilih foto struk bukti transfer atau pembayaran untuk diverifikasi oleh admin (Maks 5MB: JPG, PNG, WEBP).</p>
            <div class="mb-3">
              <label class="form-label fw-semibold">Pilih Berkas Foto:</label>
              <input class="form-control" type="file" name="gambar" accept="image/jpeg,image/png,image/webp" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" name="submit" class="btn btn-primary">Unggah Sekarang</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</main>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('uploadProofModal');
    if (modal) {
      modal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var orderId = button.getAttribute('data-id');
        document.getElementById('uploadOrderId').value = orderId;
      });
    }
  });
</script>

<?php
include __DIR__ . '/../includes/cart.php';
include __DIR__ . '/../includes/footer.php';
?>
