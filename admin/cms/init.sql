-- CMS Tables for JenniferLamiVisuals
-- Run this AFTER the main schema.sql

-- Site settings (key-value pairs)
CREATE TABLE IF NOT EXISTS cms_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Content sections (hero, about, portfolio header, contact, footer)
CREATE TABLE IF NOT EXISTS cms_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page VARCHAR(100) NOT NULL DEFAULT 'home',
    section_key VARCHAR(100) NOT NULL UNIQUE,
    title VARCHAR(255),
    subtitle VARCHAR(255),
    content TEXT,
    image_url VARCHAR(500),
    button_text VARCHAR(100),
    button_link VARCHAR(500),
    extra_json TEXT COMMENT 'JSON for additional structured fields',
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Navigation menu items
CREATE TABLE IF NOT EXISTS cms_navigation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(100) NOT NULL,
    href VARCHAR(500) NOT NULL,
    icon VARCHAR(50),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pricing packages
CREATE TABLE IF NOT EXISTS cms_pricing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(50) NOT NULL COMMENT 'wedding, lifestyle, event',
    package_name VARCHAR(255) NOT NULL,
    price VARCHAR(50) NOT NULL,
    subtitle VARCHAR(255),
    is_featured TINYINT(1) DEFAULT 0 COMMENT 'center/black card',
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pricing features (linked to packages)
CREATE TABLE IF NOT EXISTS cms_pricing_features (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pricing_id INT NOT NULL,
    feature_text VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (pricing_id) REFERENCES cms_pricing(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Portfolio items
CREATE TABLE IF NOT EXISTS cms_portfolio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    video_src VARCHAR(500),
    instagram_url VARCHAR(500),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Social media links
CREATE TABLE IF NOT EXISTS cms_social_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    platform VARCHAR(50) NOT NULL,
    url VARCHAR(500) NOT NULL,
    icon VARCHAR(50),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SEED DEFAULT DATA
-- ============================================================

-- Settings
INSERT IGNORE INTO cms_settings (setting_key, setting_value) VALUES
('site_name', 'JenniferLamiVisuals'),
('site_tagline', 'Professional Videography Services'),
('contact_email', 'info@jenniferlamivisuals.com'),
('contact_phone', '08060425569'),
('site_logo', 'images/logo.png'),
('footer_copyright', 'Copyright © 2026 JenniferLamiVisuals'),
('meta_title', 'JenniferLamiVisuals - Professional Videography Services | Jacksonville Videographer'),
('meta_description', 'Professional Videography Services by JenniferLamiVisuals. Cinematic videos for weddings, events, and commercials in Jacksonville, FL.'),
('meta_keywords', 'videography, wedding videographer, event videography, commercial video production, Jacksonville videographer, JenniferLamiVisuals, cinematic video'),
('og_title', 'JenniferLamiVisuals - Professional Videography Services'),
('og_description', 'Cinematic wedding films, event coverage, and brand promos. Based in Jacksonville, FL.'),
('og_image', 'images/header.png'),
('json_ld_business_name', 'JenniferLamiVisuals'),
('json_ld_price_range', '$200-$2500'),
('json_ld_service_types', 'Wedding Videography,Event Videography,Commercial Video Production,Lifestyle Videography,Social Media Content Creation');

-- Navigation
INSERT IGNORE INTO cms_navigation (label, href, sort_order) VALUES
('Home', '#section_1', 1),
('About', '#section_2', 2),
('Portfolio', '#section_3', 3),
('Pricing', '#section_5', 4),
('Contact', '#section_6', 5);

-- Sections
INSERT IGNORE INTO cms_sections (section_key, title, content, image_url) VALUES
('hero_image', NULL, NULL, 'images/header.png'),
('about_heading', 'About JenniferLami Visuals', NULL, 'images/ceo.png'),
('about_text_1', NULL, 'A documentary-trained eye that finds beauty in unscripted moments, I capture weddings, events, lifestyle and brands with cinematic storytelling and professional polish. Full wedding films, event highlights, brand promos — emotional, timeless, high-end.', NULL),
('about_text_2', NULL, 'But I also live in the fast world of social media. Using just my iPhone, I create scroll-stopping, trend-native Reels, TikToks, and Instagram Stories for events and brands in real time. From golden-hour transitions and viral audio syncs to behind-the-scenes', NULL),
('about_text_3', NULL, 'Whether you''re a couple wanting a timeless wedding film + instant social magic, a brand needing polished campaign footage + trendy short-form content, or an event host looking to amplify every moment — I''m here to make your story look and feel incredible.', NULL),
('about_tagline', 'Your Vision, My Lens', NULL, NULL),
('portfolio_heading', 'Featured on Instagram', 'Check out our latest reels and shots directly from our feed.', NULL),
('contact_heading', 'Interested? Let''s talk', NULL, NULL),
('footer_site_name', 'JenniferLami Visuals', NULL, NULL);

-- Social links
INSERT IGNORE INTO cms_social_links (platform, url, icon, sort_order) VALUES
('Facebook', 'https://www.facebook.com/share/1Hf2ocPwf8/?mibextid=wwXIfr', 'bi-facebook', 1),
('TikTok', 'https://www.tiktok.com/@jenniferlamivisuals?_r=1&_t=ZP-93uRXJUXpGx', 'bi-tiktok', 2),
('Instagram', 'https://www.instagram.com/jenniferlamivisuals/', 'bi-instagram', 3),
('YouTube', 'https://youtube.com/@jenniferlamivisuals?si=GF1MfRWTjIvi6dwX', 'bi-youtube', 4);

-- Portfolio items
INSERT IGNORE INTO cms_portfolio (video_src, instagram_url, sort_order) VALUES
('video/DYa5IslykU-.mp4', 'https://www.instagram.com/reel/DYa5IslykU-/', 1),
('video/DYNKFW3ynKV.mp4', 'https://www.instagram.com/reel/DYNKFW3ynKV/', 2),
('video/DWwrGTFkvTW.mp4', 'https://www.instagram.com/reel/DWwrGTFkvTW/', 3);

-- Pricing categories with packages and features
-- Wedding
INSERT IGNORE INTO cms_pricing (id, category, package_name, price, subtitle, is_featured, sort_order) VALUES
(1, 'wedding', 'Basic Wedding', '$1500', '7 Hours Coverage', 0, 1),
(2, 'wedding', 'Premium Wedding', '$2500', '10- Hours Event Coverage', 1, 2),
(3, 'wedding', 'Standard Wedding', '$600', '7 Hours Coverage', 0, 3);

INSERT IGNORE INTO cms_pricing_features (pricing_id, feature_text, sort_order) VALUES
(1, '5-10 mins full length', 1), (1, '2-3 mins Highlight', 2), (1, '3 Reels', 3),
(2, '10-15 mins full length', 1), (2, '2 Highlights', 2), (2, '2 Reels', 3), (2, 'TikTok/IG Content', 4),
(3, '2-3 mins Highlight', 1), (3, '2 Reels', 2), (3, 'TikTok/IG Content', 3);

-- Lifestyle
INSERT IGNORE INTO cms_pricing (id, category, package_name, price, subtitle, is_featured, sort_order) VALUES
(4, 'lifestyle', 'Photoshoot', '$250', '2 Hours Coverage', 0, 1),
(5, 'lifestyle', 'Ad Campaign', '$500', '9 Hours Coverage', 1, 2),
(6, 'lifestyle', 'Product Shoot', '$200', '2 Hours Coverage', 0, 3);

INSERT IGNORE INTO cms_pricing_features (pricing_id, feature_text, sort_order) VALUES
(4, '2 Reels', 1), (4, 'TikTok/IG Content', 2),
(5, '1 Add', 1), (5, '2 Reels/Teaser', 2), (5, 'TikTok/IG Content', 3),
(6, '3 Reels', 1);

-- Event
INSERT IGNORE INTO cms_pricing (id, category, package_name, price, subtitle, is_featured, sort_order) VALUES
(7, 'event', 'Basic Event', '$250', '2-3 Hours Coverage', 0, 1),
(8, 'event', 'Premium Event', '$500', 'Full Event Coverage', 1, 2),
(9, 'event', 'Standard Event', '$350', '2-4 Hours Coverage', 0, 3);

INSERT IGNORE INTO cms_pricing_features (pricing_id, feature_text, sort_order) VALUES
(7, '1 Highlight (1-2 Mins)', 1), (7, '1 Reels/Teaser', 2),
(8, '1 Highlight (1-3 Mins)', 1), (8, '2 Reels/Teaser', 2), (8, 'TikTok/IG Content', 3),
(9, '1 Highlight (1-3 Mins)', 1), (9, '2 Reels/Teaser', 2);
