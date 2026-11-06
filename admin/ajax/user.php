<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/admin-guard.php';

use App\AuthService;

header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$data = $id > 0 ? AuthService::getUserById($id) : null;

if ($data) {
    echo json_encode($data);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Pengguna tidak ditemukan']);
}
