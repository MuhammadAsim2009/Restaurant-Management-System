-- Kitchen 86 - Site Settings Table
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) UNIQUE NOT NULL,
    `value` TEXT,
    `group` VARCHAR(50) DEFAULT 'general',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Seed Default Settings
INSERT IGNORE INTO site_settings (`key`, `value`, `group`) VALUES 
-- General
('site_name', 'Kitchen 86', 'general'),
('site_tagline', 'The Taste of Tradition', 'general'),
('about_us', 'Kitchen 86 brings you the most authentic flavors with a modern twist. Our journey began with a passion for quality ingredients and traditional cooking methods.', 'general'),
('contact_phone', '+92 300 1234567', 'general'),
('contact_email', 'info@kitchen86.com', 'general'),
('address', 'Street 86, Food Street, Lahore, Pakistan', 'general'),
('map_embed_link', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3401.123456789012!2d74.3456789!3d31.5678901!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzHCsDM0JzA0LjQiTiA3NMKwMjAnNDQuNCJF!5e0!3m2!1sen!2s!4v1234567890123', 'general'),

-- Hero Section
('hero_heading', 'Experience the Fiery Flavors of Kitchen 86', 'hero'),
('hero_subheading', 'Authentic Matka Fries, Burgers, and More!', 'hero'),
('hero_bg_image', 'assets/images/hero_bg.jpg', 'hero'),
('hero_cta_text', 'Order Now', 'hero'),

-- Brand Customization
('primary_color', '#FF2020', 'brand'),
('accent_color', '#FFB800', 'brand'),
('bg_color', '#161616', 'brand'),
('surface_color', '#1A1A1A', 'brand'),
('text_color', '#FFFFFF', 'brand'),
('font_primary', 'Barlow', 'brand'),
('font_secondary', 'Barlow Condensed', 'brand'),
('font_display', 'Bebas Neue', 'brand'),

-- Footer
('footer_description', 'Serving the best flavors since 2024. Quality and taste are our top priorities.', 'footer'),
('footer_copyright', '© 2024 Kitchen 86. All rights reserved.', 'footer'),
('social_instagram', 'https://instagram.com/kitchen86', 'footer'),
('social_facebook', 'https://facebook.com/kitchen86', 'footer'),
('social_whatsapp', 'https://wa.me/923001234567', 'footer');
