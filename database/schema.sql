-- ============================================
-- M2B E-book Database Schema
-- Run this SQL to create the orders table
-- ============================================

-- Create database (if not exists)
CREATE DATABASE IF NOT EXISTS m2b_ebook
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE m2b_ebook;

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(30) NOT NULL UNIQUE,
    buyer_name VARCHAR(255) NOT NULL,
    buyer_email VARCHAR(255) NOT NULL,
    buyer_whatsapp VARCHAR(30) NOT NULL,
    buyer_city VARCHAR(100) NOT NULL,
    purchase_purpose VARCHAR(100) DEFAULT NULL,
    amount INT NOT NULL DEFAULT 49000,
    payment_status ENUM('pending', 'verified', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    notes TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_payment_status (payment_status),
    INDEX idx_created_at (created_at),
    INDEX idx_email (buyer_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
