<?php
session_start();
include 'config.php';

if ($_SESSION['privilege'] != 'admin') {
  header('Location: index.php');
}

$status = '';

if (isset($_POST['submit'])) {
  if (isset($_POST['category_name']) && $_POST['category_name'] != null) {
    $category_name = $_POST['category_name'];
    $sql = "INSERT INTO categories (name) VALUES ('$category_name')";
    if (mysqli_query($conn, $sql)) {
      $status = '<div class="mt-3 alert alert-success" role="alert">New category has been added!</div>';
    } else {
      $status = '<div class="mt-3 alert alert-danger" role="alert">Failed to add category!</div>';
    }
  }
  if (isset($_POST['edit_category_name']) && $_POST['edit_category_name'] != null) {
    $id = $_POST['id'];
    $edit_category_name = $_POST['edit_category_name'];
    $sql = "UPDATE categories SET name='$edit_category_name' WHERE id=$id";
    $status = "";
    if (mysqli_query($conn, $sql)) {
      $status = '<div class="mt-3 alert alert-success" role="alert">Edit success!</div>';
    } else {
      $status = '<div class="mt-3 alert alert-danger" role="alert">Failed to edit category!</div>';
    }
  }
  if (isset($_POST['delete_category_name']) && $_POST['delete_category_name'] != null) {
    $id = $_POST['id'];
    $sql = "DELETE FROM categories WHERE id=$id";
    $status = "";
    if (mysqli_query($conn, $sql)) {
      $status = '<div class="mt-3 alert alert-success" role="alert">Category has been deleted!</div>';
    } else {
      $status = '<div class="mt-3 alert alert-danger" role="alert">Failed to delete category!</div>';
    }
  }
}

?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pawshop | Kategori</title>
  <link rel="icon" type="image/x-icon" href="./logo-title.png">
  <link href="./css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link rel="stylesheet" href="./css/datatables.min.css">
  <script src="./js/datatables.min.js"></script>
  <script src="./js/jquery-3.7.1.min.js"></script>
  <script src="https://kit.fontawesome.com/ec712a3d01.js" crossorigin="anonymous"></script>
</head>

