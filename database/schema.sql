-- M2B E-book Database Schema
-- Synced with PHP codebase (May 2026)

CREATE DATABASE IF NOT EXISTS m2b_ebook CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE m2b_ebook;

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) UNIQUE NOT NULL,
    buyer_name VARCHAR(255) NOT NULL,
    buyer_email VARCHAR(255) NOT NULL,
    buyer_whatsapp VARCHAR(50) NOT NULL,
    buyer_city VARCHAR(100) NOT NULL,
    buyer_purpose TEXT,
    ebook_lang VARCHAR(5) DEFAULT 'id',
    payment_status ENUM('pending', 'verified', 'failed') DEFAULT 'pending',
    ebook_url TEXT,
    watermark_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    email_sent TINYINT(1) DEFAULT 0,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    verified_at TIMESTAMP NULL,
    INDEX idx_order_id (order_id),
    INDEX idx_payment_status (payment_status),
    INDEX idx_email (buyer_email),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
