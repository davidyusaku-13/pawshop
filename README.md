# Pawshop - Pet Supplies E-commerce

A PHP e-commerce application for pet (cat) supplies with role-based access control (admin/user). Built with vanilla PHP, MySQL (MariaDB), Bootstrap 5, and jQuery. All UI text is in Indonesian (Bahasa Indonesia).

## Quick Start with Docker

### Prerequisites
- Docker and Docker Compose installed
- Git (optional)

### Setup

1. **Clone the repository** (or download the source)
   ```bash
   git clone <repository-url>
   cd pawshop
   ```

2. **Create environment file**
   ```bash
   cp .env.example .env
   ```

3. **Generate a secure admin secret key**
   ```bash
   # Generate a random 64-character key
   php -r "echo bin2hex(random_bytes(32));"

   # Or use openssl
   openssl rand -hex 32
   ```

   Copy the generated key and update `PAWSHOP_ADMIN_SECRET` in your `.env` file.

4. **Start the application**
   ```bash
   docker-compose up -d
   ```

5. **Access the application**
   - **Main application**: http://localhost:8080
   - **phpMyAdmin**: http://localhost:8081
   - **Default admin credentials**:
     - Username: `admin`
     - Password: `admin`

### Docker Services

The setup includes three services:

- **web** (PHP 8.2 + Apache) - Main application server on port 8080
- **db** (MySQL 8.0) - Database server on port 3306
- **phpmyadmin** - Database management interface on port 8081

### Useful Commands

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# View logs
docker-compose logs -f

# Restart services
docker-compose restart

# Rebuild after code changes
docker-compose up -d --build

# Access MySQL CLI
docker-compose exec db mysql -u root -p pawshop

# Access web container shell
docker-compose exec web bash
```

### Database

The database is automatically initialized from `pawshop.sql` on first run. Data persists in a Docker volume named `pawshop_db_data`.

To reset the database:
```bash
docker-compose down -v  # Remove volumes
docker-compose up -d    # Recreate with fresh data
```

## Traditional Setup (XAMPP/WAMP)

If you prefer not to use Docker:

1. Install XAMPP or WAMP (PHP 8.x, MySQL/MariaDB)
2. Place project in `htdocs/pawshop` (XAMPP) or `www/pawshop` (WAMP)
3. Import `pawshop.sql` into MySQL
4. Set `PAWSHOP_ADMIN_SECRET` environment variable
5. Access at http://localhost/pawshop/

## Features

- **User Features**
  - Product browsing with categories
  - Shopping cart
  - Checkout and order tracking
  - User profile management

- **Admin Features**
  - Product management (CRUD)
  - Category management
  - Order management with status updates
  - User management
  - Sales reports and exports

## Security Features

This application includes comprehensive security measures:

- **Authentication**: Bcrypt password hashing with transparent MD5 migration
- **CSRF Protection**: Token-based protection on all forms
- **SQL Injection Prevention**: Prepared statements with parameter binding
- **XSS Protection**: Output escaping helpers
- **Input Validation**: Type-safe input sanitization
- **Secure Sessions**: HTTP-only cookies with secure flags

## Project Structure

```
pawshop/
├── admin*.php          # Admin pages
├── user.php            # Customer storefront
├── checkout.php        # Order processing
├── includes/           # Helper functions
│   ├── auth.php       # Authentication helpers
│   ├── csrf.php       # CSRF protection
│   ├── db.php         # Database helpers
│   ├── security.php   # Security utilities
│   └── validation.php # Input validation
├── css/               # Stylesheets
├── js/                # JavaScript files
├── img/               # Product images
└── config.php         # Database configuration
```

## Development

### Running Migrations

```bash
# With Docker
docker-compose exec web php migrate.php

# Without Docker
php migrate.php
```

### Testing

```bash
# PHPUnit tests (currently empty)
vendor/bin/phpunit
```

## Environment Variables

Required environment variables:

- `PAWSHOP_ADMIN_SECRET` - Secret key for admin registration (min 32 chars)
- `MYSQL_ROOT_PASSWORD` - MySQL root password (Docker only)
- `MYSQL_USER` - MySQL user (Docker only)
- `MYSQL_PASSWORD` - MySQL password (Docker only)

## License

[Add your license here]

## Contributing

[Add contribution guidelines here]
