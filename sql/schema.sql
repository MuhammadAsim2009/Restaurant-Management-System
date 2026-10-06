-- ========================================================
-- Restaurant Management System & Ordering Platform MVP
-- Database Schema & Initial Seed Data
-- ========================================================

CREATE DATABASE IF NOT EXISTS restaurant_db;
USE restaurant_db;

-- --------------------------------------------------------
-- 1. Users Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------
-- 2. Cart Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_id INT NOT NULL,
    item_type ENUM('item', 'deal') NOT NULL,
    quantity INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- --------------------------------------------------------
-- 3. Categories Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(80) NOT NULL,
    display_order INT DEFAULT 0,
    icon VARCHAR(10)
);

-- --------------------------------------------------------
-- 4. Menu Items Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS menu_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(8,2) NOT NULL,
    image VARCHAR(255),
    is_available TINYINT(1) DEFAULT 1,
    is_featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- --------------------------------------------------------
-- 5. Deals / Combos Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS deals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2),
    items_json JSON,
    image VARCHAR(255),
    valid_until DATE,
    is_active TINYINT(1) DEFAULT 1
);

-- --------------------------------------------------------
-- 6. Orders Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    order_type ENUM('delivery', 'pickup') DEFAULT 'delivery',
    payment_method ENUM('cash', 'card') DEFAULT 'cash',
    subtotal DECIMAL(8,2) NOT NULL,
    tax DECIMAL(8,2) NOT NULL,
    delivery_fee DECIMAL(8,2) DEFAULT 0.00,
    total DECIMAL(8,2) NOT NULL,
    status ENUM('pending','confirmed','preparing','ready','delivered','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- --------------------------------------------------------
-- 7. Order Items Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    item_id INT NULL,
    item_type ENUM('item', 'deal') DEFAULT 'item',
    item_name VARCHAR(150) NOT NULL,
    item_price DECIMAL(8,2) NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(8,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- --------------------------------------------------------
-- 8. Messages / Inquiries Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('unread', 'read') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------
-- 9. Dynamic Site Settings Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) UNIQUE NOT NULL,
    `value` TEXT,
    `group` VARCHAR(50) DEFAULT 'general',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- --------------------------------------------------------
-- SEED DATA
-- --------------------------------------------------------

-- Seed Default Administrator
-- Default Email: admin@restaurant.com
-- Default Password: admin@123
INSERT INTO users (name, email, password_hash, role) 
VALUES ('System Admin', 'admin@restaurant.com', '$2y$10$o/8u2lY.ikoqsPgkdNAfS.AElyzgHCcsfYiiSVX1CJZ3b8xNHAx9i', 'admin')
ON DUPLICATE KEY UPDATE role='admin';

-- Seed Categories
INSERT INTO categories (name, display_order, icon) VALUES 
('Gourmet Burgers', 1, '🍔'),
('Crispy Chicken', 2, '🍗'),
('Loaded Fries', 3, '🍟'),
('Artisan Wraps', 4, '🌯'),
('Beverages', 5, '🥤');

-- Seed Sample Menu Items
INSERT INTO menu_items (category_id, name, description, price, image, is_available, is_featured) VALUES
(1, 'Classic Smash Burger', 'Double smashed beef patties with melted cheddar, house sauce, and pickles on a brioche bun.', 650.00, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600&q=80', 1, 1),
(1, 'Smoky BBQ Bacon Burger', 'Angus beef patty topped with smoked beef bacon, caramelized onions, and smoky BBQ drizzle.', 799.00, 'https://images.unsplash.com/photo-1586190848861-99aa4a171e90?w=600&q=80', 1, 1),
(2, 'Crunchy Fried Chicken Strips', 'Tender boneless chicken strips fried to golden perfection, served with garlic mayo dip.', 520.00, 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=600&q=80', 1, 1),
(3, 'Cheezy Loaded Matka Fries', 'Crispy spiced fries smothered in liquid cheese, jalapeños, and pulled chicken bits.', 480.00, 'https://images.unsplash.com/photo-1585109649139-366815a0d713?w=600&q=80', 1, 1),
(4, 'Fiery Grilled Chicken Wrap', 'Grilled spicy chicken fillet with lettuce, tomatoes, and spicy chipotle rolled in a warm tortilla.', 450.00, 'https://images.unsplash.com/photo-1626700051175-6818013e1d4f?w=600&q=80', 1, 0),
(5, 'Artisan Mint Lemonade', 'Fresh squeezed lemon juice infused with crushed wild mint and sparkling soda.', 220.00, 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=600&q=80', 1, 0);

-- Seed Sample Deals
INSERT INTO deals (name, description, price, original_price, is_active) VALUES
('Duo Mega Feast', '2 Classic Smash Burgers, 1 Cheezy Loaded Fries, and 2 Chilled Drinks.', 1499.00, 1850.00, 1),
('Crispy Chicken Box', '4 Pcs Crispy Fried Strips, 1 Artisan Wrap, French Fries & 1 Drink.', 999.00, 1250.00, 1);

-- Seed Default Settings
INSERT IGNORE INTO site_settings (`key`, `value`, `group`) VALUES 
('site_name', 'Restaurant', 'general'),
('site_tagline', 'The Taste of Quality & Tradition', 'general'),
('about_us', 'We started with a simple belief: dining out should be exceptional. We bring you authentic, mouth-watering gourmet dishes, crafted with fresh ingredients and passion straight to your table.', 'general'),
('contact_phone', '+1 (555) 019-2834', 'general'),
('contact_email', 'contact@restaurant.com', 'general'),
('address', '123 Gourmet Boulevard, Food District', 'general'),
('hero_heading', 'Experience Fine Dining & Bold Flavors', 'hero'),
('hero_subheading', 'Freshly prepared meals, artisanal recipes, and fast delivery to your doorstep.', 'hero'),
('hero_bg_image', 'assets/images/hero_bg.jpg', 'hero'),
('hero_cta_text', 'Order Now', 'hero'),
('primary_color', '#FF2020', 'brand'),
('accent_color', '#FFB800', 'brand'),
('bg_color', '#161616', 'brand'),
('surface_color', '#1A1A1A', 'brand'),
('text_color', '#FFFFFF', 'brand'),
('font_primary', 'Barlow', 'brand'),
('font_secondary', 'Barlow Condensed', 'brand'),
('font_display', 'Bebas Neue', 'brand'),
('footer_description', 'Serving fresh, chef-curated meals crafted with quality ingredients. Taste the difference.', 'footer'),
('footer_copyright', '© 2026 Restaurant. All rights reserved.', 'footer'),
('social_instagram', 'https://instagram.com', 'footer'),
('social_facebook', 'https://facebook.com', 'footer'),
('social_whatsapp', 'https://wa.me/15550192834', 'footer'),
('light_bg_color', '#F8FAFC', 'brand'),
('light_surface_color', '#FFFFFF', 'brand'),
('light_primary_color', '#FF2020', 'brand'),
('light_accent_color', '#FFB800', 'brand'),
('light_text_color', '#0F172A', 'brand');
