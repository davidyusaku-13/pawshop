<?php
include 'config.php';

// Require admin access
requireAdmin();

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = getPositiveInt('id');

    if ($id > 0) {
        $product = dbFetchOne("SELECT * FROM produk WHERE id = ?", 'i', [$id]);

        if ($product) {
            echo json_encode($product);
        } else {
            echo json_encode(['error' => 'Product not found']);
        }
    } else {
        echo json_encode(['error' => 'Invalid product ID']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
