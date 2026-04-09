# 🍗 Kitchen 86 — Restaurant Management MVP

> Full-stack single-page web app for Kitchen 86 Larkana — ordering, cart, checkout, and admin panel.
> Specialized in Cheezious Matka Fries & Fried Chicken 🔥

---

## 📌 Project Overview

**Stack:** HTML · Tailwind CSS (CDN) · Vanilla JavaScript · PHP · MySQL  
**Location:** Beside KFC & Jinnah Bagh, Larkana, Pakistan  
**Social:** [@the_kitchen86](https://instagram.com/the_kitchen86) · [Facebook](https://facebook.com/p/Kitchen-86-61550338954877)

---

## 🎨 Brand Identity

> Colors and fonts extracted directly from Kitchen 86's official Facebook and Instagram pages.

### Color Palette

| Token | Hex | Usage |
|-------|-----|-------|
| `k86-red` | `#FF2020` | Primary brand red — Logo, CTAs, accents, borders |
| `k86-red-dark` | `#CC1010` | Hover / active / pressed states |
| `k86-amber` | `#FFB800` | Fire accent — prices, HOT/NEW badges, highlights |
| `k86-black` | `#0A0A0A` | Page background (near black) |
| `k86-surface` | `#161616` | Card and panel backgrounds |
| `k86-surface2` | `#222222` | Elevated — modals, dropdowns, nav |
| `k86-border` | `#2A2A2A` | Dividers, card borders, input borders |
| `k86-muted` | `#AAAAAA` | Secondary text, placeholders |
| `k86-white` | `#FFFFFF` | Primary text on dark backgrounds |
| `k86-success` | `#22C55E` | Order confirmed, available, delivered |
| `k86-warning` | `#F97316` | Preparing, pending status |
| `k86-danger` | `#EF4444` | Errors, cancelled orders |

### Typography

| Role | Font | Weight | Usage |
|------|------|--------|-------|
| Display / Logo | **Bebas Neue** | 400 | "KITCHEN 86" logo, hero numbers, section display |
| Headings | **Poppins** | 700 | Section titles, card names, admin headings |
| Body / UI | **Barlow** | 400–600 | All UI text, descriptions, labels, nav |
| Prices / Numbers | **Barlow Condensed** | 700 | Prices in PKR, order totals, quantity badges |

```html
<!-- Add to <head> before Tailwind -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@400;600;700&family=Barlow:wght@400;500;600&family=Barlow+Condensed:wght@600;700&display=swap" rel="stylesheet">
```

### Tailwind CDN Config

```html
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          'k86-red':      '#FF2020',   // primary brand red
          'k86-red-dark': '#CC1010',   // hover states
          'k86-amber':    '#FFB800',   // fire accent / prices
          'k86-black':    '#0A0A0A',   // page background
          'k86-surface':  '#161616',   // cards / panels
          'k86-surface2': '#222222',   // modals / elevated
          'k86-border':   '#2A2A2A',   // borders / dividers
          'k86-muted':    '#AAAAAA',   // secondary text
        },
        fontFamily: {
          'display': ['Bebas Neue', 'cursive'],
          'heading': ['Poppins', 'sans-serif'],
          'body':    ['Barlow', 'sans-serif'],
          'price':   ['Barlow Condensed', 'sans-serif'],
        }
      }
    }
  }
</script>
```

### Aesthetic Direction

**Bold. Hot. Fast.** Kitchen 86 is a loud fast-food brand — red on black, fire energy, high contrast. Think KFC-level color confidence but darker and more street. Design rules:

- Backgrounds are always dark (`#0A0A0A` or `#161616`) — never white
- Every CTA button is solid `#FF2020` — no exceptions
- Prices always render in `#FFB800` Barlow Condensed — they should pop
- "HOT", "NEW", "DEAL" badges use amber or red on dark
- The word "86" or "KITCHEN 86" always appears in Bebas Neue — large, tracked, dominant
- Red border accents on cards give the brand-mark feel without overloading red fills

---

## 🗂️ Project Structure

```
kitchen86/
│
├── index.php                   # Customer SPA shell
├── admin/
│   └── index.php               # Admin panel SPA shell
│
├── api/
│   ├── auth.php                # Login · Register · Logout · Me
│   ├── menu.php                # Menu items · Categories · Deals
│   ├── orders.php              # Place order · History · Admin manage
│   └── admin.php               # Full admin CRUD
│
├── includes/
│   ├── db.php                  # PDO MySQL connection
│   ├── session.php             # Session start + helpers
│   ├── auth_guard.php          # Admin route protection
│   └── response.php            # JSON response helper
│
├── assets/
│   ├── css/app.css             # Custom styles on top of Tailwind CDN
│   ├── js/
│   │   ├── app.js              # SPA routing + section rendering
│   │   ├── cart.js             # Cart: add/remove/qty/localStorage
│   │   ├── auth.js             # Login/Register AJAX + session sync
│   │   ├── checkout.js         # Checkout form + order submission
│   │   └── admin.js            # Admin panel logic
│   └── images/                 # Food photos, logo assets
│
└── sql/
    └── kitchen86.sql           # DB schema + seed data + admin account
```

---

## 🗃️ Database Schema

### `users`
| Column | Type | Notes |
|--------|------|-------|
| id | INT PK AUTO_INCREMENT | |
| name | VARCHAR(100) | |
| email | VARCHAR(150) UNIQUE | |
| password_hash | VARCHAR(255) | bcrypt |
| role | ENUM('customer','admin') | Default: customer |
| phone | VARCHAR(20) | |
| created_at | TIMESTAMP | |

### `categories`
| Column | Type | Notes |
|--------|------|-------|
| id | INT PK | |
| name | VARCHAR(80) | e.g. Fries, Burgers, Wraps, Drinks |
| display_order | INT | Sort order in menu |
| icon | VARCHAR(10) | Emoji icon |

### `menu_items`
| Column | Type | Notes |
|--------|------|-------|
| id | INT PK | |
| category_id | INT FK | → categories.id |
| name | VARCHAR(150) | |
| description | TEXT | |
| price | DECIMAL(8,2) | PKR |
| image | VARCHAR(255) | Image path |
| is_available | TINYINT(1) | 0 = hidden from menu |
| is_featured | TINYINT(1) | Show on homepage hero |
| created_at | TIMESTAMP | |

### `deals`
| Column | Type | Notes |
|--------|------|-------|
| id | INT PK | |
| name | VARCHAR(150) | e.g. "Family Bundle" |
| description | TEXT | |
| price | DECIMAL(8,2) | Deal price in PKR |
| original_price | DECIMAL(8,2) | For showing savings |
| items_json | JSON | Array of included menu_item IDs |
| image | VARCHAR(255) | |
| valid_until | DATE | NULL = no expiry |
| is_active | TINYINT(1) | |

### `orders`
| Column | Type | Notes |
|--------|------|-------|
| id | INT PK | |
| user_id | INT FK NULL | NULL = guest |
| customer_name | VARCHAR(100) | |
| phone | VARCHAR(20) | |
| address | TEXT | |
| order_type | ENUM('delivery','pickup') | |
| payment_method | ENUM('cash','card') | |
| subtotal | DECIMAL(8,2) | |
| tax | DECIMAL(8,2) | |
| total | DECIMAL(8,2) | |
| status | ENUM('pending','confirmed','preparing','ready','delivered','cancelled') | |
| notes | TEXT | |
| created_at | TIMESTAMP | |

### `order_items`
| Column | Type | Notes |
|--------|------|-------|
| id | INT PK | |
| order_id | INT FK | → orders.id |
| item_id | INT FK NULL | → menu_items.id |
| item_name | VARCHAR(150) | Snapshot at order time |
| item_price | DECIMAL(8,2) | Snapshot at order time |
| quantity | INT | |
| subtotal | DECIMAL(8,2) | item_price × quantity |

---

## 🌐 Customer SPA Features

### Hash Routing

| Section | Hash | Description |
|---------|------|-------------|
| Home | `#home` | Hero, featured items, deals strip |
| Menu | `#menu` | Category tabs, item cards, Add to Cart |
| Cart | `#cart` | Slide-out drawer — qty, remove, totals |
| Checkout | `#checkout` | Name, phone, address, type, payment |
| My Orders | `#orders` | History (login required) |
| Login | `#login` | Email + password modal |
| Register | `#register` | Full registration form |

### Cart
- `localStorage` — works without login
- Persists on login — nothing wiped
- Add / increment / decrement / remove
- Red badge on nav cart icon
- Live subtotal + tax + total

### Checkout Flow
1. Fill form → client-side validation
2. AJAX POST → `/api/orders.php`
3. Cart cleared → confirmation screen with order ID

### Auth
- Session checked on load: `GET /api/auth.php?action=me`
- Logout clears session + JS re-renders nav
- `#orders` redirects to `#login` if unauthenticated

---

## 🔧 Admin Panel (`/admin/`)

Role `admin` required — checked server-side on every request.

| Section | Features |
|---------|----------|
| Dashboard | Orders today · Revenue today · Pending count · Total users |
| Menu Manager | Add / edit / toggle availability / delete items |
| Deals Manager | Create combos · Set discount · Expiry · Active toggle |
| Orders | Auto-refresh 30s · Filter by status · Update status · Full detail view |
| Users | Name · Email · Phone · Join date · Order count |
| Categories | Add / rename / reorder / delete |

---

## 🔌 API Reference

### `/api/auth.php`
| Method | Param | Description |
|--------|-------|-------------|
| POST | `action=login` | Login |
| POST | `action=register` | Register |
| POST | `action=logout` | Logout |
| GET | `action=me` | Current user |

### `/api/menu.php`
| Method | Params | Description |
|--------|--------|-------------|
| GET | — | All categories + items |
| GET | `category_id=X` | Filter by category |
| GET | `featured=1` | Featured items |
| GET | `deals=1` | Active deals |

### `/api/orders.php`
| Method | Params | Description |
|--------|--------|-------------|
| POST | — | Place order |
| GET | `my=1` | My orders (auth) |
| GET | `admin=1` | All orders (admin) |
| GET | `id=X` | Order detail |
| PATCH | `id=X` | Update status (admin) |

### `/api/admin.php`
| Method | `resource=` | Description |
|--------|------------|-------------|
| CRUD | `items` | Menu items |
| CRUD | `deals` | Deals/combos |
| CRUD | `categories` | Categories |
| GET | `users` | All users |
| GET | `dashboard` | Stats |

---

## 🚀 Setup

```bash
# 1. Place files in web root
# 2. Create database
mysql -u root -p -e "CREATE DATABASE kitchen86;"

# 3. Import schema + seed
mysql -u root -p kitchen86 < sql/kitchen86.sql

# 4. Configure includes/db.php with your credentials
```

### Seeded Admin
```
Email:    admin@gmail.com
Password: Admin123

```

---

## 📋 Development Phases

### Phase 1 — Foundation
- [ ] DB schema + seed data (`sql/kitchen86.sql`)
- [ ] `includes/db.php`, `session.php`, `response.php`
- [ ] Base HTML shell for `index.php` and `admin/index.php`
- [ ] Tailwind config + base CSS variables

### Phase 2 — Customer Frontend
- [ ] Navigation + hash routing in `app.js`
- [ ] Menu section (fetch + render items by category)
- [ ] Cart system (`cart.js` + localStorage)
- [ ] Checkout form + order submission
- [ ] Login/Register modals + session sync (`auth.js`)

### Phase 3 — Backend APIs
- [ ] `api/auth.php` — full auth flow
- [ ] `api/menu.php` — menu + deals fetch
- [ ] `api/orders.php` — order placement + history
- [ ] `api/admin.php` — full admin CRUD

### Phase 4 — Admin Panel
- [ ] Dashboard stats view
- [ ] Menu & deals manager (add/edit/delete)
- [ ] Order management + status updates
- [ ] User list + categories manager

### Phase 5 — Polish
- [ ] Loading states + skeleton screens
- [ ] Toast notifications (success/error)
- [ ] Full mobile responsiveness
- [ ] Image upload handling (server-side)
- [ ] Input validation (client + server)

---

## 🔐 Security

- `password_hash()` bcrypt on all passwords
- PDO prepared statements — no SQL injection
- Server-side session role check on all admin routes
- Input validation + sanitization on all API inputs

---

## 📦 MVP Scope

**In:** Menu browsing · Cart · Checkout · Auth · Admin CRUD · Order management · Deals

**Out (post-MVP):** Payment gateway · SMS/email alerts · Table reservations · KDS · Inventory · Multi-branch

---

*Kitchen 86 · Larkana, Pakistan · Beside KFC & Jinnah Bagh · +92 333 7522050*
