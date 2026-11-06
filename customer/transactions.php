<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth-guard.php';

$pageTitle = "Pawshop | Riwayat Transaksi";
$file = '';
$status = '';

if (isset($_POST['submit']) && !empty($_POST['id'])) {
  $id = mysqli_real_escape_string($conn, $_POST['id']);
  $gambar = basename($_FILES["gambar"]["name"]);

  if (empty($gambar)) {
    $status = '<div class="mt-3 alert alert-danger" role="alert">File bukti pembayaran tidak boleh kosong!</div>';
  } else {
    $target_dir = ROOT_PATH . "/assets/images/";
    $uniqueName = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", $gambar);
    $target_file = $target_dir . $uniqueName;

    $check = getimagesize($_FILES["gambar"]["tmp_name"]);
    if ($check !== false) {
      if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
        $sql = "UPDATE transaksi SET status_id=3, bukti_pembayaran='$uniqueName' WHERE id='$id' AND user_id='$userid'";
        if (mysqli_query($conn, $sql)) {
          $status = '<div class="mt-3 alert alert-success" role="alert">Bukti pembayaran berhasil diunggah! Status: Dikirim.</div>';
        } else {
          $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal memperbarui status transaksi di database!</div>';
        }
      } else {
        $status = '<div class="mt-3 alert alert-danger" role="alert">Gagal mengunggah berkas!</div>';
      }
    } else {
      $status = '<div class="mt-3 alert alert-danger" role="alert">Berkas yang dipilih bukan gambar yang valid!</div>';
    }
  }
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold">Riwayat Transaksi Saya</h3>
    <a href="<?= BASE_URL ?>index.php" class="btn btn-outline-primary btn-sm">Belanja Lagi</a>
  </div>

  <?= $status ?>

  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>ID Transaksi</th>
            <th>Tanggal</th>
            <th>Total</th>
            <th>Metode Pembayaran</th>
            <th>Status</th>
            <th>Batas Pembayaran</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sql = "SELECT tr.id, tr.timestamp, tr.total_amount, tr.payment_method, st.name AS status_name, tr.expiry_date, tr.status_id, tr.bukti_pembayaran 
                  FROM transaksi tr 
                  JOIN status st ON tr.status_id=st.id 
                  WHERE user_id='$userid' 
                  ORDER BY tr.id DESC";
          $result = mysqli_query($conn, $sql);

          if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
              $transID = $row['id'];
              $expiry_date = $row['expiry_date'];
              $formattedDate = "-";
              if (!empty($expiry_date)) {
                $dateObj = DateTime::createFromFormat('ymdHis', substr($expiry_date, -12));
                if ($dateObj) {
                  $formattedDate = $dateObj->format('d-m-Y H:i');
                }
              }
          ?>
              <tr>
                <td>
                  <a href="<?= BASE_URL ?>customer/transaction-detail.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm fw-bold">
                    <?= $row['id'] ?>
                  </a>
                </td>
                <td><?= htmlspecialchars($row['timestamp']) ?></td>
                <td class="fw-bold">Rp <?= number_format($row['total_amount']) ?></td>
                <td><span class="badge bg-secondary"><?= htmlspecialchars($row['payment_method']) ?></span></td>
                <td>
                  <span class="badge <?= ($row['status_id'] == 7) ? 'bg-success' : (($row['status_id'] == 6) ? 'bg-danger' : 'bg-warning text-dark') ?>">
                    <?= htmlspecialchars($row['status_name']) ?>
                  </span>
                  <?php if ($row['status_id'] == 1): ?>
                    <form action="" method="post" enctype="multipart/form-data" class="mt-2">
                      <input type="hidden" name="id" value="<?= $row['id'] ?>">
                      <div class="input-group input-group-sm">
                        <input class="form-control" type="file" name="gambar" required accept="image/*">
                        <button class="btn btn-primary" type="submit" name="submit">Unggah</button>
                      </div>
                    </form>
                  <?php endif; ?>
                </td>
                <td class="small text-muted"><?= $formattedDate ?></td>
                <td>
                  <a href="<?= BASE_URL ?>customer/invoice.php?id=<?= $row['id'] ?>" class="btn btn-outline-secondary btn-sm" title="Lihat Invoice">
                    <i class="fa-solid fa-receipt"></i> Invoice
                  </a>
                </td>
              </tr>
          <?php
            }
          } else {
            echo '<tr><td colspan="7" class="text-center py-4 text-muted">Belum ada transaksi yang dibuat.</td></tr>';
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<?php
include __DIR__ . '/../includes/cart.php';
include __DIR__ . '/../includes/footer.php';
?>
