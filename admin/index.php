<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-guard.php';

$updateProdukStatus = '';
$updateTransaksiStatus = '';

if (isset($_POST['transaksi-edit-simpan'])) {
  if (!empty($_POST['transaksi-edit-id']) && !empty($_POST['transaksi-edit-status'])) {
    $transID = mysqli_real_escape_string($conn, $_POST['transaksi-edit-id']);
    $status = (int)$_POST['transaksi-edit-status'];

    $updateTransaksi = "UPDATE transaksi SET status_id='$status' WHERE id='$transID'";
    if ($conn->query($updateTransaksi) === TRUE) {
      $updateTransaksiStatus = '<script>window.alert("Berhasil mengubah status!!");</script>';
    } else {
      $updateTransaksiStatus = '<script>window.alert("Gagal mengubah status!!");</script>';
    }
  }
}

if (isset($_POST['stok-edit-simpan'])) {
  if (
    !empty($_POST['stok-edit-id']) && !empty($_POST['stok-edit-nama']) && !empty($_POST['stok-edit-kategori']) &&
    isset($_POST['stok-edit-stok']) && !empty($_POST['stok-edit-harga'])
  ) {
    $prodID = (int)$_POST['stok-edit-id'];
    $nama_produk = mysqli_real_escape_string($conn, $_POST['stok-edit-nama']);
    $category_id = (int)$_POST['stok-edit-kategori'];
    $stok = (int)$_POST['stok-edit-stok'];
    $harga = (int)$_POST['stok-edit-harga'];
    $detail = mysqli_real_escape_string($conn, $_POST['stok-edit-detail'] ?? '');

    if (empty($_FILES['stok-edit-gambar']['name'])) {
      $updateProduk = "UPDATE produk SET nama_produk='$nama_produk', category_id='$category_id', stok='$stok', harga='$harga', detail='$detail' WHERE id=$prodID";
    } else {
      $gambarName = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", basename($_FILES['stok-edit-gambar']['name']));
      $target_file = ROOT_PATH . "/assets/images/" . $gambarName;
      move_uploaded_file($_FILES['stok-edit-gambar']['tmp_name'], $target_file);
      $updateProduk = "UPDATE produk SET gambar='$gambarName', nama_produk='$nama_produk', category_id='$category_id', stok='$stok', harga='$harga', detail='$detail' WHERE id=$prodID";
    }

    if ($conn->query($updateProduk) === TRUE) {
      $updateProdukStatus = '<script>window.alert("Berhasil mengubah produk!!");</script>';
    } else {
      $updateProdukStatus = '<script>window.alert("Gagal mengubah produk!!");</script>';
    }
  }
}

$minStok = 20;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            "pawshop-pemasukan": "#20639B",
            "pawshop-gatau": "#ED553B",
            "pawshop-stok": "#3CAEA3",
            "pawshop-transaksi": "#F6D55C",
            "pawshop-grafik": "#173F5F",
            "pawshop-background": "#DCDDDD",
            "pawshop-kiri": "#353E52",
            "pawshop-bulatan-avatar": "#293144",
            "pawshop-tulisan-kiri": "#8495B3",
            "pawshop-yellow-darker": "#E0B828",
            "pawshop-maurin-pink": "rgb(255, 150, 215)",
          },
        },
      },
    }
  </script>
  <script src="https://kit.fontawesome.com/ec712a3d01.js" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
  <link rel="shortcut icon" href="<?= BASE_URL ?>assets/images/brand/logo-title.png" type="image/x-icon" />
  <style>
    body { font-family: "Montserrat", sans-serif; }
  </style>
  <title>Pawshop | Dasbor Admin</title>
</head>

