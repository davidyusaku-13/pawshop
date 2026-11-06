<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-guard.php';

use App\OrderService;
use App\ProductService;
use App\Database;

$pageTitle = "Dasbor Pengelola";
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

include __DIR__ . '/../includes/admin-header.php';
?>

<?= render_flash() ?>

<!-- METRIC CARDS ROW -->
<div class="row g-3 mb-4">
  <!-- Pemasukan Harian -->
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card bg-stat-blue shadow-sm h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <span class="text-uppercase small fw-bold opacity-75">Pemasukan (Harian)</span>
          <h3 class="fw-bold my-2"><?= format_rupiah($harian) ?></h3>
          <span class="small opacity-75">Per <?= date("d M Y") ?></span>
        </div>
        <div class="p-2 bg-white bg-opacity-25 rounded-circle">
          <i class="fa-solid fa-wallet fs-4"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Pemasukan Bulanan -->
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card bg-stat-coral shadow-sm h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <span class="text-uppercase small fw-bold opacity-75">Pemasukan (Bulanan)</span>
          <h3 class="fw-bold my-2"><?= format_rupiah($bulanan) ?></h3>
          <span class="small opacity-75">Bulan <?= date("F Y") ?></span>
        </div>
        <div class="p-2 bg-white bg-opacity-25 rounded-circle">
          <i class="fa-solid fa-chart-line fs-4"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Stok Menipis -->
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card bg-stat-teal shadow-sm h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <span class="text-uppercase small fw-bold opacity-75">Stok Menipis</span>
          <h3 class="fw-bold my-2">
            <a href="#" class="text-white text-decoration-none" data-bs-toggle="modal" data-bs-target="#lowStockModal">
              <?= $lowStockCount ?> Produk &rarr;
            </a>
          </h3>
          <span class="small opacity-75">Batas minimum &lt; <?= $minStok ?></span>
        </div>
        <div class="p-2 bg-white bg-opacity-25 rounded-circle">
          <i class="fa-solid fa-box-archive fs-4"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Transaksi Pending -->
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card bg-stat-gold shadow-sm h-100">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <span class="text-uppercase small fw-bold opacity-75">Perlu Tindakan</span>
          <h3 class="fw-bold my-2">
            <a href="#" class="text-dark text-decoration-none" data-bs-toggle="modal" data-bs-target="#pendingOrdersModal">
              <?= $pendingCount ?> Pesanan &rarr;
            </a>
          </h3>
          <span class="small opacity-75">Menunggu bukti & konfirmasi</span>
        </div>
        <div class="p-2 bg-dark bg-opacity-10 rounded-circle text-dark">
          <i class="fa-solid fa-bell fs-4"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- SALES PER CATEGORY CHART / PROGRESS -->
<div class="row g-4 mb-4">
  <div class="col-lg-7">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="fa-solid fa-chart-pie me-2 text-primary"></i> Penjualan Berdasarkan Kategori</h6>
        <a href="<?= BASE_URL ?>admin/reports" class="btn btn-sm btn-outline-secondary">Rincian &rarr;</a>
      </div>
      <div class="card-body">
        <?php
        $colors = ['bg-primary', 'bg-success', 'bg-warning text-dark', 'bg-danger', 'bg-info'];
        $colorIdx = 0;
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
            $barColor = $colors[$colorIdx % count($colors)];
            $colorIdx++;
        ?>
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1 small fw-semibold">
              <span><?= htmlspecialchars($cat['name']) ?> (Terjual: <?= (int)$cat['total_sold'] ?> item)</span>
              <span><?= $pct ?>%</span>
            </div>
            <div class="progress" style="height: 10px;">
              <div class="progress-bar <?= $barColor ?>" role="progressbar" style="width: <?= max(4, $pct) ?>%"></div>
            </div>
          </div>
        <?php endforeach; else: ?>
          <p class="text-muted text-center py-4">Belum ada data penjualan kategori.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- QUICK SHORTCUTS & SYSTEM SUMMARY -->
  <div class="col-lg-5">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0"><i class="fa-solid fa-bolt me-2 text-warning"></i> Menu Akses Cepat</h6>
      </div>
      <div class="card-body d-flex flex-column justify-content-between">
        <div class="list-group list-group-flush">
          <a href="<?= BASE_URL ?>admin/products" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
            <div>
              <i class="fa-solid fa-plus-circle text-success me-2"></i>
              <strong>Kelola & Tambah Produk</strong>
              <div class="small text-muted">Update stok, harga, dan foto katalog</div>
            </div>
            <i class="fa-solid fa-chevron-right text-muted small"></i>
          </a>
          <a href="<?= BASE_URL ?>admin/transactions" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
            <div>
              <i class="fa-solid fa-receipt text-primary me-2"></i>
              <strong>Verifikasi Pembayaran</strong>
              <div class="small text-muted">Periksa bukti transfer dan update status</div>
            </div>
            <i class="fa-solid fa-chevron-right text-muted small"></i>
          </a>
          <a href="<?= BASE_URL ?>admin/reports" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
            <div>
              <i class="fa-solid fa-file-csv text-info me-2"></i>
              <strong>Ekspor Laporan Penjualan</strong>
              <div class="small text-muted">Unduh rekap transaksi format CSV</div>
            </div>
            <i class="fa-solid fa-chevron-right text-muted small"></i>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- MODAL LOW STOCK LIST -->
<div class="modal fade" id="lowStockModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold text-danger">
          <i class="fa-solid fa-triangle-exclamation me-2"></i>
          <?= $lowStockCount ?> Produk Hampir Habis (Stok &lt; <?= $minStok ?>)
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-0">
        <?php if (!empty($lowStockProducts)): ?>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Nama Produk</th>
                  <th>Kategori</th>
                  <th class="text-center">Sisa Stok</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($lowStockProducts as $p): ?>
                  <tr>
                    <td class="fw-semibold"><?= htmlspecialchars($p['nama_produk']) ?></td>
                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($p['kategori_name']) ?></span></td>
                    <td class="text-center font-bold text-danger fw-bold"><?= $p['stok'] ?></td>
                    <td class="text-center">
                      <a href="<?= BASE_URL ?>admin/products" class="btn btn-sm btn-outline-primary">Kelola</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <p class="py-4 text-center text-muted mb-0">Semua produk memiliki stok yang cukup.</p>
        <?php endif; ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- MODAL PENDING ORDERS -->
<div class="modal fade" id="pendingOrdersModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold text-warning">
          <i class="fa-solid fa-clock me-2"></i>
          <?= $pendingCount ?> Transaksi Membutuhkan Tindakan
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-0">
        <?php if (!empty($pendingOrders)): ?>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>ID Transaksi</th>
                  <th>Pelanggan</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($pendingOrders as $o): ?>
                  <tr>
                    <td class="font-monospace fw-bold"><?= $o['id'] ?></td>
                    <td><?= htmlspecialchars($o['username']) ?></td>
                    <td class="fw-bold text-primary"><?= format_rupiah($o['total_amount']) ?></td>
                    <td><span class="badge bg-warning text-dark"><?= htmlspecialchars($o['status_name']) ?></span></td>
                    <td class="text-center">
                      <a href="<?= BASE_URL ?>admin/transactions" class="btn btn-sm btn-outline-warning text-dark fw-semibold">Periksa &rarr;</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <p class="py-4 text-center text-muted mb-0">Tidak ada transaksi yang tertunda saat ini.</p>
        <?php endif; ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
