<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-guard.php';

$status = '';

if (isset($_POST['submit'])) {
  if (!empty($_POST['editID']) && !empty($_POST['edit_status'])) {
    $editID = mysqli_real_escape_string($conn, $_POST['editID']);
    $edit_status = (int)$_POST['edit_status'];
    $sql = "UPDATE transaksi SET status_id='$edit_status' WHERE id='$editID'";
    if (mysqli_query($conn, $sql)) {
      $status = '<div class="mt-3 alert alert-success" role="alert">Status transaksi berhasil diperbarui!</div>';
    } else {
      $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal memperbarui status!</div>';
    }
  }

  if (!empty($_POST['deleteID'])) {
    $deleteID = mysqli_real_escape_string($conn, $_POST['deleteID']);
    mysqli_query($conn, "DELETE FROM transaksi_detail WHERE transactions_id='$deleteID'");
    if (mysqli_query($conn, "DELETE FROM transaksi WHERE id='$deleteID'")) {
      $status = '<div class="mt-3 alert alert-success" role="alert">Transaksi berhasil dihapus!</div>';
    } else {
      $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal menghapus transaksi!</div>';
    }
  }
}
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

  <div class="container-fluid px-4 mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="fw-bold">Manajemen Transaksi</h3>
      <a href="<?= BASE_URL ?>admin/reports.php" class="btn btn-outline-primary btn-sm">
        <i class="fa-solid fa-chart-line me-1"></i> Lihat Laporan Penjualan
      </a>
    </div>

    <?= $status; ?>

    <!-- MODAL EDIT STATUS TRANSAKSI -->
    <div class="modal fade" id="editTransactions" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Edit Status Transaksi</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="" method="POST">
            <div class="modal-body">
              <input type="hidden" name="editID" value="">
              <div class="form-floating mb-3">
                <select class="form-select" name="edit_status" required>
                  <option value="" disabled>Pilih Status</option>
                  <?php
                  $stQ = mysqli_query($conn, 'SELECT * FROM status');
                  while ($stRow = mysqli_fetch_assoc($stQ)) {
                    echo "<option value='{$stRow['id']}'>" . htmlspecialchars($stRow['name']) . "</option>";
                  }
                  ?>
                </select>
                <label>Status Transaksi</label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" name="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
          </form>
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
              <input type="hidden" name="deleteID" value="">
              <p class="text-confirm text-muted">Apakah Anda yakin ingin menghapus transaksi ini?</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" name="submit" class="btn btn-danger">Hapus</button>
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
                <th style="width: 50px;">Aksi</th>
                <th>ID Transaksi</th>
                <th>Tanggal</th>
                <th>Pembeli</th>
                <th>Total</th>
                <th>Metode</th>
                <th>Status</th>
                <th>Bukti Pembayaran</th>
                <th>Batas Waktu</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $fetch = "SELECT tr.id, tr.timestamp, u.username, tr.total_amount, tr.payment_method, tr.bukti_pembayaran, st.name AS status_name, tr.expiry_date, tr.status_id 
                        FROM transaksi tr 
                        JOIN users u ON tr.user_id=u.id 
                        JOIN status st ON tr.status_id=st.id 
                        ORDER BY tr.id DESC";
              $res = mysqli_query($conn, $fetch);
              if ($res && mysqli_num_rows($res) > 0) {
                while ($row = mysqli_fetch_assoc($res)) {
              ?>
                  <tr>
                    <td>
                      <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-id="<?= $row['id']; ?>" data-bs-target="#deleteTransactions">
                        <i class="fa-solid fa-trash"></i>
                      </button>
                    </td>
                    <td class="fw-semibold">
                      <a href="<?= BASE_URL ?>customer/transaction-detail.php?id=<?= $row['id'] ?>" target="_blank" class="text-decoration-none">
                        <?= $row['id']; ?>
                      </a>
                    </td>
                    <td><?= htmlspecialchars($row['timestamp']); ?></td>
                    <td><?= htmlspecialchars($row['username']); ?></td>
                    <td class="fw-bold">Rp <?= number_format($row['total_amount']); ?></td>
                    <td><span class="badge bg-secondary"><?= htmlspecialchars($row['payment_method']); ?></span></td>
                    <td>
                      <span class="badge <?= ($row['status_id'] == 7) ? 'bg-success' : (($row['status_id'] == 6) ? 'bg-danger' : 'bg-warning text-dark') ?>">
                        <?= htmlspecialchars($row['status_name']); ?>
                      </span>
                      <button class="btn btn-sm btn-link p-0 ms-1" data-bs-toggle="modal" data-id="<?= $row['id']; ?>" data-bs-target="#editTransactions">
                        <i class="fa-solid fa-pen-to-square"></i>
                      </button>
                    </td>
                    <td>
                      <?php if (!empty($row['bukti_pembayaran'])): ?>
                        <a href="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($row['bukti_pembayaran']) ?>" target="_blank" class="btn btn-sm btn-outline-info">
                          <i class="fa-solid fa-image me-1"></i> Lihat
                        </a>
                      <?php else: ?>
                        <span class="text-muted small">-</span>
                      <?php endif; ?>
                    </td>
                    <td class="small text-muted"><?= htmlspecialchars($row['expiry_date'] ?? '-'); ?></td>
                  </tr>
              <?php
                }
              }
              ?>
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
            $('#editTransactions input[name="editID"]').val(data.id);
            $('#editTransactions select[name="edit_status"]').val(data.status_id);
          }
        }
      });
    });

    $('#deleteTransactions').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var transId = button.data('id');
      $('#deleteTransactions .text-confirm').text('Apakah Anda yakin ingin menghapus transaksi ' + transId + '?');
      $('#deleteTransactions input[name="deleteID"]').val(transId);
    });
  </script>
</body>
</html>
