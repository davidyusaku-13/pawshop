<?php
include 'config.php';

// Require admin access
requireAdmin();

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = getPositiveInt('id');

    if ($id > 0) {
        $category = dbFetchOne("SELECT * FROM kategori WHERE id = ?", 'i', [$id]);

        if ($category) {
            echo json_encode($category);
        } else {
            echo json_encode(['error' => 'Category not found']);
        }
    } else {
        echo json_encode(['error' => 'Invalid category ID']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
