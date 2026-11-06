<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-guard.php';

use App\OrderService;

$pageTitle = "Manajemen Transaksi";

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

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-1">Daftar Transaksi Pesanan</h4>
    <p class="text-muted small mb-0">Verifikasi bukti transfer dan perbarui status pengiriman pelanggan</p>
  </div>
  <a href="<?= BASE_URL ?>admin/reports" class="btn btn-outline-dark btn-sm">
    <i class="fa-solid fa-file-export me-1"></i> Rekap Laporan Penjualan
  </a>
</div>

<?= render_flash() ?>

<!-- MODAL EDIT STATUS TRANSAKSI -->
<div class="modal fade" id="editTransactions" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Ubah Status Transaksi</h5>
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
          <button type="submit" name="submit" class="btn btn-pawshop">Simpan Status</button>
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
      <div class="modal-body text-center p-4">
        <img id="paymentProofImg" src="" class="img-fluid rounded border shadow-sm" alt="Bukti Pembayaran" style="max-height: 480px;">
        <p id="noProofText" class="text-muted d-none my-4">Belum ada bukti pembayaran yang diunggah untuk transaksi ini.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- MODAL HAPUS TRANSAKSI -->
<div class="modal fade" id="deleteTransactions" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold text-danger">Hapus Transaksi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="" method="POST">
        <div class="modal-body">
          <input type="hidden" name="delete_id" value="">
          <p class="text-confirm text-muted">Apakah Anda yakin ingin menghapus transaksi ini beserta rincian itemnya?</p>
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
<div class="card shadow-sm border-0">
  <div class="card-body p-4">
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle mb-0" id="transactionsDataTables">
        <thead class="table-light">
          <tr>
            <th>ID Transaksi</th>
            <th>Tanggal</th>
            <th>Pengguna</th>
            <th>Total</th>
            <th>Metode</th>
            <th>Bukti</th>
            <th>Status</th>
            <th style="width: 120px;" class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($transactions as $row): ?>
            <tr>
              <td class="font-monospace fw-bold"><?= $row['id']; ?></td>
              <td><?= $row['timestamp']; ?></td>
              <td class="fw-semibold"><?= htmlspecialchars($row['username']); ?></td>
              <td class="fw-bold text-primary"><?= format_rupiah($row['total_amount']); ?></td>
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
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTransactions" data-id="<?= $row['id']; ?>" title="Ubah Status">
                  <i class="fa-solid fa-pen-to-square"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteTransactions" data-id="<?= $row['id']; ?>" title="Hapus">
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

<script>
  $(document).ready(function() {
    new DataTable('#transactionsDataTables');

    $('#editTransactions').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var transId = button.data('id');
      $.ajax({
        url: '<?= BASE_URL ?>admin/ajax/transaction',
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
  });
</script>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
