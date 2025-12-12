<?php
include 'config.php';

// Require admin access
requireAdmin();

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = getPositiveInt('id');

    if ($id > 0) {
        $status = dbFetchOne("SELECT * FROM status WHERE id = ?", 'i', [$id]);

        if ($status) {
            echo json_encode($status);
        } else {
            echo json_encode(['error' => 'Status not found']);
        }
    } else {
        echo json_encode(['error' => 'Invalid status ID']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
