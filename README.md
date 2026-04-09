# 🍗 Kitchen 86 — Restaurant Management MVP

> A premium, fully dynamic restaurant management system and ordering platform for Kitchen 86 Larkana.  
> Specialized in **Cheezious Matka Fries & Fried Chicken** 🔥

---

## 📌 Project Overview

**Stack:** HTML5 · Vanilla JavaScript (SPA) · Tailwind CSS · PHP 8+ · MySQL  
**Location:** Beside KFC & Jinnah Bagh, Larkana, Pakistan  
**Dynamic Control:** Fully manageable via the integrated Admin Panel.

---

## 🌟 Key Features

### 🛒 Customer Storefront (SPA)
- **Fluid Navigation**: Seamless hash-based routing for a single-page experience.
- **Dynamic Menu**: Real-time fetching of menu items and deals by category.
- **Smart Cart**: Persistent local-storage based cart with live totals and tax calculation.
- **Modern Checkout**: Guest and registered user checkout flows with instant order tracking.
- **Auth System**: Secure login and registration with session-based authentication.

### 🔐 Admin Dashboard
- **Instant Stats**: Real-time overview of today's orders, revenue, and pending tasks.
- **Dynamic Site Branding**: Update site name, tagline, colors, and fonts instantly without code changes.
- **Message Center**: Manage customer inquiries directly from the dashboard with 'Read/Unread' status tracking.
- **Full CRUD Managers**: Total control over Menu Items, Deals, Categories, and Users.
- **Order Management**: Professional order processing with status updates and printable invoices.

---

## 🎨 Brand Identity

The system uses a high-contrast, premium dark theme tailored to the Kitchen 86 brand:
- **Primary Color**: `#FF2020` (Kitchen 86 Red)
- **Accent Color**: `#FFB800` (Amber/Fire)
- **Base Theme**: Deep Black and Surface Charcoal for a modern, fast-food aesthetic.

---

## 🗂️ Project Structure

```
kitchen86/
├── index.php               # Customer SPA Main Shell
├── admin/                  # Admin Dashboard Module
│   ├── index.php           # Dashboard Entry
│   ├── messages.php        # Message Management
│   ├── settings.php        # Dynamic Site Settings
│   └── print_order.php     # Invoice Generation
├── api/                    # RESTful JSON APIs
│   ├── auth.php            # Security & Session
│   ├── menu.php            # Menu & Deals Data
│   ├── orders.php          # Order Processing
│   └── contact.php         # Public Message API
├── includes/               # Core Logic & Utilities
│   ├── db.php              # PDO Connection
│   ├── site_settings.php   # Dynamic Config Loader
│   └── auth_guard.php      # Admin Authorization
├── assets/                 # Frontend Resources
│   ├── js/                 # App logic (app.js, cart.js, auth.js)
│   └── css/                # Custom Styles & Tailwind
└── sql/
    └── kitchen86.sql       # COMPLETED Master Database Schema & Seed
```

---

## 🚀 Quick Setup

1.  **Clone/Copy Files**: Place the project folder in your web root (e.g., `xampp/htdocs/Kitchen_86_MVP`).
2.  **Database Import**:
    - Create a database named `kitchen86`.
    - Import the **master** SQL file: `sql/kitchen86.sql`.
3.  **Config**: Update `includes/db.php` with your local database credentials if they differ from defaults (root/no password).

### Default Admin Credentials
- **Email**: ``sasim4589@gmail.com`
- **Password**: `Asim@123`

---

## 🔐 Security & Optimization
- **Secure Hashing**: All passwords use `bcrypt` encryption.
- **SQL Protection**: Forced use of PDO prepared statements throughout to prevent injection.
- **Access Control**: Robust server-side role verification for all admin routes.
- **Optimized UI**: Modern skeleton loaders and toast notifications for an enhanced user experience.

---
*Developed for Kitchen 86 · Larkana, Pakistan · Beside KFC & Jinnah Bagh*  
**Contact:** [Instagram](https://instagram.com/the_kitchen86) | [Facebook](https://facebook.com/p/Kitchen-86-61550338954877)
