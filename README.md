# 🍔 Restaurant Management & Online Ordering System (MVP)

> A full-stack, dynamic restaurant management system and online ordering platform with real-time analytics, dynamic theme customizer, order tracking, and a comprehensive admin suite.

---

## 📌 Features

### 🛒 Customer Storefront (SPA)
- **Fluid Single Page Application**: Fast navigation, smooth category tabs, and interactive animations.
- **Dynamic Menu & Deals**: Real-time fetching of dishes, custom deal combos, price tags, and dietary badges.
- **Persistent Cart & Fast Checkout**: LocalStorage-backed cart with item customization, delivery fee calculation, and order placement.
- **Live Order Tracking**: Instant tracking status with visual progression steps (Pending $\rightarrow$ Confirmed $\rightarrow$ Preparing $\rightarrow$ Ready $\rightarrow$ Delivered).
- **Authentication**: Customer registration and login system with secure session management.
- **Contact & Inquiry Form**: Built-in messaging form linked directly to the admin message inbox.

### 🔐 Admin Control Center
- **Live Performance Dashboard**: Real-time metrics for today's orders, total revenue, average order value, and visual sales analytics powered by Chart.js.
- **Order Management & Invoicing**: Update order lifecycle statuses, filter by date/status, and generate printable invoices.
- **Menu & Category Management**: Full CRUD capabilities to add, edit, feature, or temporarily disable dishes and category icons.
- **Deals & Combos Builder**: Create promotional deals and combo meals with discounted pricing and expiry dates.
- **Real-Time Theme & Branding Customizer**: Modify site name, logo text, slogans, hero headers, color palette (Primary, Accent, Backgrounds), and typography (Display & Body fonts) without writing a single line of code!
- **Customer Messages & Inquiries**: Read, filter, and respond to incoming customer contact queries.

---

## 🛠️ Tech Stack

- **Frontend**: HTML5, Vanilla JavaScript (ES6+), Tailwind CSS (CDN / Utility classes), FontAwesome 6, Chart.js
- **Backend**: Native PHP 8.x (Modular API Architecture & PDO)
- **Database**: MySQL (Relational schema with cascade rules and indexing)
- **Security**: Bcrypt password hashing, PDO prepared statements against SQL injection, and session-based Role-Based Access Control (RBAC).

---

## 🗂️ Project Structure

```
Restaurant-Management-System/
├── index.php               # Customer Storefront Shell
├── admin/                  # Admin Dashboard Module
│   ├── index.php           # Admin Analytics Dashboard
│   ├── categories.php      # Category Manager
│   ├── items.php           # Menu Item Manager
│   ├── deals.php           # Deals & Combos Manager
│   ├── orders.php          # Order Processing
│   ├── messages.php        # Customer Inquiry Inbox
│   ├── settings.php        # Dynamic Theme & Site Settings
│   ├── users.php           # User & Staff Management
│   └── print_order.php     # Receipt & Invoice Generator
├── api/                    # RESTful JSON Endpoints
│   ├── admin.php           # Admin CRUD & Metrics Endpoints
│   ├── auth.php            # Login, Registration & Session
│   ├── cart.php            # Cart operations
│   ├── contact.php         # Customer Inquiry Submission
│   ├── menu.php            # Public Menu & Categories Fetching
│   ├── orders.php          # Checkout & Order Placement
│   └── upload.php          # Image Upload Handler
├── includes/               # Core Utilities & Configurations
│   ├── db.php              # PDO Database Connection
│   ├── site_settings.php   # Dynamic Config & CSS Injector
│   ├── session.php         # Session Management
│   ├── auth_guard.php      # Role-Based Access Guard
│   └── response.php        # Unified JSON Responder
├── assets/                 # Static Assets
│   ├── js/                 # Modular JS (app.js, admin.js, cart.js, etc.)
│   └── css/                # Custom Stylesheets & Theme Variables
└── sql/
    └── schema.sql          # Master Database Schema & Initial Seed Data
```

---

## 🚀 Quick Start Guide

### Prerequisites
- PHP >= 8.0
- MySQL >= 5.7 or MariaDB
- Apache / Nginx web server (e.g. XAMPP, WAMP, Laragon, or PHP built-in server)

### 1. Installation
Clone or download the repository into your web root directory:
```bash
git clone https://github.com/your-username/Restaurant-Management-System.git
```

### 2. Database Setup
1. Open your database management tool (e.g., phpMyAdmin, MySQL CLI, DBeaver).
2. Create a new database named `restaurant_db` (or import directly):
```sql
CREATE DATABASE restaurant_db;
```
3. Import `sql/schema.sql` into your database.

### 3. Database Configuration
Open `includes/db.php` and verify your connection parameters:
```php
$host = 'localhost';
$dbname = 'restaurant_db';
$username = 'root';
$password = ''; // Set your database password if applicable
```

### 4. Access the Application
- **Customer Storefront**: `http://localhost/Restaurant-Management-System/`
- **Admin Dashboard**: `http://localhost/Restaurant-Management-System/admin/`

#### Default Admin Credentials
- **Email**: `admin@restaurant.com`
- **Password**: `admin@123`

---

## 🎨 Customizing the Brand
Once logged in to the Admin Dashboard:
1. Navigate to **Site Settings** (`admin/settings.php`).
2. Update the **Site Name**, **Tagline**, **Contact Details**, and **Address**.
3. Customize your brand colors (Primary, Accent, Backgrounds) and typography.
4. Changes take effect across both the storefront and admin panel immediately!

---

## 🛡️ License & Contributions
Distributed under the MIT License. Contributions and pull requests are welcome!
