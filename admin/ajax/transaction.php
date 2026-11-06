<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/admin-guard.php';

use App\OrderService;

header('Content-Type: application/json');

$id = isset($_GET['id']) ? trim($_GET['id']) : '';
$data = !empty($id) ? OrderService::getOrderById($id) : null;

if ($data) {
    $data['details'] = OrderService::getOrderDetails($id);
    echo json_encode($data);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Transaksi tidak ditemukan']);
}
