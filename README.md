# 🐾 Pawshop

> Modern, lightweight, and containerized PHP e-commerce platform specializing in feline nutrition and pet supplies.

[![PHP Version](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MariaDB Version](https://img.shields.io/badge/MariaDB-10.11-003545?logo=mariadb&logoColor=white)](https://mariadb.org/)
[![Bootstrap Version](https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![Containerized](https://img.shields.io/badge/Container-Podman%20%7C%20Docker-892CA0?logo=podman&logoColor=white)](https://podman.io/)
[![License](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

---

## 📖 Overview

**Pawshop** is a full-featured e-commerce web application engineered for cat lovers and pet supply retailers. Originally built on a legacy LAMP stack, the project has been comprehensively refactored, modernized, and containerized to follow modern software engineering standards—eliminating XAMPP dependencies, separating business logic into reusable domain services, optimizing frontend assets, and delivering a responsive, app-like customer shopping experience.

---

## ✨ Key Features

### 🛒 Customer Storefront
- **Instant In-Place Category Filtering:** Filter through cat food, equipment, healthcare, and toys smoothly without full-page reloads using dynamic background AJAX updates (`history.pushState` enabled).
- **Interactive Offcanvas Shopping Cart:** Slide-out 480px cart drawer accessible across all pages with AJAX quantity steppers (`[-] [ Qty ] [+]`), live price calculations, and pinned checkout controls.
- **Product Details & Stock Indicators:** Real-time stock availability badges, responsive product image showcases, and buyer reassurance trust perks.
- **Modern Order Tracking:** Card-based order dashboard replacing rigid tabular layouts, featuring realistic 24-hour payment deadlines and live image preview for bank transfer receipt uploads.
- **Customer Account Hub:** Profile management portal with quick order statistics (total orders, pending payments, completed transactions) and credential updates.

### ⚙️ Administrative Dashboard (`/admin`)
- **Responsive Admin Layout:** Clean, dark-sidebar navigation built entirely with Bootstrap 5 (no conflicting CSS frameworks or Tailwind CDNs).
- **Real-Time KPI Analytics:** Instant summaries for gross revenue, total completed orders, registered customer counts, and low-stock product alerts.
- **Comprehensive CRUD Operations:** Full management modules for products (with image uploads), categories, order statuses, transactions, and user privileges.
- **Reporting & Data Export:** Date-range financial reporting (daily/weekly/monthly/custom) with one-click CSV report export.

### 🏗️ Architecture & Engineering
- **Domain Service Layer (`src/`):** Clean separation of concerns encapsulating business logic into dedicated services (`AuthService`, `ProductService`, `OrderService`, `FileUploader`, and `Database`).
- **Resilient Dual Database Driver:** Transparent PDO abstraction with an automatic MySQLi fallback, complete with prepared statements and ACID transaction rollback support.
- **Performance Optimized:** 
  - Downscaled legacy 8K/6K image assets to web-optimized resolutions (-95.4% payload reduction, saving over 18 MB per load).
  - Native image `loading="lazy"` and `fetchpriority` hints.
  - Eliminated render-blocking CDN scripts via `defer` and preconnect tags.
  - Apache `.htaccess` rules enabled for Gzip (`mod_deflate`), browser caching (`mod_expires`), and 30-day `Cache-Control` headers.

---

## 🛠️ Tech Stack

- **Backend:** PHP 8.2 (Apache)
- **Database:** MariaDB 10.11
- **Database Management:** phpMyAdmin
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla ES6+), Bootstrap 5.3, FontAwesome 6
- **Typography:** Google Fonts (Montserrat)
- **Containerization:** Podman / Docker (`docker-compose.yml`)

---

## 🚀 Quick Start

### Prerequisites
- [Podman](https://podman.io/) with `podman-compose`, **or** [Docker](https://www.docker.com/) with `docker compose`.
- Git.

### 1. Clone Repository
```bash
git clone https://github.com/davidyusaku-13/pawshop.git
cd pawshop
```

### 2. Start Application Containers
Using **Podman**:
```bash
podman-compose up -d
```
*Or using **Docker**.*
```bash
docker compose up -d
```

The database initializes automatically on the first run from `database/schema.sql`.

### 3. Access URLs

| Portal | URL | Description |
| :--- | :--- | :--- |
| **Storefront** | [http://localhost:8000/](http://localhost:8000/) | Customer catalog, cart, and shopping flow |
| **Admin Panel** | [http://localhost:8000/admin](http://localhost:8000/admin) | Store management, analytics, and reports |
| **phpMyAdmin** | [http://localhost:8080/](http://localhost:8080/) | Direct database administration |

### 4. Stop Application
```bash
podman-compose down
# or: docker compose down
```

---

## 🔑 Default Credentials

| Role | Username | Password | Access |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin` | `admin` | Full admin panel access (`/admin`) & storefront |
| **Customer** | `david` | `david` | Customer shopping, cart, orders, & profile |

*(New customer accounts can also be created via the `/auth/register` page).*

---

## 📁 Project Directory Structure

```text
pawshop/
├── admin/                  # Administrative control panel
│   ├── ajax/               # Admin AJAX endpoints (categories, products, orders, users)
│   ├── categories.php      # Category management
│   ├── export-csv.php      # Financial CSV export
│   ├── generate-report.php # Printable sales report
│   ├── index.php           # Admin dashboard & KPI analytics
│   ├── products.php        # Catalog inventory CRUD
│   ├── reports.php         # Sales filtering and reporting
│   ├── statuses.php        # Order status configuration
│   ├── transactions.php    # Order review & payment verification
│   └── users.php           # User account & privilege control
├── assets/                 # Static assets
│   ├── css/                # Custom stylesheets (style.css)
│   ├── images/             # Optimized brand, carousel, and product photos
│   └── js/                 # Client scripts
├── auth/                   # Authentication endpoints
│   ├── login.php           # User login portal
│   ├── logout.php          # Session termination
│   └── register.php        # Customer registration
├── cart/                   # Shopping cart actions
│   ├── add.php             # Add to cart
│   ├── checkout.php        # Atomic checkout processor
│   ├── remove.php          # Remove cart line item
│   └── update.php          # AJAX quantity stepper endpoint
├── config/                 # Environment & database configuration
│   └── database.php        # Database connection & PSR-4 autoloader
├── customer/               # Customer account views
│   ├── invoice.php         # Printable invoice receipt
│   ├── product-detail.php  # Detailed product view
│   ├── profile.php         # Customer account hub
│   ├── transaction-detail.php # Detailed order view
│   └── transactions.php    # Customer order tracking & receipt upload
├── database/               # Database migrations & schemas
│   └── schema.sql          # Base database DDL and initial seeds
├── includes/               # Reusable view partials & components
│   ├── admin-footer.php    # Admin layout footer
│   ├── admin-guard.php     # Admin authorization middleware
│   ├── admin-header.php    # Admin layout header (<head>)
│   ├── admin-sidebar.php   # Admin responsive sidebar
│   ├── auth-guard.php      # Customer authentication guard
│   ├── cart.php            # Offcanvas drawer cart component
│   ├── catalog-list.php    # Dynamic AJAX catalog partial
│   ├── footer.php          # Storefront footer
│   ├── header.php          # Storefront HTML header (<head>)
│   └── navbar.php          # Storefront navigation bar
├── src/                    # Core Domain Services & Business Logic
│   ├── AuthService.php     # Authentication & registration logic
│   ├── Database.php        # Dual-driver database abstraction (PDO / MySQLi)
│   ├── FileUploader.php    # Secure image upload handler
│   ├── helpers.php         # Global helpers (formatting, flash messages, URL generation)
│   ├── OrderService.php    # Order processing, status transitions, and deadlines
│   └── ProductService.php  # Product catalog and inventory management
├── .htaccess               # Apache compression, caching, and security headers
├── docker-compose.yml      # Podman/Docker multi-container orchestration
├── Dockerfile              # PHP 8.2 Apache container definition
└── index.php               # Customer storefront homepage
```

---

## 📝 Git Commit Guidelines

This project strictly adheres to [Conventional Commits](https://www.conventionalcommits.org/):

- `feat:` New features (e.g., AJAX category switching, offcanvas cart drawer)
- `fix:` Bug fixes (e.g., UI layout alignments, deadline calculations)
- `refactor:` Code restructuring without changing external behavior
- `perf:` Performance optimizations (e.g., image compression, caching headers)
- `style:` Formatting and design adjustments
- `chore:` Dependency or configuration updates

---

## 📄 License

This project is licensed under the [MIT License](LICENSE).
