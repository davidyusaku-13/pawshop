# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Pawshop is a PHP e-commerce application for pet (cat) supplies with role-based access (admin/user). Built with vanilla PHP, MySQL (MariaDB), Bootstrap 5, and jQuery. All UI text is in Indonesian (Bahasa Indonesia).

## Development Setup

```bash
# Requires XAMPP/WAMP (PHP 8.x, MariaDB/MySQL)
# Import database
mysql -u root pawshop < pawshop.sql

# Access application
http://localhost/pawshop/

# Default admin: username "admin", password "admin"
```

## Testing

```bash
vendor/bin/phpunit   # PHPUnit installed via Composer (tests/ directory currently empty)
```

## Architecture

### Entry Point & Routing

- `index.php` → includes `config.php`, then routes to `admin.php` or `user.php` based on `$privilege` global
- No framework routing; each PHP file is a standalone page
- Admin pages: `admin-*.php` with corresponding `admin-*-fetch.php` AJAX endpoints
- User pages: `user.php`, `details.php`, `transactions.php`, `profile.php`, `checkout.php`

### Authentication Pattern

- Session + cookie-based auth in `config.php` (included everywhere)
- Globals `$userid` and `$privilege` set from `$_SESSION` or `$_COOKIE`
- Admin check: `if ($privilege != 'admin') { header('Location: index.php'); }`
- User check: `if (!isset($userid)) { header('Location: index.php'); }`

### Database Schema

```
users        → id, username, password (MD5), phone_number, privilege ('admin'|'user')
produk       → id, gambar, nama_produk, category_id, stok, harga, detail
kategori     → id, name
transaksi    → id (TRS+datetime), timestamp, user_id, total_amount, payment_method, status_id, bukti_pembayaran, expiry_date
transaksi_detail → id, transactions_id, product_id, quantity, unit_price, subtotal
status       → id, name (Indonesian order statuses)
```

### Cart System

- Session-based: Cart stored in `$_SESSION['cart']` as array of `['id', 'quantity', 'name', 'price']`
- Operations: `addToCart.php`, `updateCart.php`, `removeFromCart.php` handle cart via POST
- `checkout.php` creates `transaksi` + `transaksi_detail` records and decrements stock

## Code Patterns

### Database Queries

Uses `mysqli` with direct SQL string interpolation (not parameterized):

```php
$sql = "SELECT * FROM produk WHERE id='$id'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // process $row
    }
}
```

### File Uploads

Images uploaded to `./img/` directory. Validate with `getimagesize()`, then `move_uploaded_file()`.

### Currency Formatting

Indonesian Rupiah: `number_format($value, 2, ",", ".")`

### Transaction IDs

Format: `"TRS" . date("dmyHis")` - maintain this pattern.

## Frontend Stack

- **Admin**: TailwindCSS (CDN), custom colors defined in catatan.txt
- **User**: Bootstrap 5 (local files in css/), custom css/style.css
- **Data Tables**: DataTables library for admin tables
- **Icons**: FontAwesome (CDN)

## Order Statuses (Indonesian)

1. Menunggu Konfirmasi
2. Menunggu Pembayaran
3. Menunggu Konfirmasi Pembayaran
4. Barang Diproses
5. Barang Dikirim
6. Dibatalkan
7. Selesai
