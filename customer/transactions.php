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

<main class="container py-5">
  <!-- Page Header -->
  <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
      <h3 class="fw-bold mb-1">Riwayat Transaksi Saya</h3>
      <p class="text-muted small mb-0">Pantau status pembayaran, proses pengiriman, dan nota belanja Anda</p>
    </div>
    <a href="<?= BASE_URL ?>index.php" class="btn btn-outline-dark btn-sm rounded-pill px-3">
      <i class="fa-solid fa-basket-shopping me-1"></i> Belanja Lagi
    </a>
  </div>

  <?= render_flash() ?>

  <!-- Order Cards List -->
  <?php if (!empty($orders)): ?>
    <div class="d-flex flex-column gap-3">
      <?php foreach ($orders as $row): ?>
        <?php
        $statusId = (int)$row['status_id'];
        $badgeClass = 'bg-secondary';
        $badgeIcon = 'fa-circle-info';

        if ($statusId === 1) {
            $badgeClass = 'bg-warning text-dark';
            $badgeIcon = 'fa-clock';
        } elseif ($statusId === 2) {
            $badgeClass = 'bg-info text-white';
            $badgeIcon = 'fa-boxes-packing';
        } elseif ($statusId === 3) {
            $badgeClass = 'bg-primary';
            $badgeIcon = 'fa-truck-fast';
        } elseif ($statusId === 4) {
            $badgeClass = 'bg-success';
            $badgeIcon = 'fa-circle-check';
        } elseif ($statusId === 5) {
            $badgeClass = 'bg-danger';
            $badgeIcon = 'fa-circle-xmark';
        }

        $formattedExpiry = "-";
        if (!empty($row['expiry_date'])) {
            $formattedExpiry = substr($row['expiry_date'], 0, 2) . '-' . substr($row['expiry_date'], 2, 2) . '-20' . substr($row['expiry_date'], 4, 2);
        }
        ?>
        <div class="card border rounded-3 shadow-none bg-white">
          <!-- Card Header -->
          <div class="card-header bg-light border-bottom d-flex flex-wrap justify-content-between align-items-center py-3 px-4 gap-2">
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <span class="badge bg-dark font-monospace px-2 py-1">ID #<?= $row['id'] ?></span>
              <span class="text-muted small">
                <i class="fa-regular fa-calendar me-1"></i> <?= $row['timestamp'] ?>
              </span>
            </div>
            <span class="badge <?= $badgeClass ?> px-3 py-2 rounded-pill small">
              <i class="fa-solid <?= $badgeIcon ?> me-1"></i> <?= htmlspecialchars($row['status_name']) ?>
            </span>
          </div>

          <!-- Card Body -->
          <div class="card-body p-4">
            <div class="row g-3 align-items-center">
              <div class="col-md-7">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <span class="text-muted small">Metode Pembayaran:</span>
                  <span class="badge bg-light text-dark border"><?= htmlspecialchars($row['payment_method']) ?></span>
                </div>
                <?php if ($statusId === 1): ?>
                  <div class="alert alert-warning bg-opacity-25 border-warning py-2 px-3 small mb-0 d-inline-block">
                    <i class="fa-solid fa-triangle-exclamation text-warning me-1"></i>
                    Batas Akhir Pembayaran: <strong><?= $formattedExpiry ?></strong>
                  </div>
                <?php else: ?>
                  <p class="text-muted small mb-0">
                    <i class="fa-solid fa-circle-check text-success me-1"></i> Pembayaran untuk transaksi ini telah diverifikasi oleh pengelola toko.
                  </p>
                <?php endif; ?>
              </div>
              <div class="col-md-5 text-md-end">
                <span class="text-muted small d-block mb-1">Total Tagihan:</span>
                <span class="fs-4 fw-bold text-danger"><?= format_rupiah($row['total_amount']) ?></span>
              </div>
            </div>
          </div>

          <!-- Card Footer -->
          <div class="card-footer bg-white border-top d-flex flex-wrap justify-content-between align-items-center py-3 px-4 gap-2">
            <div class="d-flex gap-2">
              <a href="<?= BASE_URL ?>customer/transaction-detail.php?id=<?= $row['id'] ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fa-solid fa-list me-1"></i> Rincian Pesanan
              </a>
              <a href="<?= BASE_URL ?>customer/invoice.php?id=<?= $row['id'] ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="fa-solid fa-receipt me-1"></i> Nota
              </a>
            </div>

            <?php if ($statusId === 1): ?>
              <button type="button" class="btn btn-warning text-dark fw-bold btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadProofModal" data-id="<?= $row['id'] ?>">
                <i class="fa-solid fa-upload me-1"></i> Upload Bukti Transfer
              </button>
            <?php else: ?>
              <span class="small text-muted">
                <i class="fa-solid fa-check-double text-success me-1"></i> Terkonfirmasi
              </span>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <!-- Empty State -->
    <div class="card border-0 shadow-sm p-5 text-center bg-white rounded-4">
      <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 80px; height: 80px;">
        <i class="fa-solid fa-clock-rotate-left text-muted fs-2"></i>
      </div>
      <h5 class="fw-bold text-dark">Belum Ada Transaksi</h5>
      <p class="text-muted small">Anda belum melakukan pemesanan apa pun. Mulai penuhi kebutuhan anabul kesayangan Anda!</p>
      <a href="<?= BASE_URL ?>index.php" class="btn btn-pawshop btn-sm rounded-pill px-4 mx-auto mt-2">
        Mulai Belanja Sekarang
      </a>
    </div>
  <?php endif; ?>
