<?php
/**
 * Database Migration Script
 * Run this script once to update the database schema with:
 * - Indexes for performance
 * - Foreign key constraints
 * - Updated column types
 * - Unique constraints
 *
 * Usage: php migrate.php
 */

require_once __DIR__ . '/includes/db.php';

echo "Starting database migration...\n\n";

$migrations = [
    // Add indexes for better performance
    "ALTER TABLE `produk` ADD INDEX IF NOT EXISTS `idx_category_id` (`category_id`)" => "Add index on produk.category_id",

    "ALTER TABLE `transaksi` ADD INDEX IF NOT EXISTS `idx_user_id` (`user_id`)" => "Add index on transaksi.user_id",

    "ALTER TABLE `transaksi` ADD INDEX IF NOT EXISTS `idx_status_id` (`status_id`)" => "Add index on transaksi.status_id",

    "ALTER TABLE `transaksi` ADD INDEX IF NOT EXISTS `idx_timestamp` (`timestamp`)" => "Add index on transaksi.timestamp",

    "ALTER TABLE `transaksi_detail` ADD INDEX IF NOT EXISTS `idx_transactions_id` (`transactions_id`)" => "Add index on transaksi_detail.transactions_id",

    "ALTER TABLE `transaksi_detail` ADD INDEX IF NOT EXISTS `idx_product_id` (`product_id`)" => "Add index on transaksi_detail.product_id",

    // Add unique constraint on username
    "ALTER TABLE `users` ADD UNIQUE INDEX IF NOT EXISTS `idx_username` (`username`)" => "Add unique index on users.username",

    // Update password column to support bcrypt (60 chars, but 255 for future)
    "ALTER TABLE `users` MODIFY `password` VARCHAR(255) NOT NULL" => "Expand password column for bcrypt",

    // Expand bukti_pembayaran for longer filenames
    "ALTER TABLE `transaksi` MODIFY `bukti_pembayaran` VARCHAR(255) NOT NULL DEFAULT ''" => "Expand bukti_pembayaran column",

    // Expand remember_token
    "ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `remember_token` VARCHAR(64) NOT NULL DEFAULT ''" => "Add/update remember_token column",

    // Add fulltext index for product search (if not exists)
    "ALTER TABLE `produk` ADD FULLTEXT INDEX IF NOT EXISTS `idx_search` (`nama_produk`, `detail`)" => "Add fulltext index for product search",
];

// Foreign key migrations (run separately to check if they already exist)
$foreignKeys = [
    [
        'table' => 'produk',
        'constraint' => 'fk_produk_category',
        'sql' => "ALTER TABLE `produk` ADD CONSTRAINT `fk_produk_category` FOREIGN KEY (`category_id`) REFERENCES `kategori`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE"
    ],
    [
        'table' => 'transaksi',
        'constraint' => 'fk_transaksi_user',
        'sql' => "ALTER TABLE `transaksi` ADD CONSTRAINT `fk_transaksi_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE"
    ],
    [
        'table' => 'transaksi',
        'constraint' => 'fk_transaksi_status',
        'sql' => "ALTER TABLE `transaksi` ADD CONSTRAINT `fk_transaksi_status` FOREIGN KEY (`status_id`) REFERENCES `status`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE"
    ],
    [
        'table' => 'transaksi_detail',
        'constraint' => 'fk_detail_transaction',
        'sql' => "ALTER TABLE `transaksi_detail` ADD CONSTRAINT `fk_detail_transaction` FOREIGN KEY (`transactions_id`) REFERENCES `transaksi`(`id`) ON DELETE CASCADE ON UPDATE CASCADE"
    ],
    [
        'table' => 'transaksi_detail',
        'constraint' => 'fk_detail_product',
        'sql' => "ALTER TABLE `transaksi_detail` ADD CONSTRAINT `fk_detail_product` FOREIGN KEY (`product_id`) REFERENCES `produk`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE"
    ],
];

$conn = getDB();
$success = 0;
$failed = 0;
$skipped = 0;

// Run index migrations
foreach ($migrations as $sql => $description) {
    echo "Running: $description... ";
    try {
        $conn->query($sql);
        echo "OK\n";
        $success++;
    } catch (mysqli_sql_exception $e) {
        if (strpos($e->getMessage(), 'Duplicate') !== false || strpos($e->getMessage(), 'already exists') !== false) {
            echo "SKIPPED (already exists)\n";
            $skipped++;
        } else {
            echo "FAILED: " . $e->getMessage() . "\n";
            $failed++;
        }
    }
}

// Check and add foreign keys
echo "\nChecking foreign key constraints...\n";

foreach ($foreignKeys as $fk) {
    echo "Checking: {$fk['constraint']}... ";

    // Check if constraint exists
    $result = $conn->query("
        SELECT CONSTRAINT_NAME
        FROM information_schema.TABLE_CONSTRAINTS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = '{$fk['table']}'
        AND CONSTRAINT_NAME = '{$fk['constraint']}'
        AND CONSTRAINT_TYPE = 'FOREIGN KEY'
    ");

    if ($result->num_rows > 0) {
        echo "SKIPPED (already exists)\n";
        $skipped++;
        continue;
    }

    try {
        $conn->query($fk['sql']);
        echo "OK\n";
        $success++;
    } catch (mysqli_sql_exception $e) {
        echo "FAILED: " . $e->getMessage() . "\n";
        $failed++;
    }
}

echo "\n";
echo "Migration complete!\n";
echo "Success: $success\n";
echo "Skipped: $skipped\n";
echo "Failed: $failed\n";

if ($failed > 0) {
    echo "\nSome migrations failed. Please check the errors above.\n";
    exit(1);
}

echo "\nDatabase is now up to date.\n";
