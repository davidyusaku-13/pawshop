<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-guard.php';

use App\OrderService;

// UBAH STATUS TRANSAKSI
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    if (!empty($_POST['edit_id']) && !empty($_POST['edit_status'])) {
        $transID = trim($_POST['edit_id']);
        $statusID = (int)$_POST['edit_status'];

        if (OrderService::updateStatus($transID, $statusID)) {
            flash('success', "Status transaksi #{$transID} berhasil diubah!");
        } else {
            flash('danger', 'Gagal mengubah status transaksi!');
        }
        redirect('admin/transactions.php');
    }
}

// HAPUS TRANSAKSI
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    if (!empty($_POST['delete_id'])) {
        $deleteID = trim($_POST['delete_id']);
        if (OrderService::deleteOrder($deleteID)) {
            flash('success', "Transaksi #{$deleteID} dan rinciannya berhasil dihapus!");
        } else {
            flash('danger', 'Gagal menghapus transaksi!');
        }
        redirect('admin/transactions.php');
    }
}

$statuses = OrderService::getAllStatuses();
$transactions = OrderService::getAllOrders();
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pawshop | Kelola Transaksi</title>
  <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>assets/images/brand/logo-title.png">
  <link href="<?= BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/datatables.min.css">
  <script src="<?= BASE_URL ?>assets/js/jquery-3.7.1.min.js"></script>
  <script src="<?= BASE_URL ?>assets/js/datatables.min.js"></script>
  <script src="https://kit.fontawesome.com/ec712a3d01.js" crossorigin="anonymous"></script>
</head>

<body>
  <?php include __DIR__ . '/../includes/admin-navbar.php'; ?>

  <div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="fw-bold">Manajemen Transaksi</h3>
      <a href="<?= BASE_URL ?>admin/reports.php" class="btn btn-outline-primary btn-sm">
        <i class="fa-solid fa-file-export me-1"></i> Laporan Transaksi
      </a>
    </div>

    <?= render_flash() ?>

    <!-- MODAL EDIT STATUS TRANSAKSI -->
    <div class="modal fade" id="editTransactions" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Ubah Status Transaksi</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="" method="POST">
            <div class="modal-body">
              <input type="hidden" name="edit_id" value="">
              <div class="form-floating mb-3">
                <input class="form-control" type="text" name="display_id" readonly>
                <label>ID Transaksi</label>
              </div>
              <div class="form-floating mb-3">
                <select class="form-select" name="edit_status" required>
                  <?php foreach ($statuses as $st): ?>
                    <option value="<?= $st['id']; ?>"><?= htmlspecialchars($st['name']); ?></option>
                  <?php endforeach; ?>
                </select>
                <label>Pilih Status Baru</label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" name="submit" class="btn btn-primary">Simpan Status</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- MODAL LIHAT BUKTI PEMBAYARAN -->
    <div class="modal fade" id="viewPaymentProof" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title fw-bold">Bukti Pembayaran</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body text-center">
            <img id="paymentProofImg" src="" class="img-fluid rounded border" alt="Bukti Pembayaran">
            <p id="noProofText" class="text-muted d-none my-4">Belum ada bukti pembayaran yang diunggah untuk transaksi ini.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL HAPUS TRANSAKSI -->
    <div class="modal fade" id="deleteTransactions" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <p class="modal-title fw-bold text-danger">Hapus Transaksi</p>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="" method="POST">
            <div class="modal-body">
              <input type="hidden" name="delete_id" value="">
              <p class="text-confirm text-muted">Apakah Anda yakin ingin menghapus transaksi ini?</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" name="delete" class="btn btn-danger">Hapus</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- TABEL TRANSAKSI -->
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle" id="transactionsDataTables">
            <thead>
              <tr>
                <th>ID Transaksi</th>
                <th>Tanggal</th>
                <th>Pengguna</th>
                <th>Total</th>
                <th>Metode</th>
                <th>Bukti</th>
                <th>Status</th>
                <th style="width: 140px;" class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($transactions as $row): ?>
                <tr>
                  <td class="font-monospace fw-bold"><?= $row['id']; ?></td>
                  <td><?= $row['timestamp']; ?></td>
                  <td class="fw-semibold"><?= htmlspecialchars($row['username']); ?></td>
                  <td class="fw-semibold text-primary"><?= format_rupiah($row['total_amount']); ?></td>
                  <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['payment_method']); ?></span></td>
                  <td>
                    <?php if (!empty($row['bukti_pembayaran'])): ?>
                      <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewPaymentProof" data-proof="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($row['bukti_pembayaran']); ?>">
                        <i class="fa-solid fa-image me-1"></i> Lihat
                      </button>
                    <?php else: ?>
                      <span class="text-muted small">Belum ada</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="badge <?= ($row['status_id'] == 1 || $row['status_id'] == 3) ? 'bg-warning text-dark' : 'bg-success' ?>">
                      <?= htmlspecialchars($row['status_name']); ?>
                    </span>
                  </td>
                  <td class="text-center">
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editTransactions" data-id="<?= $row['id']; ?>" title="Ubah Status">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteTransactions" data-id="<?= $row['id']; ?>" title="Hapus">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script src="<?= BASE_URL ?>assets/js/bootstrap.bundle.min.js"></script>
  <script src="<?= BASE_URL ?>assets/js/jquery.dataTables.min.js"></script>
  <script>
    new DataTable('#transactionsDataTables');

    $('#editTransactions').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var transId = button.data('id');
      $.ajax({
        url: '<?= BASE_URL ?>admin/ajax/transaction.php',
        type: 'GET',
        data: { id: transId },
        dataType: 'json',
        success: function(data) {
          if (!data.error) {
            $('#editTransactions input[name="edit_id"]').val(data.id);
            $('#editTransactions input[name="display_id"]').val(data.id);
            $('#editTransactions select[name="edit_status"]').val(data.status_id);
          }
        }
      });
    });

    $('#viewPaymentProof').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var proofUrl = button.data('proof');
      if (proofUrl) {
        $('#paymentProofImg').attr('src', proofUrl).removeClass('d-none');
        $('#noProofText').addClass('d-none');
      } else {
        $('#paymentProofImg').addClass('d-none');
        $('#noProofText').removeClass('d-none');
      }
    });

    $('#deleteTransactions').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var transId = button.data('id');
      $('#deleteTransactions .text-confirm').text('Apakah Anda yakin ingin menghapus transaksi #' + transId + '?');
      $('#deleteTransactions input[name="delete_id"]').val(transId);
    });
  </script>
</body>
</html>
