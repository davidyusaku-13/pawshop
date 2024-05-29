<?php
include 'config.php';

if (!isset($userid)) {
  header('Location: login.php');
}

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
            <li class="px-8 py-2 hover:bg-pawshop-background">
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
            <li class="px-8 py-2 hover:bg-pawshop-background bg-pawshop-background">
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

        <div class="bg-pawshop-pemasukan rounded-lg text-white w-full px-16 py-8">
          <h1 class="font-bold text-lg">TRANSAKSI</h1>
          <form class="flex flex-col" action="generate.php" method="POST">
            <div class="flex justify-between items-center">
              <label class="basis-6/12">Tanggal Awal</label>
              <input class="basis-6/12 text-black" type="date" name="tanggal-awal" id="">
            </div>
            <div class="mt-2 flex justify-between">
              <label class="basis-6/12">Tanggal Akhir</label>
              <input class="basis-6/12 text-black" type="date" name="tanggal-akhir" id="">
            </div>
            <button class="mt-2 bg-red-500 rounded-lg shadow hover:bg-white hover:text-red-500 hover:shadow" type="submit" name="generate-transaksi">Generate</button>
          </form>
        </div>

        <div class="bg-pawshop-stok rounded-lg text-white px-16 py-8">
          <div class="flex items-center">
            <h1 class="font-bold text-lg">PRODUK</h1>
          </div>
          <form action="generate.php" class="flex justify-center items-center m-auto" method="POST">
            <button class="mt-2 p-6 bg-red-500 rounded-lg shadow hover:bg-white hover:text-red-500 hover:shadow" type="submit" name="generate-produk">Generate</button>
          </form>
        </div>

        <div class="bg-pawshop-transaksi rounded-lg text-white px-16 py-8">
          <div class="flex items-center">
            <h1 class="font-bold text-lg">USER</h1>
          </div>
          <form action="generate.php" class="flex justify-center items-center m-auto" method="POST">
            <button class="mt-2 p-6 bg-red-500 rounded-lg shadow hover:bg-white hover:text-red-500 hover:shadow" type="submit" name="generate-user">Generate</button>
          </form>
        </div>
      </div>
      <!-- CARD -->
    </div>
    <!-- MAIN CONTENT -->
  </div>
  <!-- CONTENT WRAPPER -->
</body>

</html>