<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Database;
use App\OrderService;
use PDO;

class OrderServiceTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        parent::setUp();

        // Create fast in-memory SQLite database
        $this->pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Create required schema
        $this->pdo->exec("
            CREATE TABLE produk (
                id INTEGER PRIMARY KEY,
                nama_produk TEXT,
                harga INTEGER,
                stok INTEGER,
                category_id INTEGER,
                detail TEXT,
                gambar TEXT
            );

            CREATE TABLE status (
                id INTEGER PRIMARY KEY,
                name TEXT
            );

            CREATE TABLE transaksi (
                id TEXT PRIMARY KEY,
                timestamp TEXT,
                user_id INTEGER,
                total_amount INTEGER,
                payment_method TEXT,
                bukti_pembayaran TEXT,
                status_id INTEGER,
                expiry_date INTEGER
            );

            CREATE TABLE transaksi_detail (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                transactions_id TEXT,
                product_id INTEGER,
                quantity INTEGER,
                unit_price INTEGER,
                subtotal INTEGER
            );
        ");

        // Seed test products
        $this->pdo->exec("
            INSERT INTO produk (id, nama_produk, harga, stok, category_id) VALUES
            (1, 'Makanan Kucing Bolt 1kg', 25000, 10, 1),
            (2, 'Fish Oil Omega Bulu', 15000, 5, 2);
            INSERT INTO status (id, name) VALUES (1, 'Menunggu Pembayaran');
        ");

        // Inject in-memory database instance into Database service
        Database::createWithPdo($this->pdo);
    }

    protected function tearDown(): void
    {
        Database::reset();
        parent::tearDown();
    }

    public function testCreateOrderSuccessfullyDeductsStockAndCalculatesTotal(): void
    {
        $cart = [
            ['id' => 1, 'quantity' => 2], // 2 x 25.000 = 50.000
            ['id' => 2, 'quantity' => 1], // 1 x 15.000 = 15.000
        ];

        $error = null;
        $orderId = OrderService::createOrder(1, $cart, 'Transfer Bank', $error);

        $this->assertNotNull($orderId);
        $this->assertStringStartsWith('TRS', $orderId);
        $this->assertNull($error);

        // Verify stock was deducted atomically
        $p1 = Database::fetchOne("SELECT stok FROM produk WHERE id = 1");
        $this->assertSame(8, (int)$p1['stok']); // 10 - 2

        $p2 = Database::fetchOne("SELECT stok FROM produk WHERE id = 2");
        $this->assertSame(4, (int)$p2['stok']); // 5 - 1

        // Verify transaction total amount is 65.000
        $order = Database::fetchOne("SELECT * FROM transaksi WHERE id = ?", [$orderId]);
        $this->assertNotNull($order);
        $this->assertSame(65000, (int)$order['total_amount']);
        $this->assertSame('Transfer Bank', $order['payment_method']);
        $this->assertSame(1, (int)$order['status_id']);

        // Verify line items
        $details = Database::query("SELECT * FROM transaksi_detail WHERE transactions_id = ?", [$orderId]);
        $this->assertCount(2, $details);
    }

    public function testCreateOrderRejectsWhenStockIsInsufficientAndRollsBack(): void
    {
        $cart = [
            ['id' => 1, 'quantity' => 1],  // valid (stok 10)
            ['id' => 2, 'quantity' => 99], // invalid (stok only 5)
        ];

        $error = null;
        $orderId = OrderService::createOrder(1, $cart, 'COD', $error);

        $this->assertNull($orderId);
        $this->assertNotNull($error);
        $this->assertStringContainsString('Stok tidak mencukupi', $error);

        // Verify rollback: stock of Product 1 must remain 10!
        $p1 = Database::fetchOne("SELECT stok FROM produk WHERE id = 1");
        $this->assertSame(10, (int)$p1['stok']);

        // Verify no order was inserted
        $orderCount = Database::fetchOne("SELECT COUNT(*) as cnt FROM transaksi");
        $this->assertSame(0, (int)$orderCount['cnt']);
    }

    public function testCreateOrderFailsWhenProductDoesNotExist(): void
    {
        $cart = [
            ['id' => 999, 'quantity' => 1],
        ];

        $error = null;
        $orderId = OrderService::createOrder(1, $cart, 'COD', $error);

        $this->assertNull($orderId);
        $this->assertStringContainsString('tidak ditemukan', $error);
    }

    public function testOrderCalculates24HourPaymentDeadline(): void
    {
        $cart = [['id' => 1, 'quantity' => 1]];
        $error = null;

        $before = time() + 86400;
        $orderId = OrderService::createOrder(1, $cart, 'Transfer Bank', $error);
        $after = time() + 86400;

        $order = Database::fetchOne("SELECT expiry_date FROM transaksi WHERE id = ?", [$orderId]);
        $this->assertNotNull($order);
        $this->assertGreaterThanOrEqual($before, (int)$order['expiry_date']);
        $this->assertLessThanOrEqual($after, (int)$order['expiry_date']);
    }
}
