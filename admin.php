<?php

// Require admin access
requireAdmin();

$updateProdukStatus = '';
$updateTransaksiStatus = '';

// Handle transaction status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transaksi-edit-simpan'])) {
  if (csrfValidate()) {
    $transID = post('transaksi-edit-id');
    $status = postPositiveInt('transaksi-edit-status');

    if (!empty($transID) && $status > 0) {
      $result = dbQuery(
        "UPDATE transaksi SET status_id = ? WHERE id = ?",
        'is',
        [$status, $transID]
      );
      if ($result) {
        $updateTransaksiStatus = '<script>window.alert("Berhasil mengubah status!!");</script>';
      } else {
        $updateTransaksiStatus = '<script>window.alert("Gagal mengubah status!!");</script>';
      }
    }
  }
}

// Handle product stock update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['stok-edit-simpan'])) {
  if (csrfValidate()) {
    $prodID = postPositiveInt('stok-edit-id');
    $nama_produk = post('stok-edit-nama');
    $category_id = postPositiveInt('stok-edit-kategori');
    $stok = postPositiveInt('stok-edit-stok');
    $harga = postPositiveInt('stok-edit-harga');
    $detail = post('stok-edit-detail');

    if ($prodID > 0 && !empty($nama_produk) && $category_id > 0) {
      // Handle file upload if present
      if (isset($_FILES['stok-edit-gambar']) && $_FILES['stok-edit-gambar']['name'] != '') {
        $errors = validateImageUpload($_FILES['stok-edit-gambar']);
        if (empty($errors)) {
          $newFilename = generateSafeFilename($_FILES['stok-edit-gambar']['name']);
          $targetPath = UPLOAD_DIR . $newFilename;
          if (move_uploaded_file($_FILES['stok-edit-gambar']['tmp_name'], $targetPath)) {
            dbQuery(
              "UPDATE produk SET gambar = ?, nama_produk = ?, category_id = ?, stok = ?, harga = ?, detail = ? WHERE id = ?",
              'ssiiisi',
              [$newFilename, $nama_produk, $category_id, $stok, $harga, $detail, $prodID]
            );
            $updateProdukStatus = '<script>window.alert("Berhasil mengubah produk!!");</script>';
          }
        } else {
          $updateProdukStatus = '<script>window.alert("' . addslashes(implode(', ', $errors)) . '");</script>';
        }
      } else {
        // Update without changing image
        $result = dbQuery(
          "UPDATE produk SET nama_produk = ?, category_id = ?, stok = ?, harga = ?, detail = ? WHERE id = ?",
          'siiisi',
          [$nama_produk, $category_id, $stok, $harga, $detail, $prodID]
        );
        if ($result) {
          $updateProdukStatus = '<script>window.alert("Berhasil mengubah produk!!");</script>';
        } else {
          $updateProdukStatus = '<script>window.alert("Gagal mengubah produk!!");</script>';
        }
      }
    }
  }
}

$minStok = MIN_STOCK_ALERT;

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
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
  <link rel="shortcut icon" href="./logo-title.png" type="image/x-icon" />
  <style>
    body {
      font-family: "Montserrat", sans-serif;
    }
  </style>
  <title>Pawshop | Dasbor</title>
</head>

