CREATE DATABASE IF NOT EXISTS kitchen86;
USE kitchen86;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_id INT NOT NULL,
    item_type ENUM('item', 'deal') NOT NULL,
    quantity INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(80) NOT NULL,
    display_order INT DEFAULT 0,
    icon VARCHAR(10)
);

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

-- Seed Admin User 
-- Email: admin@gmail.com
-- Password: admin@123
INSERT INTO users (name, email, password_hash, role) 
VALUES ('System Admin', 'admin@gmail.com', '__ADMIN_PASSWORD_HASH__', 'admin')
ON DUPLICATE KEY UPDATE role='admin';

-- Seed Categories
INSERT INTO categories (name, display_order, icon) VALUES 
('Matka Fries', 1, '🍟'),
('Fried Chicken', 2, '🍗'),
('Burgers', 3, '🍔'),
('Wraps', 4, '🌯'),
('Drinks', 5, '🥤');

-- --------------------------------------------------------
-- 10. Messages Table
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
-- 11. Site Settings Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) UNIQUE NOT NULL,
    `value` TEXT,
    `group` VARCHAR(50) DEFAULT 'general',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Seed Default Settings
INSERT IGNORE INTO site_settings (`key`, `value`, `group`) VALUES 
('site_name', 'Kitchen 86', 'general'),
('site_tagline', 'The Taste of Tradition', 'general'),
('about_us', 'Kitchen 86 brings you the most authentic flavors with a modern twist. Our journey began with a passion for quality ingredients and traditional cooking methods.', 'general'),
('contact_phone', '+92 300 1234567', 'general'),
('contact_email', 'info@kitchen86.com', 'general'),
('address', 'Street 86, Food Street, Lahore, Pakistan', 'general'),
('hero_heading', 'Experience the Fiery Flavors of Kitchen 86', 'hero'),
('hero_subheading', 'Authentic Matka Fries, Burgers, and More!', 'hero'),
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
('footer_description', 'Serving the best flavors since 2024. Quality and taste are our top priorities.', 'footer'),
('footer_copyright', '© 2024 Kitchen 86. All rights reserved.', 'footer'),
('social_instagram', 'https://instagram.com/kitchen86', 'footer'),
('social_facebook', 'https://facebook.com/kitchen86', 'footer'),
('social_whatsapp', 'https://wa.me/923001234567', 'footer'),
('light_bg_color', '#F8FAFC', 'brand'),
('light_surface_color', '#FFFFFF', 'brand'),
('light_primary_color', '#FF2020', 'brand'),
('light_accent_color', '#FFB800', 'brand'),
('light_text_color', '#0F172A', 'brand');
