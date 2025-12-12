<?php
include 'config.php';

// Require login
requireLogin();

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Validate CSRF token
if (!csrfValidate()) {
    $_SESSION['transaction_error'] = 'Sesi tidak valid. Silakan coba lagi.';
    header('Location: index.php');
    exit;
}

// Check if cart exists and has items
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit;
}

// Validate payment method
$payment_method = post('payment_method');
if (!validatePaymentMethod($payment_method)) {
    $_SESSION['transaction_error'] = 'Metode pembayaran tidak valid.';
    header('Location: index.php');
    exit;
}

// Get user ID from session (not POST - security fix)
$user_id = $userid;

if (!$user_id) {
    header('Location: login.php');
    exit;
}

// Process checkout with transaction
$success = dbTransaction(function ($conn) use ($user_id, $payment_method) {
    $cart = $_SESSION['cart'];
    $total = 0;
    $validatedItems = [];

    // Generate transaction ID
    $transID = "TRS" . date("dmyHis");
    $transDate = date("d-m-Y");
    $expiry_date = strtotime('+2 days');
    $expiry_formatted = date("dmyHis", $expiry_date);

    // Validate stock and fetch DB prices for each item (with row locking)
    foreach ($cart as $item) {
        $product_id = sanitizeInt($item['id']);
        $quantity = sanitizePositiveInt($item['quantity']);

        if ($product_id <= 0 || $quantity <= 0) {
            throw new Exception('Data keranjang tidak valid');
        }

        // Lock the row and fetch stock + price atomically
        $stmt = $conn->prepare("SELECT stok, harga, nama_produk FROM produk WHERE id = ? FOR UPDATE");
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if (!$product) {
            throw new Exception('Produk tidak ditemukan');
        }

        if ($product['stok'] < $quantity) {
            throw new Exception('Stok tidak mencukupi untuk: ' . e($product['nama_produk']));
        }

        // Use DB-verified price
        $db_unit_price = (int) $product['harga'];
        $subtotal = $quantity * $db_unit_price;

        // Store validated item data for later use
        $validatedItems[] = [
            'product_id' => $product_id,
            'quantity' => $quantity,
            'unit_price' => $db_unit_price,
            'subtotal' => $subtotal,
            'current_stock' => (int) $product['stok']
        ];

        $total += $subtotal;
    }

    // Update stock for all validated items
    foreach ($validatedItems as $validItem) {
        $newStock = $validItem['current_stock'] - $validItem['quantity'];
        $updateStmt = $conn->prepare("UPDATE produk SET stok = ? WHERE id = ?");
        $updateStmt->bind_param('ii', $newStock, $validItem['product_id']);
        if (!$updateStmt->execute()) {
            throw new Exception('Gagal mengupdate stok');
        }
    }

    // Insert transaction
    $stmt = $conn->prepare(
        "INSERT INTO transaksi (id, timestamp, user_id, total_amount, payment_method, status_id, bukti_pembayaran, expiry_date)
         VALUES (?, ?, ?, ?, ?, 1, '', ?)"
    );
    $stmt->bind_param('ssisis', $transID, $transDate, $user_id, $total, $payment_method, $expiry_formatted);
    if (!$stmt->execute()) {
        throw new Exception('Gagal membuat transaksi');
    }

    // Insert transaction details using validated DB prices
    foreach ($validatedItems as $validItem) {
        $stmt = $conn->prepare(
            "INSERT INTO transaksi_detail (transactions_id, product_id, quantity, unit_price, subtotal)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('siiii', $transID, $validItem['product_id'], $validItem['quantity'], $validItem['unit_price'], $validItem['subtotal']);
        if (!$stmt->execute()) {
            throw new Exception('Gagal menyimpan detail transaksi');
        }
    }

    return true;
});

if ($success) {
    // Clear cart after successful checkout
    unset($_SESSION['cart']);
    $_SESSION['transaction_status'] = 1;
    csrfRegenerate(); // Regenerate CSRF token after sensitive action
} else {
    $_SESSION['transaction_error'] = 'Checkout gagal. Silakan coba lagi.';
}

header("Location: index.php");
exit;
