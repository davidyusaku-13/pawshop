<?php
include 'config.php';

// Require admin access
requireAdmin();

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = getPositiveInt('id');

    if ($id > 0) {
        $transaction = dbFetchOne("SELECT * FROM transaksi WHERE id = ?", 'i', [$id]);

        if ($transaction) {
            echo json_encode($transaction);
        } else {
            echo json_encode(['error' => 'Transaction not found']);
        }
    } else {
        echo json_encode(['error' => 'Invalid transaction ID']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