</main>

<!-- MODAL UPLOAD BUKTI PEMBAYARAN -->
<div class="modal fade" id="uploadProofModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <div class="modal-header bg-light py-3">
        <h5 class="modal-title fw-bold">Upload Bukti Transfer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <form action="" method="POST" enctype="multipart/form-data">
        <div class="modal-body p-4">
          <input type="hidden" name="id" value="">

          <div class="alert alert-info border-info bg-opacity-25 small mb-3">
            <div class="fw-bold mb-1"><i class="fa-solid fa-building-columns me-1"></i> Rekening Resmi Pawshop:</div>
            <div>BCA: <strong>123-456-7890</strong> a.n. Pawshop Indonesia</div>
            <div>Mandiri: <strong>987-654-3210</strong> a.n. Pawshop Indonesia</div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Pilih Berkas Bukti Pembayaran (JPG/PNG/WEBP):</label>
            <input class="form-control" type="file" name="gambar" id="proofFileInput" accept="image/jpeg,image/png,image/webp" required>
          </div>

          <!-- Instant Live Image Preview -->
          <div id="proofPreviewBox" class="text-center d-none mt-3 p-3 border rounded-3 bg-light">
            <span class="small text-muted d-block mb-2 fw-semibold">Pratinjau Bukti Transfer:</span>
            <img id="proofPreviewImg" src="" class="img-fluid rounded border shadow-sm" style="max-height: 240px;" alt="Pratinjau">
          </div>
        </div>
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="submit" class="btn btn-pawshop btn-sm rounded-pill px-4 fw-bold shadow-sm">
            <i class="fa-solid fa-paper-plane me-1"></i> Kirim Bukti
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var uploadModal = document.getElementById('uploadProofModal');
    if (uploadModal) {
      uploadModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var transId = button.getAttribute('data-id');
        uploadModal.querySelector('input[name="id"]').value = transId;
        
        // Reset preview
        var previewBox = document.getElementById('proofPreviewBox');
        var previewImg = document.getElementById('proofPreviewImg');
        var fileInput = document.getElementById('proofFileInput');
        if (fileInput) fileInput.value = '';
        if (previewBox) previewBox.classList.add('d-none');
        if (previewImg) previewImg.src = '';
      });
    }

    var fileInput = document.getElementById('proofFileInput');
    if (fileInput) {
      fileInput.addEventListener('change', function() {
        var file = this.files[0];
        var previewBox = document.getElementById('proofPreviewBox');
        var previewImg = document.getElementById('proofPreviewImg');
        if (file && previewBox && previewImg) {
          previewImg.src = URL.createObjectURL(file);
          previewBox.classList.remove('d-none');
        }
      });
    }
  });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