<body class="bg-pawshop-background">
  <?= $updateTransaksiStatus ?>
  <?= $updateProdukStatus ?>
  <!-- CONTENT WRAPPER -->
  <div class="flex">

    <!-- SIDEBAR -->
    <div class="bg-pawshop-kiri text-pawshop-tulisan-kiri h-svh basis-1/6 flex flex-col justify-between items-center text-center">
      <div class="mt-8 w-full flex flex-col items-center text-center">
        <a href="./index.php">
          <svg class="w-1/2 mx-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
            <path d="M399 384.2C376.9 345.8 335.4 320 288 320H224c-47.4 0-88.9 25.8-111 64.2c35.2 39.2 86.2 63.8 143 63.8s107.8-24.7 143-63.8zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm256 16a72 72 0 1 0 0-144 72 72 0 1 0 0 144z" />
          </svg>
        </a>
        <span class="mt-4 font-bold">ADMIN</span>
      </div>
      <div class="w-full">
        <nav>
          <ul class="flex flex-col text-start">
            <li class="px-8 py-2 hover:bg-pawshop-background bg-pawshop-background">
              <a class="flex flex-row justify-evenly items-center" href="./index.php">
                <i class="basis1/4 fa-solid fa-house"></i>
                <span class="basis-3/4">Dasbor</span>
              </a>
            </li>
            <li class="px-8 py-2 hover:bg-pawshop-background">
              <a class="flex flex-row justify-evenly items-center" href="./admin-produk.php">
                <img src="./paw.png" class="w-4" alt="">
                <span class="basis-3/4">Produk</span>
              </a>
            </li>
            <li class="px-8 py-2 hover:bg-pawshop-background">
              <a class="flex flex-row justify-evenly items-center" href="./admin-users.php">
                <i class="basis1/4 fa-solid fa-user"></i>
                <span class="basis-3/4">Pengguna</span>
              </a>
            </li>
            <li class="px-8 py-2 hover:bg-pawshop-background">
              <a class="flex flex-row justify-evenly items-center" href="./admin-report.php">
                <i class="basis1/4 fa-solid fa-chart-simple"></i>
                <span class="basis-3/4">Laporan</span>
              </a>
            </li>
          </ul>
        </nav>
      </div>
      <div class="mb-16">
        <a class="bg-pawshop-gatau px-8 py-2 text-pawshop-kiri font-bold" href="./logout.php">LOGOUT</a>
      </div>
    </div>
    <!-- SIDEBAR -->

    <!-- MAIN CONTENT -->
    <div class="m-auto">
      <!-- LOGO -->
      <div class="flex justify-end mb-3">
        <img src="./logo.png" class="w-48" alt="">
      </div>
      <!-- LOGO -->
      <!-- CARD -->
      <div class="basis-5/6 grid grid-cols-2 gap-4">
        <div class="bg-pawshop-pemasukan rounded-lg text-white pl-8 pr-28 py-8">
          <h1 class="font-semibold text-lg">PEMASUKAN (TOTAL)</h1>
          <?php
          $result = dbFetchAll("SELECT total_amount FROM transaksi");
          $pemasukan = 0;
          foreach ($result as $row) {
            $pemasukan += $row['total_amount'];
          }
          ?>
          <h2 class="font-black text-2xl">Rp <?= e(number_format($pemasukan, 2, ",", ".")) ?></h2>
        </div>
        <div class="bg-pawshop-gatau rounded-lg text-white pl-8 pr-28 py-8">
          <h1 class="font-semibold text-lg">PEMASUKAN (HARIAN)</h1>
          <?php
          $datenow = date("d-m-Y");
          $result = dbFetchOne("SELECT SUM(total_amount) AS harian FROM transaksi WHERE timestamp = ?", 's', [$datenow]);
          $harian = $result['harian'] ?? 0;
          ?>
          <h2 class="font-black text-2xl">Rp <?= e(number_format($harian, 2, ",", ".")) ?></h2>
        </div>
        <div class="bg-pawshop-stok rounded-lg text-white pl-8 pr-28 py-8">
          <div class="flex items-center">
            <h1 class="font-semibold text-lg">STOK</h1>
            <?php
            $lowStockProducts = dbFetchAll("SELECT id, nama_produk, stok FROM produk WHERE stok < ?", 'i', [$minStok]);
            $lowStockCount = count($lowStockProducts);
            ?>
            <span class="flex ml-2 bg-red-600 items-center justify-center rounded-full border w-6 h-6 text-xs"><?= e($lowStockCount) ?></span>
          </div>
          <button data-modal-target="modal-stok" data-modal-toggle="modal-stok" class="font-black text-2xl" type="button">
            <?= e($lowStockCount) ?> Produk Hampir Habis
          </button>

          <!-- MODAL STOK -->
          <!-- Main modal -->
          <div id="modal-stok" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full">
              <!-- Modal content -->
              <div class="relative bg-white rounded-lg shadow">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                  <h3 class="text-xl font-semibold text-gray-900">
                    <?= e($lowStockCount) ?> Produk Hampir Habis
                  </h3>
                  <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="modal-stok">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                  </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5 space-y-4 mx-auto text-gray-900">
                  <div class="flex flex-col w-full">
                    <div class="flex w-full mx-auto justify-between items-center">
                      <h1 class="font-bold basis-1/12 text-center">No</h1>
                      <h1 class="font-bold basis-10/12 text-center">Nama Produk</h1>
                      <h1 class="font-bold basis-1/12 text-center">Stok</h1>
                    </div>
                    <?php
                    $rowNum = 0;
                    foreach ($lowStockProducts as $row):
                      $editID = (int)$row['id'];
                      $rowNum++;
                    ?>
                        <div class="flex w-full mx-auto justify-between items-start">
                          <p class="basis-1/12 text-center"><?= e($rowNum) ?></p>
                          <p class="basis-10/12 text-start"><?= e($row['nama_produk']) ?></p>
                          <p class="basis-1/12 text-center flex">
                            <span class="basis-1/2">
                              <?= e($row['stok']) ?>
                            </span>
                            <span class="basis-1/2">
                              <button data-modal-target="modal-edit-stok-<?= e($editID) ?>" data-modal-toggle="modal-edit-stok-<?= e($editID) ?>" data-modal-hide="modal-stok">
                                <i class="fa-regular fa-pen-to-square"></i>
                              </button>
                            </span>
                          </p>
                        </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- MODAL STOK -->

          <?php
          // Use already-fetched low stock products for edit modals
          foreach ($lowStockProducts as $rowOuter):
            $editID = (int)$rowOuter['id'];
            // Fetch full product data with category
            $productData = dbFetchOne(
              "SELECT p.*, k.name as kategori_name FROM produk p JOIN kategori k ON p.category_id = k.id WHERE p.id = ?",
              'i',
              [$editID]
            );
            $allCategories = dbFetchAll("SELECT id, name FROM kategori");
          ?>
              <!-- MODAL EDIT STOK -->
              <!-- Main modal -->
              <div id="modal-edit-stok-<?= e($editID) ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-2xl max-h-full">
                  <!-- Modal content -->
                  <div class="relative bg-white rounded-lg shadow">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                      <h3 class="text-xl font-semibold text-gray-900">
                        Edit Produk
                      </h3>
                      <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-target="modal-stok" data-modal-toggle="modal-stok" data-modal-hide="modal-edit-stok-<?= e($editID) ?>">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                      </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4 mx-auto text-gray-900">
                      <div class="flex flex-col w-full">
                        <?php if ($productData): ?>
                            <div>
                              <form action="" method="POST" enctype="multipart/form-data" class="flex flex-col w-full mx-auto justify-between items-center">
                                <?= csrfField() ?>
                                <input type="hidden" name="stok-edit-id" value="<?= e($editID) ?>">
                                <div class="flex w-full items-center">
                                  <label class="basis-2/12 font-semibold" for="stok-edit-gambar">Gambar</label>
                                  <span class="basis-1/12">:</span>
                                  <input type="file" name="stok-edit-gambar" class="basis-9/12 w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-pawshop-grafik file:text-pawshop-tulisan-kiri hover:file:bg-pawshop-background">
                                </div>
                                <div class="flex w-full items-center mt-2">
                                  <label class="basis-2/12 font-semibold" for="stok-edit-nama">Nama Produk</label>
                                  <span class="basis-1/12">:</span>
                                  <input required type="text" name="stok-edit-nama" class="basis-9/12 border border-pawshop-grafik rounded px-2 py-2 w-full" value="<?= e($productData['nama_produk']) ?>">
                                </div>
                                <div class="flex w-full items-center mt-2">
                                  <label class="basis-2/12 font-semibold" for="stok-edit-kategori">Kategori</label>
                                  <span class="basis-1/12">:</span>
                                  <select required name="stok-edit-kategori" class="basis-9/12 border border-pawshop-grafik rounded px-2 py-2 w-full">
                                    <option disabled>Pilih Kategori</option>
                                    <?php foreach ($allCategories as $cat): ?>
                                        <option <?= $productData['category_id'] == $cat['id'] ? 'selected' : '' ?> value="<?= e($cat['id']) ?>"><?= e($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                  </select>
                                </div>
                                <div class="flex w-full items-center mt-2">
                                  <label class="basis-2/12 font-semibold" for="stok-edit-stok">Stok</label>
                                  <span class="basis-1/12">:</span>
                                  <input required type="number" min="0" name="stok-edit-stok" class="basis-9/12 border border-pawshop-grafik rounded px-2 py-2 w-full" value="<?= e($productData['stok']) ?>">
                                </div>
                                <div class="flex w-full items-center mt-2">
                                  <label class="basis-2/12 font-semibold" for="stok-edit-harga">Harga</label>
                                  <span class="basis-1/12">:</span>
                                  <input required type="number" name="stok-edit-harga" class="basis-9/12 border border-pawshop-grafik rounded px-2 py-2 w-full" value="<?= e($productData['harga']) ?>">
                                </div>
                                <div class="flex w-full items-center mt-2">
                                  <label class="basis-2/12 font-semibold" for="stok-edit-detail">Detail</label>
                                  <span class="basis-1/12">:</span>
                                  <textarea required name="stok-edit-detail" class="basis-9/12 border border-pawshop-grafik rounded px-2 py-2 w-full" cols="30" rows="5"><?= e($productData['detail']) ?></textarea>
                                </div>
                                <div class="flex justify-end w-full items-center mt-2">
                                  <button class="border rounded p-2 bg-pawshop-grafik text-white font-semibold" type="submit" name="stok-edit-simpan">SIMPAN</button>
                                </div>
                              </form>
                            </div>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- MODAL EDIT STOK -->
          <?php endforeach; ?>

        </div>

        <div class="bg-pawshop-transaksi rounded-lg text-white pl-8 pr-28 py-8">
          <div class="flex items-center">
            <h1 class="font-semibold text-lg">TRANSAKSI</h1>
            <?php
            $pendingTransactions = dbFetchAll(
              "SELECT t.id, s.name as status_name, t.status_id FROM transaksi t JOIN status s ON t.status_id = s.id WHERE t.status_id IN (1, 3)"
            );
            $pendingCount = count($pendingTransactions);
            ?>
            <span class="flex ml-2 bg-red-600 items-center justify-center rounded-full border w-6 h-6 text-xs"><?= e($pendingCount) ?></span>
          </div>
          <button data-modal-target="modal-transaksi" data-modal-toggle="modal-transaksi" class="font-black text-2xl" type="button">
            <?= e($pendingCount) ?> Pemberitahuan
          </button>


          <!-- MODAL TRANSAKSI -->
          <!-- Main modal -->
          <div id="modal-transaksi" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full">
              <!-- Modal content -->
              <div class="relative bg-white rounded-lg shadow">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                  <h3 class="text-xl font-semibold text-gray-900">
                    <?= e($pendingCount) ?> Pemberitahuan
                  </h3>
                  <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="modal-transaksi">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                  </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5 space-y-4 mx-auto text-gray-900">
                  <div class="flex flex-col w-full">
                    <div class="flex w-full mx-auto justify-between items-center">
                      <h1 class="font-bold basis-1/12 text-center">No</h1>
                      <h1 class="font-bold basis-3/12 text-center">Transaksi</h1>
                      <h1 class="font-bold basis-8/12 text-center">Status</h1>
                    </div>
                    <?php
                    $rowNum = 0;
                    foreach ($pendingTransactions as $trans):
                      $transEditID = (int)$trans['id'];
                      $rowNum++;
                    ?>
                        <div class="flex w-full mx-auto justify-between items-start">
                          <p class="basis-1/12 text-center"><?= e($rowNum) ?></p>
                          <p class="basis-3/12 text-center"><?= e($trans['id']) ?></p>
                          <p class="basis-8/12 text-center flex">
                            <span class="basis-11/12">
                              <?= e($trans['status_name']) ?>
                            </span>
                            <span class="basis-1/12">
                              <button data-modal-target="modal-edit-transaksi-<?= e($transEditID) ?>" data-modal-toggle="modal-edit-transaksi-<?= e($transEditID) ?>" data-modal-hide="modal-transaksi">
                                <i class="fa-regular fa-pen-to-square"></i>
                              </button>
                            </span>
                          </p>
                        </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- MODAL TRANSAKSI -->

          <?php
          // Fetch all statuses once for the edit modals
          $allStatuses = dbFetchAll("SELECT id, name FROM status");
          foreach ($pendingTransactions as $transOuter):
            $transEditID = (int)$transOuter['id'];
            $transData = dbFetchOne("SELECT * FROM transaksi WHERE id = ?", 's', [$transEditID]);
          ?>
              <!-- MODAL EDIT TRANSAKSI -->
              <!-- Main modal -->
              <div id="modal-edit-transaksi-<?= e($transEditID) ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-2xl max-h-full">
                  <!-- Modal content -->
                  <div class="relative bg-white rounded-lg shadow">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                      <h3 class="text-xl font-semibold text-gray-900">
                        Edit Transaksi
                      </h3>
                      <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-target="modal-transaksi" data-modal-toggle="modal-transaksi" data-modal-hide="modal-edit-transaksi-<?= e($transEditID) ?>">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                      </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4 mx-auto text-gray-900">
                      <div class="flex flex-col w-full">
                        <?php if ($transData): ?>
                            <div>
                              <form action="" method="POST" enctype="multipart/form-data" class="flex flex-col w-full mx-auto justify-between items-center">
                                <?= csrfField() ?>
                                <input type="hidden" name="transaksi-edit-id" value="<?= e($transEditID) ?>">
                                <div class="flex w-full items-center mt-2">
                                  <label class="basis-2/12 font-semibold" for="transaksi-edit-status">Status</label>
                                  <span class="basis-1/12">:</span>
                                  <select required name="transaksi-edit-status" class="basis-9/12 border border-pawshop-grafik rounded px-2 py-2 w-full">
                                    <option disabled>Pilih Status</option>
                                    <?php foreach ($allStatuses as $stat): ?>
                                        <option <?= $transData['status_id'] == $stat['id'] ? 'selected' : '' ?> value="<?= e($stat['id']) ?>"><?= e($stat['name']) ?></option>
                                    <?php endforeach; ?>
                                  </select>
                                </div>
                                <div class="flex justify-end w-full items-center mt-2">
                                  <button class="border rounded p-2 bg-pawshop-grafik text-white font-semibold" type="submit" name="transaksi-edit-simpan">SIMPAN</button>
                                </div>
                              </form>
                            </div>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- MODAL EDIT TRANSAKSI -->
          <?php endforeach; ?>

        </div>
      </div>
      <!-- CARD -->

      <!-- GRAFIK -->
      <div class="mt-4 bg-pawshop-grafik text-white rounded-lg p-6">
        <div>
          <h1 class="font-bold text-xl mb-2">GRAFIK PENJUALAN (PER KATEGORI)</h1>
        </div>
        <?php
        $colors = array("pawshop-gatau", "pawshop-stok", "pawshop-yellow-darker", "pawshop-maurin-pink");
        $colorIndex = 0;
        $salesData = dbFetchAll(
          "SELECT k.name, SUM(p.stok) AS total_stok, COALESCE(total_sold, 0) AS total_sold
           FROM produk p
           JOIN kategori k ON p.category_id = k.id
           LEFT JOIN (
             SELECT k.id AS category_id, SUM(quantity) AS total_sold
             FROM transaksi_detail trd
             JOIN produk p ON trd.product_id = p.id
             JOIN kategori k ON p.category_id = k.id
             GROUP BY k.id
           ) sold_totals ON k.id = sold_totals.category_id
           GROUP BY k.id"
        );
        foreach ($salesData as $row):
          $totalAvailable = $row['total_stok'] + $row['total_sold'];
          $persentase = $totalAvailable > 0 ? ($row['total_sold'] / $totalAvailable * 100) : 0;
        ?>
            <div>
              <h1><?= e($row['name']) ?></h1>
              <div class="w-full bg-white rounded-full">
                <div class="bg-<?= e($colors[$colorIndex % count($colors)]) ?> text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" style="width: <?= e(number_format($persentase, 0)) ?>%">
                  <?= e(number_format($persentase)) ?>%
                </div>
              </div>
              <h1><?= e($row['total_sold']) ?> dari <?= e($totalAvailable) ?></h1>
            </div>
        <?php
          $colorIndex++;
        endforeach;
        ?>
      </div>
      <!-- GRAFIK -->
    </div>
    <!-- MAIN CONTENT -->
  </div>
  <!-- CONTENT WRAPPER -->
</body>

</html>