<body class="bg-pawshop-background">
  <?= $updateTransaksiStatus ?>
  <?= $updateProdukStatus ?>

  <!-- CONTENT WRAPPER -->
  <div class="flex">
    <?php include __DIR__ . '/../includes/admin-sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="m-auto flex-1 p-8">
      <!-- LOGO -->
      <div class="flex justify-end mb-4">
        <img src="<?= BASE_URL ?>assets/images/brand/logo.png" class="w-48" alt="Logo">
      </div>

      <!-- CARDS -->
      <div class="grid grid-cols-2 gap-4">
        <!-- TOTAL REVENUE -->
        <div class="bg-pawshop-pemasukan rounded-lg text-white p-6">
          <h1 class="font-semibold text-lg">PEMASUKAN (TOTAL)</h1>
          <?php
          $sql = "SELECT COALESCE(SUM(total_amount), 0) AS total FROM transaksi";
          $result = $conn->query($sql);
          $pemasukan = ($result && $r = $result->fetch_assoc()) ? (float)$r['total'] : 0;
          ?>
          <h2 class="font-black text-2xl mt-2">Rp <?= number_format($pemasukan, 2, ",", ".") ?></h2>
        </div>

        <!-- DAILY REVENUE -->
        <div class="bg-pawshop-gatau rounded-lg text-white p-6">
          <h1 class="font-semibold text-lg">PEMASUKAN (HARIAN)</h1>
          <?php
          $datenow = date("d-m-Y");
          $sql = "SELECT COALESCE(SUM(total_amount), 0) AS harian FROM transaksi WHERE timestamp='$datenow'";
          $result = $conn->query($sql);
          $harian = ($result && $r = $result->fetch_assoc()) ? (float)$r['harian'] : 0;
          ?>
          <h2 class="font-black text-2xl mt-2">Rp <?= number_format($harian, 2, ",", ".") ?></h2>
        </div>

        <!-- LOW STOCK -->
        <div class="bg-pawshop-stok rounded-lg text-white p-6">
          <div class="flex items-center">
            <h1 class="font-semibold text-lg">STOK</h1>
            <?php
            $sql = "SELECT COUNT(*) AS low_count FROM produk WHERE stok < $minStok";
            $res = $conn->query($sql);
            $c = ($res && $r = $res->fetch_assoc()) ? (int)$r['low_count'] : 0;
            ?>
            <span class="flex ml-2 bg-red-600 items-center justify-center rounded-full border w-6 h-6 text-xs"><?= $c ?></span>
          </div>
          <button data-modal-target="modal-stok" data-modal-toggle="modal-stok" class="font-black text-2xl mt-2 text-start" type="button">
            <?= $c ?> Produk Hampir Habis
          </button>

          <!-- MODAL LOW STOCK LIST -->
          <div id="modal-stok" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full">
              <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                  <h3 class="text-xl font-semibold text-gray-900"><?= $c ?> Produk Hampir Habis</h3>
                  <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="modal-stok">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" /></svg>
                  </button>
                </div>
                <div class="p-4 md:p-5 space-y-4 text-gray-900">
                  <div class="flex flex-col w-full divide-y">
                    <div class="flex w-full py-2 font-bold text-center">
                      <span class="w-1/12">No</span>
                      <span class="w-5/12">Nama Produk</span>
                      <span class="w-3/12">Kategori</span>
                      <span class="w-2/12">Stok</span>
                      <span class="w-1/12">Aksi</span>
                    </div>
                    <?php
                    $lowRes = $conn->query("SELECT p.*, k.name AS kategori_name FROM produk p JOIN kategori k ON p.category_id=k.id WHERE p.stok < $minStok");
                    $num = 1;
                    if ($lowRes && $lowRes->num_rows > 0):
                      while ($row = $lowRes->fetch_assoc()):
                    ?>
                      <div class="flex w-full py-2 items-center text-center text-sm">
                        <span class="w-1/12"><?= $num++ ?></span>
                        <span class="w-5/12 text-start font-medium truncate px-2" title="<?= htmlspecialchars($row['nama_produk']) ?>"><?= htmlspecialchars($row['nama_produk']) ?></span>
                        <span class="w-3/12"><?= htmlspecialchars($row['kategori_name']) ?></span>
                        <span class="w-2/12 font-bold text-red-600"><?= $row['stok'] ?></span>
                        <span class="w-1/12">
                          <a href="<?= BASE_URL ?>admin/products.php" class="text-blue-600 hover:underline"><i class="fa-solid fa-pen-to-square"></i></a>
                        </span>
                      </div>
                    <?php endwhile; else: ?>
                      <p class="py-4 text-center text-gray-500">Semua stok produk mencukupi!</p>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- TRANSACTIONS NOTIFICATION -->
        <div class="bg-pawshop-transaksi rounded-lg text-white p-6">
          <div class="flex items-center">
            <h1 class="font-semibold text-lg">TRANSAKSI</h1>
            <?php
            $sql = "SELECT COUNT(*) AS pending_count FROM transaksi WHERE status_id=1 OR status_id=3";
            $res = $conn->query($sql);
            $cTrans = ($res && $r = $res->fetch_assoc()) ? (int)$r['pending_count'] : 0;
            ?>
            <span class="flex ml-2 bg-red-600 items-center justify-center rounded-full border w-6 h-6 text-xs"><?= $cTrans ?></span>
          </div>
          <button data-modal-target="modal-transaksi" data-modal-toggle="modal-transaksi" class="font-black text-2xl mt-2 text-start" type="button">
            <?= $cTrans ?> Pemberitahuan
          </button>

          <!-- MODAL TRANSAKSI PENDING -->
          <div id="modal-transaksi" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full">
              <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                  <h3 class="text-xl font-semibold text-gray-900"><?= $cTrans ?> Transaksi Membutuhkan Tindakan</h3>
                  <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="modal-transaksi">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" /></svg>
                  </button>
                </div>
                <div class="p-4 md:p-5 space-y-4 text-gray-900">
                  <div class="flex flex-col w-full divide-y">
                    <div class="flex w-full py-2 font-bold text-center">
                      <span class="w-1/12">No</span>
                      <span class="w-4/12">ID Transaksi</span>
                      <span class="w-4/12">Status</span>
                      <span class="w-3/12">Aksi</span>
                    </div>
                    <?php
                    $pendingRes = $conn->query("SELECT t.id, s.name FROM transaksi t JOIN status s ON t.status_id=s.id WHERE status_id=1 OR status_id=3 ORDER BY t.id DESC");
                    $num = 1;
                    if ($pendingRes && $pendingRes->num_rows > 0):
                      while ($row = $pendingRes->fetch_assoc()):
                    ?>
                      <div class="flex w-full py-2 items-center text-center text-sm">
                        <span class="w-1/12"><?= $num++ ?></span>
                        <span class="w-4/12 font-medium"><?= $row['id'] ?></span>
                        <span class="w-4/12"><span class="px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs"><?= htmlspecialchars($row['name']) ?></span></span>
                        <span class="w-3/12">
                          <a href="<?= BASE_URL ?>admin/transactions.php" class="text-blue-600 hover:underline">Kelola &rarr;</a>
                        </span>
                      </div>
                    <?php endwhile; else: ?>
                      <p class="py-4 text-center text-gray-500">Tidak ada transaksi yang tertunda!</p>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- GRAFIK PENJUALAN PER KATEGORI -->
      <div class="bg-pawshop-grafik rounded-lg text-white p-6 mt-6">
        <h1 class="font-semibold text-lg mb-4">TOTAL PENJUALAN PER KATEGORI</h1>
        <div class="space-y-4">
          <?php
          $colors = array("pawshop-gatau", "pawshop-stok", "pawshop-yellow-darker", "pawshop-maurin-pink");
          $c = 0;
          $sql = "SELECT k.name, SUM(p.stok) AS total_stok, COALESCE(total_sold, 0) AS total_sold 
                  FROM produk p 
                  JOIN kategori k ON p.category_id = k.id 
                  LEFT JOIN ( 
                    SELECT p.category_id, SUM(quantity) AS total_sold 
                    FROM transaksi_detail trd 
                    JOIN produk p ON trd.product_id = p.id 
                    GROUP BY p.category_id 
                  ) sold_totals ON k.id = sold_totals.category_id 
                  GROUP BY k.id, k.name";
          $result = $conn->query($sql);
          if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $total_all = (int)$row['total_stok'] + (int)$row['total_sold'];
              $persentase = ($total_all > 0) ? round(($row['total_sold'] / $total_all) * 100) : 0;
              $barColor = $colors[$c % count($colors)];
          ?>
              <div>
                <div class="flex justify-between mb-1 text-sm font-medium">
                  <span><?= htmlspecialchars($row['name']) ?></span>
                  <span><?= $row['total_sold'] ?> terjual dari <?= $total_all ?> stok</span>
                </div>
                <div class="w-full bg-gray-700 rounded-full h-4 overflow-hidden">
                  <div class="bg-<?= $barColor ?> h-4 text-xs font-semibold text-center leading-4 text-white rounded-full transition-all duration-300" style="width: <?= max(5, $persentase) ?>%">
                    <?= $persentase ?>%
                  </div>
                </div>
              </div>
          <?php
              $c++;
            }
          }
          ?>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
