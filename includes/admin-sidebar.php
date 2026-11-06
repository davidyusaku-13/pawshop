<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!-- SIDEBAR -->
<div class="bg-pawshop-kiri text-pawshop-tulisan-kiri min-h-screen basis-1/6 flex flex-col justify-between items-center text-center">
  <div class="mt-8 w-full flex flex-col items-center text-center">
    <a href="<?= BASE_URL ?>index.php">
      <svg class="w-1/2 mx-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
        <path d="M399 384.2C376.9 345.8 335.4 320 288 320H224c-47.4 0-88.9 25.8-111 64.2c35.2 39.2 86.2 63.8 143 63.8s107.8-24.7 143-63.8zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm256 16a72 72 0 1 0 0-144 72 72 0 1 0 0 144z" />
      </svg>
    </a>
    <span class="mt-4 font-bold">ADMIN</span>
  </div>
  <div class="w-full">
    <nav>
      <ul class="flex flex-col text-start">
        <li class="px-8 py-2 hover:bg-pawshop-background <?= ($currentPage == 'index.php') ? 'bg-pawshop-background text-pawshop-kiri font-bold' : '' ?>">
          <a class="flex flex-row justify-evenly items-center" href="<?= BASE_URL ?>admin/index.php">
            <i class="basis-1/4 fa-solid fa-house"></i>
            <span class="basis-3/4">Dasbor</span>
          </a>
        </li>
        <li class="px-8 py-2 hover:bg-pawshop-background <?= ($currentPage == 'products.php') ? 'bg-pawshop-background text-pawshop-kiri font-bold' : '' ?>">
          <a class="flex flex-row justify-evenly items-center" href="<?= BASE_URL ?>admin/products.php">
            <img src="<?= BASE_URL ?>assets/images/brand/paw.png" class="w-4" alt="">
            <span class="basis-3/4">Produk</span>
          </a>
        </li>
        <li class="px-8 py-2 hover:bg-pawshop-background <?= ($currentPage == 'categories.php') ? 'bg-pawshop-background text-pawshop-kiri font-bold' : '' ?>">
          <a class="flex flex-row justify-evenly items-center" href="<?= BASE_URL ?>admin/categories.php">
            <i class="basis-1/4 fa-solid fa-tags"></i>
            <span class="basis-3/4">Kategori</span>
          </a>
        </li>
        <li class="px-8 py-2 hover:bg-pawshop-background <?= ($currentPage == 'statuses.php') ? 'bg-pawshop-background text-pawshop-kiri font-bold' : '' ?>">
          <a class="flex flex-row justify-evenly items-center" href="<?= BASE_URL ?>admin/statuses.php">
            <i class="basis-1/4 fa-solid fa-list-check"></i>
            <span class="basis-3/4">Status</span>
          </a>
        </li>
        <li class="px-8 py-2 hover:bg-pawshop-background <?= ($currentPage == 'transactions.php') ? 'bg-pawshop-background text-pawshop-kiri font-bold' : '' ?>">
          <a class="flex flex-row justify-evenly items-center" href="<?= BASE_URL ?>admin/transactions.php">
            <i class="basis-1/4 fa-solid fa-receipt"></i>
            <span class="basis-3/4">Transaksi</span>
          </a>
        </li>
        <li class="px-8 py-2 hover:bg-pawshop-background <?= ($currentPage == 'users.php') ? 'bg-pawshop-background text-pawshop-kiri font-bold' : '' ?>">
          <a class="flex flex-row justify-evenly items-center" href="<?= BASE_URL ?>admin/users.php">
            <i class="basis-1/4 fa-solid fa-user"></i>
            <span class="basis-3/4">Pengguna</span>
          </a>
        </li>
        <li class="px-8 py-2 hover:bg-pawshop-background <?= ($currentPage == 'reports.php') ? 'bg-pawshop-background text-pawshop-kiri font-bold' : '' ?>">
          <a class="flex flex-row justify-evenly items-center" href="<?= BASE_URL ?>admin/reports.php">
            <i class="basis-1/4 fa-solid fa-chart-simple"></i>
            <span class="basis-3/4">Laporan</span>
          </a>
        </li>
      </ul>
    </nav>
  </div>
  <div class="mb-16">
    <a class="bg-pawshop-gatau px-8 py-2 text-pawshop-kiri font-bold rounded" href="<?= BASE_URL ?>auth/logout.php">LOGOUT</a>
  </div>
</div>
<!-- SIDEBAR -->
