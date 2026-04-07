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
