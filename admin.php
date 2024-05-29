<?php

if (!isset($userid)) {
  header('Location: login.php');
}

$updateProdukStatus = '';
$updateTransaksiStatus = '';

if (isset($_POST['transaksi-edit-simpan'])) {
  if ($_POST['transaksi-edit-id'] != '' && $_POST['transaksi-edit-status'] != '') {
    $transID = $_POST['transaksi-edit-id'];
    $status = $_POST['transaksi-edit-status'];

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
    $_POST['stok-edit-id'] != '' && $_POST['stok-edit-nama'] != '' && $_POST['stok-edit-kategori'] != '' && $_POST['stok-edit-stok'] != '' && $_POST['stok-edit-harga'] != '' && $_POST['stok-edit-detail'] != ''
  ) {
    $prodID = $_POST['stok-edit-id'];
    $nama_produk = $_POST['stok-edit-nama'];
    $category_id = $_POST['stok-edit-kategori'];
    $stok = $_POST['stok-edit-stok'];
    $harga = $_POST['stok-edit-harga'];
    $detail = $_POST['stok-edit-detail'];

    if ($_FILES['stok-edit-gambar']['name'] == '') {
      // JIKA TANPA FILE
      $updateProduk = "UPDATE produk SET nama_produk='$nama_produk', category_id='$category_id', stok='$stok', harga='$harga', detail='$detail' WHERE id=$prodID";
    } else {
      // DENGAN UPLOAD FILE BARU
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
          $sql = "SELECT * FROM transaksi";
          $result = $conn->query($sql);
          $pemasukan = 0;
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $pemasukan += $row['total_amount'];
            }
          }
          ?>
          <h2 class="font-black text-2xl">Rp <?= number_format($pemasukan, 2, ",", ".") ?></h2>
        </div>
        <div class="bg-pawshop-gatau rounded-lg text-white pl-8 pr-28 py-8">
          <h1 class="font-semibold text-lg">PEMASUKAN (HARIAN)</h1>
          <?php
          $datenow = date("d-m-Y");
          $sql = "SELECT SUM(total_amount) AS harian FROM transaksi WHERE timestamp='$datenow'";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $harian = $row['harian'];
            }
          }
          ?>
          <h2 class="font-black text-2xl">Rp <?= number_format($harian, 2, ",", ".") ?></h2>
        </div>
        <div class="bg-pawshop-stok rounded-lg text-white pl-8 pr-28 py-8">
          <div class="flex items-center">
            <h1 class="font-semibold text-lg">STOK</h1>
            <?php
            $sql = "SELECT * FROM produk WHERE stok<$minStok";
            $result = $conn->query($sql);
            $c = 0;
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                $c += 1;
              }
            }
            ?>
            <span class="flex ml-2 bg-red-600 items-center justify-center rounded-full border w-6 h-6 text-xs"><?= $c ?></span>
          </div>
          <button data-modal-target="modal-stok" data-modal-toggle="modal-stok" class="font-black text-2xl" type="button">
            <?= $c ?> Produk Hampir Habis
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
                    <?= $c ?> Produk Hampir Habis
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
                    $sql = "SELECT * FROM produk WHERE stok<$minStok";
                    $result = $conn->query($sql);
                    $c = 0;
                    if ($result->num_rows > 0) {
                      $c = 0;
                      while ($row = $result->fetch_assoc()) {
                        $editID = $row['id'];
                        $c += 1;
                    ?>
                        <div class="flex w-full mx-auto justify-between items-start">
                          <p class="basis-1/12 text-center"><?= $c ?></p>
                          <p class="basis-10/12 text-start"><?= $row['nama_produk'] ?></p>
                          <p class="basis-1/12 text-center flex">
                            <span class="basis-1/2">
                              <?= $row['stok'] ?>
                            </span>
                            <span class="basis-1/2">
                              <button data-modal-target="modal-edit-stok-<?= $editID ?>" data-modal-toggle="modal-edit-stok-<?= $editID ?>" data-modal-hide="modal-stok">
                                <i class="fa-regular fa-pen-to-square"></i>
                              </button>
                            </span>
                          </p>
                        </div>
                    <?php
                      }
                    }
                    ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- MODAL STOK -->

          <?php
          $outer = "SELECT * FROM produk WHERE stok<$minStok";
          $resultOuter = $conn->query($outer);
          if ($resultOuter->num_rows > 0) {
            while ($rowOuter = $resultOuter->fetch_assoc()) {
          ?>
              <!-- MODAL EDIT STOK -->
              <!-- Main modal -->
              <div id="modal-edit-stok-<?= $rowOuter['id'] ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-2xl max-h-full">
                  <!-- Modal content -->
                  <div class="relative bg-white rounded-lg shadow">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                      <h3 class="text-xl font-semibold text-gray-900">
                        Edit Produk
                      </h3>
                      <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-target="modal-stok" data-modal-toggle="modal-stok" data-modal-hide="modal-edit-stok-<?= $rowOuter['id'] ?>">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                      </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4 mx-auto text-gray-900">
                      <div class="flex flex-col w-full">
                        <?php
                        $editID = $rowOuter['id'];
                        $sql = "SELECT * FROM produk p JOIN kategori k ON p.category_id=k.id WHERE p.id=$editID";
                        $result = $conn->query($sql);
                        $c = 0;
                        if ($result->num_rows > 0) {
                          $c = 0;
                          while ($row = $result->fetch_assoc()) {
                            $c += 1;
                        ?>
                            <div>
                              <form action="" method="POST" enctype="multipart/form-data" class="flex flex-col w-full mx-auto justify-between items-center">
                                <input type="hidden" name="stok-edit-id" value="<?= $rowOuter['id'] ?>">
                                <div class="flex w-full items-center">
                                  <label class="basis-2/12 font-semibold" for="stok-edit-gambar">Gambar</label>
                                  <span class="basis-1/12">:</span>
                                  <input type="file" name="stok-edit-gambar" class="basis-9/12 w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-pawshop-grafik file:text-pawshop-tulisan-kiri hover:file:bg-pawshop-background">
                                </div>
                                <div class="flex w-full items-center mt-2">
                                  <label class="basis-2/12 font-semibold" for="stok-edit-nama">Nama Produk</label>
                                  <span class="basis-1/12">:</span>
                                  <input required type="text" name="stok-edit-nama" class="basis-9/12 border border-pawshop-grafik rounded px-2 py-2 w-full" value="<?= $row['nama_produk'] ?>">
                                </div>
                                <div class="flex w-full items-center mt-2">
                                  <label class="basis-2/12 font-semibold" for="stok-edit-kategori">Kategori</label>
                                  <span class="basis-1/12">:</span>
                                  <select required name="stok-edit-kategori" class="basis-9/12 border border-pawshop-grafik rounded px-2 py-2 w-full">
                                    <option disabled>Pilih Kategori</option>
                                    <?php
                                    $kategori = "SELECT * FROM kategori";
                                    $resKategori = $conn->query($kategori);
                                    if ($resKategori->num_rows > 0) {
                                      while ($rowKategori = $resKategori->fetch_assoc()) {
                                        if ($row['category_id'] == $rowKategori['id']) {
                                          $select = "selected";
                                        } else {
                                          $select = "";
                                        }
                                    ?>
                                        <option <?= $select ?> value="<?= $rowKategori['id'] ?>"><?= $rowKategori['name'] ?></option>
                                    <?php
                                      }
                                    }
                                    ?>
                                  </select>
                                </div>
                                <div class="flex w-full items-center mt-2">
                                  <label class="basis-2/12 font-semibold" for="stok-edit-stok">Stok</label>
                                  <span class="basis-1/12">:</span>
                                  <input required type="number" min="0" name="stok-edit-stok" class="basis-9/12 border border-pawshop-grafik rounded px-2 py-2 w-full" value="<?= $row['stok'] ?>">
                                </div>
                                <div class="flex w-full items-center mt-2">
                                  <label class="basis-2/12 font-semibold" for="stok-edit-harga">Harga</label>
                                  <span class="basis-1/12">:</span>
                                  <input required type="number" name="stok-edit-harga" class="basis-9/12 border border-pawshop-grafik rounded px-2 py-2 w-full" value="<?= $row['harga'] ?>">
                                </div>
                                <div class="flex w-full items-center mt-2">
                                  <label class="basis-2/12 font-semibold" for="stok-edit-detail">Detail</label>
                                  <span class="basis-1/12">:</span>
                                  <textarea required name="stok-edit-detail" class="basis-9/12 border border-pawshop-grafik rounded px-2 py-2 w-full" cols="30" rows="5"><?= $row['detail'] ?></textarea>
                                </div>
                                <div class="flex justify-end w-full items-center mt-2">
                                  <button class="border rounded p-2 bg-pawshop-grafik text-white font-semibold" type="submit" name="stok-edit-simpan">SIMPAN</button>
                                </div>
                              </form>
                            </div>
                        <?php
                          }
                        }
                        ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- MODAL EDIT STOK -->
          <?php
            }
          }
          ?>

        </div>

        <div class="bg-pawshop-transaksi rounded-lg text-white pl-8 pr-28 py-8">
          <div class="flex items-center">
            <h1 class="font-semibold text-lg">TRANSAKSI</h1>
            <?php
            $sql = "SELECT * FROM transaksi WHERE status_id=1 OR status_id=3";
            $result = $conn->query($sql);
            $c = 0;
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                $c += 1;
              }
            }
            ?>
            <span class="flex ml-2 bg-red-600 items-center justify-center rounded-full border w-6 h-6 text-xs"><?= $c ?></span>
          </div>
          <button data-modal-target="modal-transaksi" data-modal-toggle="modal-transaksi" class="font-black text-2xl" type="button">
            <?= $c ?> Pemberitahuan
          </button>


          <!-- MODAL STOK -->
          <!-- Main modal -->
          <div id="modal-transaksi" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full">
              <!-- Modal content -->
              <div class="relative bg-white rounded-lg shadow">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                  <h3 class="text-xl font-semibold text-gray-900">
                    <?= $c ?> Pemberitahuan
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
                    $sql = "SELECT t.id, s.name FROM transaksi t JOIN status s ON t.status_id=s.id WHERE status_id=1 OR status_id=3";
                    $result = $conn->query($sql);
                    $c = 0;
                    if ($result->num_rows > 0) {
                      $c = 0;
                      while ($row = $result->fetch_assoc()) {
                        $editID = $row['id'];
                        $c += 1;
                    ?>
                        <div class="flex w-full mx-auto justify-between items-start">
                          <p class="basis-1/12 text-center"><?= $c ?></p>
                          <p class="basis-3/12 text-center"><?= $row['id'] ?></p>
                          <p class="basis-8/12 text-center flex">
                            <span class="basis-11/12">
                              <?= $row['name'] ?>
                            </span>
                            <span class="basis-1/12">
                              <button data-modal-target="modal-edit-transaksi-<?= $editID ?>" data-modal-toggle="modal-edit-transaksi-<?= $editID ?>" data-modal-hide="modal-transaksi">
                                <i class="fa-regular fa-pen-to-square"></i>
                              </button>
                            </span>
                          </p>
                        </div>
                    <?php
                      }
                    }
                    ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- MODAL STOK -->

          <?php
          $outer = "SELECT * FROM transaksi WHERE status_id=1 OR status_id=3";
          $resultOuter = $conn->query($outer);
          if ($resultOuter->num_rows > 0) {
            while ($rowOuter = $resultOuter->fetch_assoc()) {
          ?>
              <!-- MODAL EDIT TRANSAKSI -->
              <!-- Main modal -->
              <div id="modal-edit-transaksi-<?= $rowOuter['id'] ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-2xl max-h-full">
                  <!-- Modal content -->
                  <div class="relative bg-white rounded-lg shadow">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                      <h3 class="text-xl font-semibold text-gray-900">
                        Edit Transaksi
                      </h3>
                      <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-target="modal-transaksi" data-modal-toggle="modal-transaksi" data-modal-hide="modal-edit-transaksi-<?= $rowOuter['id'] ?>">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                      </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4 mx-auto text-gray-900">
                      <div class="flex flex-col w-full">
                        <?php
                        $editID = $rowOuter['id'];
                        $sql = "SELECT * FROM transaksi WHERE id='$editID'";
                        $result = $conn->query($sql);
                        $c = 0;
                        if ($result->num_rows > 0) {
                          $c = 0;
                          while ($row = $result->fetch_assoc()) {
                            $c += 1;
                        ?>
                            <div>
                              <form action="" method="POST" enctype="multipart/form-data" class="flex flex-col w-full mx-auto justify-between items-center">
                                <input type="hidden" name="transaksi-edit-id" value="<?= $rowOuter['id'] ?>">
                                <div class="flex w-full items-center mt-2">
                                  <label class="basis-2/12 font-semibold" for="transaksi-edit-status">Status</label>
                                  <span class="basis-1/12">:</span>
                                  <select required name="transaksi-edit-status" class="basis-9/12 border border-pawshop-grafik rounded px-2 py-2 w-full">
                                    <option disabled>Pilih Status</option>
                                    <?php
                                    $status = "SELECT * FROM status";
                                    $resStatus = $conn->query($status);
                                    if ($resStatus->num_rows > 0) {
                                      while ($rowStatus = $resStatus->fetch_assoc()) {
                                        if ($row['category_id'] == $rowStatus['id']) {
                                          $select = "selected";
                                        } else {
                                          $select = "";
                                        }
                                    ?>
                                        <option <?= $select ?> value="<?= $rowStatus['id'] ?>"><?= $rowStatus['name'] ?></option>
                                    <?php
                                      }
                                    }
                                    ?>
                                  </select>
                                </div>
                                <div class="flex justify-end w-full items-center mt-2">
                                  <button class="border rounded p-2 bg-pawshop-grafik text-white font-semibold" type="submit" name="transaksi-edit-simpan">SIMPAN</button>
                                </div>
                              </form>
                            </div>
                        <?php
                          }
                        }
                        ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- MODAL EDIT TRANSAKSI -->
          <?php
            }
          }
          ?>

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
        $c = 0;
        $sql = "SELECT k.name, SUM(p.stok) AS total_stok, COALESCE(total_sold, 0) AS total_sold FROM produk p JOIN kategori k ON p.category_id = k.id LEFT JOIN ( SELECT k.id AS category_id, SUM(quantity) AS total_sold FROM transaksi_detail trd JOIN produk p ON trd.product_id = p.id JOIN kategori k ON p.category_id = k.id GROUP BY k.id ) sold_totals ON k.id = sold_totals.category_id GROUP BY k.id";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            $persentase = $row['total_sold'] / ($row['total_stok'] + $row['total_sold']) * 100;
        ?>
            <div>
              <h1><?= $row['name'] ?></h1>
              <div class="w-full bg-white rounded-full">
                <div class="bg-<?= $colors[$c] ?> text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" style="width: <?= $persentase ?>%">
                  <?= number_format($persentase) ?>%
                </div>
              </div>
              <h1><?= $row['total_sold'] ?> dari <?= $row['total_stok'] + $row['total_sold'] ?></h1>
            </div>
        <?php
            $c++;
          }
        }
        ?>
      </div>
      <!-- GRAFIK -->
    </div>
    <!-- MAIN CONTENT -->
  </div>
  <!-- CONTENT WRAPPER -->
</body>

</html>