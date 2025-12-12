# Pawshop - AI Coding Instructions

## Project Overview

Pawshop is a PHP e-commerce application for pet (cat) supplies with role-based access (admin/user). Built with vanilla PHP, MySQL (MariaDB), Bootstrap 5, and jQuery.

## Architecture

### Entry Point & Routing

- `index.php` → includes `config.php`, then routes to `admin.php` or `user.php` based on `$privilege` global
- No framework routing; each PHP file is a standalone page with its own logic
- Admin pages: `admin-*.php` (dashboard, products, users, reports, transactions)
- User pages: `user.php`, `details.php`, `transactions.php`, `profile.php`, `checkout.php`

### Database Schema (MySQL - `pawshop.sql`)

```
users        → id, username, password (MD5), phone_number, privilege ('admin'|'user')
produk       → id, gambar, nama_produk, category_id, stok, harga, detail
kategori     → id, name
transaksi    → id (TRS+datetime), timestamp, user_id, total_amount, payment_method, status_id, bukti_pembayaran, expiry_date
transaksi_detail → id, transactions_id, product_id, quantity, unit_price, subtotal
status       → id, name (order statuses: 'Menunggu Konfirmasi', 'Menunggu Pembayaran', etc.)
```

### Authentication Pattern

- Session + cookie-based auth in `config.php` (included everywhere)
- Globals `$userid` and `$privilege` set from `$_SESSION` or `$_COOKIE`
- Admin check: `if ($privilege != 'admin') { header('Location: index.php'); }`
- User check: `if (!isset($userid)) { header('Location: index.php'); }`

### Cart System (Session-based)

- Cart stored in `$_SESSION['cart']` as array of `['id', 'quantity', 'name', 'price']`
- `addToCart.php`, `updateCart.php`, `removeFromCart.php` handle cart operations via POST
- `checkout.php` processes cart → creates `transaksi` + `transaksi_detail` records + decrements stock

## Code Patterns

### Database Queries

- Uses prepared statement helpers from `includes/db.php` for SQL injection protection
- **All queries must use parameter binding** - never interpolate variables directly into SQL
- Type specifiers for bindings: `i` (integer), `s` (string), `d` (double), `b` (blob)

**Single row fetch with `dbFetchOne()`:**

```php
$product = dbFetchOne("SELECT * FROM produk WHERE id = ?", 'i', [$id]);
if ($product) {
    // process $product['nama_produk'], $product['harga'], etc.
}
```

**Multiple rows fetch with `dbFetchAll()`:**

```php
$products = dbFetchAll(
    "SELECT p.*, k.name as kategori FROM produk p JOIN kategori k ON p.category_id = k.id WHERE p.stok > ?",
    'i',
    [0]
);
foreach ($products as $row) {
    // process each $row
}
```

**Insert/Update/Delete with `dbQuery()`:**

```php
// Insert
$result = dbQuery(
    "INSERT INTO produk (nama_produk, category_id, stok, harga) VALUES (?, ?, ?, ?)",
    'siid',
    [$name, $categoryId, $stock, $price]
);

// Update
$result = dbQuery("UPDATE produk SET stok = ? WHERE id = ?", 'ii', [$newStock, $id]);

// Delete
$result = dbQuery("DELETE FROM produk WHERE id = ?", 'i', [$id]);
```

### AJAX Data Endpoints

- `*-fetch.php` files return JSON for AJAX (used with DataTables)
- Example: `admin-produk-fetch.php` returns product data for editing

### File Uploads

- Images uploaded to `./img/` directory
- Pattern: validate with `getimagesize()`, then `move_uploaded_file()`

### Currency Formatting

- Indonesian Rupiah: `number_format($value, 2, ",", ".")`

## Frontend Stack

- **Admin**: TailwindCSS (CDN), custom colors in `tailwind.config` (see `catatan.txt` for color reference)
- **User**: Bootstrap 5 (`css/bootstrap.min.css`), custom `css/style.css`
- **Data Tables**: DataTables library for admin tables
- **Icons**: FontAwesome (CDN)

## Development Setup

1. Run with XAMPP/WAMP (PHP 8.x, MariaDB/MySQL)
2. Import `pawshop.sql` to create database and seed data
3. Configure `config.php`: localhost, root, no password, database 'pawshop'
4. Access via `http://localhost/pawshop/`
5. Default admin: username `admin`, password `admin`

## Testing

- PHPUnit installed via Composer (`vendor/phpunit/`)
- Tests directory: `tests/` (currently empty)
- Run tests: `vendor/bin/phpunit`

## Key Files Reference

| File           | Purpose                                             |
| -------------- | --------------------------------------------------- |
| `config.php`   | DB connection, session/cookie auth, timezone        |
| `admin.php`    | Admin dashboard with stats cards, low-stock alerts  |
| `user.php`     | Customer storefront with product grid, cart sidebar |
| `checkout.php` | Transaction creation, stock decrement               |
| `invoice.php`  | Transaction receipt display                         |

## Indonesian Language Notes

- UI text is in Indonesian (Bahasa Indonesia)
- Status names: Menunggu Konfirmasi, Menunggu Pembayaran, Barang Diproses, etc.
- Keep UI strings consistent with existing Indonesian translations
