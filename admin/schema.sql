CREATE DATABASE IF NOT EXISTS jennife2_jlv_bookings;
USE jennife2_jlv_bookings;

-- Bookings from process_booking.php
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(100) NOT NULL,
    event_date DATE DEFAULT NULL,
    package VARCHAR(255) NOT NULL,
    message TEXT,
    status ENUM('new', 'replied', 'completed') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contact messages from send_email.php
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(100) DEFAULT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'replied', 'completed') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admin users
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin (password: admin123)
INSERT IGNORE INTO admin_users (username, password_hash)
VALUES ('admin', '$2y$12$ozXz9wY7OwHfRnWVPyuj1uMjuPapy1nsmW8lF0luWTc7yjbpwI6lG');
