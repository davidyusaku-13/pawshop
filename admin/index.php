<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-guard.php';

use App\OrderService;
use App\ProductService;
use App\Database;

$minStok = 20;

$harian = OrderService::getDailyIncome();
$bulanan = OrderService::getMonthlyIncome();

$lowStockProducts = Database::query(
    "SELECT p.*, k.name AS kategori_name FROM produk p JOIN kategori k ON p.category_id = k.id WHERE p.stok < ? ORDER BY p.stok ASC",
    [$minStok]
);
$lowStockCount = count($lowStockProducts);

$pendingOrders = Database::query(
    "SELECT t.id, t.timestamp, t.total_amount, u.username, s.name AS status_name 
     FROM transaksi t 
     JOIN status s ON t.status_id = s.id 
     JOIN users u ON t.user_id = u.id 
     WHERE t.status_id IN (1, 3) 
     ORDER BY t.id DESC"
);
$pendingCount = count($pendingOrders);
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
    body {
      font-family: "Montserrat", sans-serif;
    }
  </style>
  <title>Pawshop | Dasbor Admin</title>
</head>

<body class="bg-pawshop-background min-h-screen">
  <div class="flex">
    <!-- SIDEBAR -->
    <?php include __DIR__ . '/../includes/admin-sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <main class="flex-1 p-8">
      <div class="flex justify-between items-center mb-6">
        <div>
          <h2 class="text-2xl font-black text-gray-800">DASBOR KELOLA</h2>
          <p class="text-sm text-gray-500">Ringkasan aktivitas toko dan penjualan Pawshop</p>
        </div>
        <img src="<?= BASE_URL ?>assets/images/brand/logo.png" class="w-40" alt="Pawshop Logo">
      </div>

      <!-- METRIC CARDS GRID -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- HARIAN -->
        <div class="bg-pawshop-pemasukan rounded-lg text-white p-6 shadow-sm">
          <h3 class="font-semibold text-sm uppercase opacity-90">Pemasukan (Harian)</h3>
          <p class="font-black text-2xl mt-2"><?= format_rupiah($harian) ?></p>
          <span class="text-xs opacity-75 mt-1 block">Per <?= date("d M Y") ?></span>
        </div>

        <!-- BULANAN -->
        <div class="bg-pawshop-gatau rounded-lg text-white p-6 shadow-sm">
          <h3 class="font-semibold text-sm uppercase opacity-90">Pemasukan (Bulanan)</h3>
          <p class="font-black text-2xl mt-2"><?= format_rupiah($bulanan) ?></p>
          <span class="text-xs opacity-75 mt-1 block">Bulan <?= date("F Y") ?></span>
        </div>

        <!-- LOW STOCK -->
        <div class="bg-pawshop-stok rounded-lg text-white p-6 shadow-sm">
          <div class="flex items-center justify-between">
            <h3 class="font-semibold text-sm uppercase opacity-90">Stok Menipis</h3>
            <span class="bg-red-600 rounded-full px-2 py-0.5 text-xs font-bold"><?= $lowStockCount ?></span>
          </div>
          <button data-modal-target="modal-stok" data-modal-toggle="modal-stok" class="font-black text-2xl mt-2 text-start hover:underline block w-full" type="button">
            <?= $lowStockCount ?> Produk
          </button>
          <span class="text-xs opacity-75 mt-1 block">Batas stok minimum: <?= $minStok ?></span>
        </div>

        <!-- TRANSAKSI PENDING -->
        <div class="bg-pawshop-transaksi rounded-lg text-white p-6 shadow-sm">
          <div class="flex items-center justify-between">
            <h3 class="font-semibold text-sm uppercase opacity-90">Transaksi Pending</h3>
            <span class="bg-red-600 rounded-full px-2 py-0.5 text-xs font-bold"><?= $pendingCount ?></span>
          </div>
          <button data-modal-target="modal-transaksi" data-modal-toggle="modal-transaksi" class="font-black text-2xl mt-2 text-start hover:underline block w-full" type="button">
            <?= $pendingCount ?> Perlu Tindakan
          </button>
          <span class="text-xs opacity-75 mt-1 block">Menunggu konfirmasi pembayaran</span>
        </div>
      </div>

      <!-- GRAFIK PENJUALAN PER KATEGORI -->
      <div class="bg-pawshop-grafik rounded-lg text-white p-6 shadow-sm mb-6">
        <h3 class="font-bold text-lg mb-4">PENJUALAN PER KATEGORI</h3>
        <div class="space-y-4">
          <?php
          $colors = ["pawshop-gatau", "pawshop-stok", "pawshop-yellow-darker", "pawshop-maurin-pink"];
          $colorIndex = 0;
          $sql = "SELECT k.name, SUM(p.stok) AS total_stok, COALESCE(sold_totals.total_sold, 0) AS total_sold 
                  FROM kategori k 
                  LEFT JOIN produk p ON k.id = p.category_id 
                  LEFT JOIN ( 
                    SELECT p.category_id, SUM(quantity) AS total_sold 
                    FROM transaksi_detail trd 
                    JOIN produk p ON trd.product_id = p.id 
                    GROUP BY p.category_id 
                  ) sold_totals ON k.id = sold_totals.category_id 
                  GROUP BY k.id, k.name";
          $categories = Database::query($sql);
          if (!empty($categories)):
            foreach ($categories as $cat):
              $totalAll = (int)($cat['total_stok'] ?? 0) + (int)($cat['total_sold'] ?? 0);
              $pct = $totalAll > 0 ? round(($cat['total_sold'] / $totalAll) * 100) : 0;
              $activeColor = $colors[$colorIndex % count($colors)];
              $colorIndex++;
          ?>
            <div>
              <div class="flex justify-between text-sm mb-1">
                <span><?= htmlspecialchars($cat['name']) ?> (Terjual: <?= (int)$cat['total_sold'] ?> item)</span>
                <span class="font-bold"><?= $pct ?>%</span>
              </div>
              <div class="w-full bg-gray-700 rounded-full h-3">
                <div class="bg-<?= $activeColor ?> h-3 rounded-full" style="width: <?= max(5, $pct) ?>%"></div>
              </div>
            </div>
          <?php endforeach; else: ?>
            <p class="text-sm text-gray-400">Belum ada data kategori untuk ditampilkan.</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- MODAL LOW STOCK LIST -->
      <div id="modal-stok" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
          <div class="relative bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-4 border-b rounded-t">
              <h3 class="text-lg font-bold text-gray-900"><?= $lowStockCount ?> Produk Hampir Habis (Stok &lt; <?= $minStok ?>)</h3>
              <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="modal-stok">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" /></svg>
              </button>
            </div>
            <div class="p-4 space-y-2 text-gray-900">
              <?php if (!empty($lowStockProducts)): ?>
                <table class="w-full text-sm text-left border">
                  <thead class="bg-gray-100 text-xs uppercase">
                    <tr>
                      <th class="p-2">Produk</th>
                      <th class="p-2">Kategori</th>
                      <th class="p-2 text-center">Stok</th>
                      <th class="p-2 text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y">
                    <?php foreach ($lowStockProducts as $p): ?>
                      <tr>
                        <td class="p-2 font-medium"><?= htmlspecialchars($p['nama_produk']) ?></td>
                        <td class="p-2 text-gray-500"><?= htmlspecialchars($p['kategori_name']) ?></td>
                        <td class="p-2 text-center font-bold text-red-600"><?= $p['stok'] ?></td>
                        <td class="p-2 text-center">
                          <a href="<?= BASE_URL ?>admin/products.php" class="text-blue-600 hover:underline">Kelola</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              <?php else: ?>
                <p class="py-4 text-center text-gray-500">Semua produk memiliki stok yang cukup.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- MODAL TRANSAKSI PENDING -->
      <div id="modal-transaksi" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
          <div class="relative bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-4 border-b rounded-t">
              <h3 class="text-lg font-bold text-gray-900"><?= $pendingCount ?> Transaksi Perlu Tindakan</h3>
              <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="modal-transaksi">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" /></svg>
              </button>
            </div>
            <div class="p-4 space-y-2 text-gray-900">
              <?php if (!empty($pendingOrders)): ?>
                <table class="w-full text-sm text-left border">
                  <thead class="bg-gray-100 text-xs uppercase">
                    <tr>
                      <th class="p-2">ID Transaksi</th>
                      <th class="p-2">Pelanggan</th>
                      <th class="p-2">Total</th>
                      <th class="p-2">Status</th>
                      <th class="p-2 text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y">
                    <?php foreach ($pendingOrders as $o): ?>
                      <tr>
                        <td class="p-2 font-mono font-medium"><?= $o['id'] ?></td>
                        <td class="p-2"><?= htmlspecialchars($o['username']) ?></td>
                        <td class="p-2"><?= format_rupiah($o['total_amount']) ?></td>
                        <td class="p-2"><span class="px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800"><?= htmlspecialchars($o['status_name']) ?></span></td>
                        <td class="p-2 text-center">
                          <a href="<?= BASE_URL ?>admin/transactions.php" class="text-blue-600 hover:underline">Periksa &rarr;</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              <?php else: ?>
                <p class="py-4 text-center text-gray-500">Tidak ada transaksi yang tertunda saat ini.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