<body>
  <!-- Start Navbar -->
  <nav class="navbar navbar-expand-sm navbar-dark bg-dark sticky-top">
    <div class="container-fluid">
      <a class="navbar-brand" href="./index.php">
        <img src="./logo.png" height="30" alt="">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mynavbar">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link" href="./admin-transaksi.php">Transaksi</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./admin-produk.php">Produk</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="./admin-kategori.php">Kategori</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./admin-status.php">Status</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./admin-users.php">Users</a>
          </li>
          <?php
          if (isset($_SESSION['userid']) && $_SESSION['userid'] != null) {
          ?>

            <li class="nav-item">
              <a class="nav-link" href="./logout.php">Logout</a>
            </li>
          <?php
          } else {
          ?>
            <li class="nav-item">
              <a class="nav-link" href="./login.php">Login</a>
            </li>
          <?php
          }
          ?>
        </ul>
      </div>
    </div>
  </nav>
  <!-- End Navbar -->

  <!-- Start Content -->
  <div class="container mt-3">

    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newCategory">Tambah</button>
    <?= $status; ?>
    <!-- Start MODAL TAMBAH KATEGORI -->
    <div class="modal" id="newCategory">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Tambah Kategori</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form action="" method="POST">
              <input class="form-control" type="text" name="category_name" placeholder="Nama Kategori">
              <div class="d-flex justify-content-end">
                <input type="submit" name="submit" class="mt-2 btn btn-primary"></input>
              </div>
            </form>
          </div>

        </div>
      </div>
    </div>
    <!-- End MODAL TAMBAH KATEGORI -->

    <!-- Start MODAL EDIT -->
    <div class="modal" id="editCategory">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Edit Kategori</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form action="" method="POST">
              <input type="hidden" name="id" value="">
              <input class="form-control" type="text" name="edit_category_name" value="">
              <div class="d-flex justify-content-end">
                <input type="submit" name="submit" class="mt-2 btn btn-primary"></input>
              </div>
            </form>
          </div>

        </div>
      </div>
    </div>
    <!-- End MODAL EDIT -->

    <!-- Start MODAL DELETE -->
    <div class="modal" id="deleteCategory">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <p></p>
          </div>
          <div class="modal-body">
            <form class="d-flex justify-content-between" action="" method="POST">
              <input type="hidden" name="id" value="">
              <input type="hidden" name="delete_category_name">
              <input type="submit" name="submit" value="Yes" class="mt-2 btn btn-danger"></input>
              <button data-bs-dismiss="modal" class="mt-2 btn btn-primary">No</button>
            </form>
          </div>

        </div>
      </div>
    </div>
    <!-- End MODAL DELETE -->

    <!-- Start Table -->
    <div class="mt-3 table-responsive">
      <table id="categoriesDataTables" class="table display">
        <thead>
          <tr>
            <th>Aksi</th>
            <th>ID</th>
            <th>Kategori</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $fetch = "SELECT * FROM kategori";
          $res = mysqli_query($conn, $fetch);

          if (mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_assoc($res)) {
          ?>
              <tr>
                <td>
                  <button class="btn" data-bs-toggle="modal" data-id="<?= $row['id']; ?>" data-bs-target="#editCategory"><i class="fa-solid fa-edit"></i></button>
                  <button class="btn" data-bs-toggle="modal" data-id="<?= $row['id']; ?>" data-bs-target="#deleteCategory"><i class="fa-solid fa-trash"></i></button>
                </td>
                <td><?= $row['id']; ?></td>
                <td><?= $row['name']; ?></td>
              </tr>
          <?php
            }
          }
          ?>
        </tbody>
        <tfoot>
          <tr>
            <th>Aksi</th>
            <th>ID</th>
            <th>Kategori</th>
          </tr>
        </tfoot>
      </table>
    </div>
    <!-- End Table -->
  </div>
  <!-- End Content -->


  <!-- Start Footer -->
  <footer class="footer mt-auto py-3 bg-body-tertiary">
    <div class="container">
      <span class="text-body-secondary">
        &copy; 2023 Pawshop, Inc. &middot; <a href="#">Privacy</a> &middot; <a href="#">Terms</a>
      </span>
    </div>
  </footer>
  <!-- End Footer -->

  <script src="./js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="./js/jquery.dataTables.min.js"></script>
  <script>
    new DataTable('#categoriesDataTables');

    // JavaScript to handle the modal show event
    $('#editCategory').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var categoryId = button.data('id');

      // Fetch data using AJAX
      $.ajax({
        url: 'admin-kategori-fetch.php',
        type: 'GET',
        data: {
          id: categoryId
        },
        dataType: 'json',
        success: function(data) {
          // Update the modal content with the fetched data
          if (!data.error) {
            $('#editCategory input[name="id"]').val(data.id);
            $('#editCategory input[name="edit_category_name"]').val(data.name);
            // You can update other modal fields as needed
          } else {
            // Handle errors
            console.log('Error fetching category:', data.error);
          }
        },
        error: function(xhr, status, error) {
          // Handle AJAX errors
          console.error('AJAX error:', status, error);
        }
      });
    });

    // JavaScript to handle the modal show event
    $('#deleteCategory').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var categoryId = button.data('id');

      // Fetch data using AJAX
      $.ajax({
        url: 'admin-kategori-fetch.php',
        type: 'GET',
        data: {
          id: categoryId
        },
        dataType: 'json',
        success: function(data) {
          // Update the modal content with the fetched data
          if (!data.error) {
            // Set the text of the paragraph element in the modal header
            $('#deleteCategory .modal-header p').text('Apakah anda yakin ingin menghapus ' + data.name + '?');

            $('#deleteCategory input[name="id"]').val(data.id);
            $('#deleteCategory input[name="delete_category_name"]').val(data.name);
            // You can update other modal fields as needed
          } else {
            // Handle errors
            console.log('Error fetching category:', data.error);
          }
        },
        error: function(xhr, status, error) {
          // Handle AJAX errors
          console.error('AJAX error:', status, error);
        }
      });
    });
  </script>
</body>

</html>