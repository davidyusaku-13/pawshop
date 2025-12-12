<?php
include 'config.php';

// Require admin access
requireAdmin();

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = getPositiveInt('id');

    if ($id > 0) {
        // Only return safe fields - never return password hash
        $user = dbFetchOne(
            "SELECT id, username, phone_number, privilege FROM users WHERE id = ?",
            'i',
            [$id]
        );

        if ($user) {
            echo json_encode($user);
        } else {
            echo json_encode(['error' => 'User not found']);
        }
    } else {
        echo json_encode(['error' => 'Invalid user ID']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
