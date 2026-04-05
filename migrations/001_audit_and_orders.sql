-- Migration 001: Audit Log + Orders Tables
-- Run this once in phpMyAdmin or via command line

-- -------------------------
-- Audit Log Table
-- -------------------------
CREATE TABLE IF NOT EXISTS audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    action VARCHAR(50) NOT NULL COMMENT 'create, update, delete, login',
    entity VARCHAR(50) NOT NULL COMMENT 'item, category, settings, etc.',
    entity_id INT DEFAULT NULL,
    details TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_entity (entity),
    INDEX idx_action (action),
    INDEX idx_username (username),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------
-- Orders Table (WhatsApp checkout history)
-- -------------------------
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) DEFAULT NULL,
    total_lbp BIGINT DEFAULT 0,
    total_usd DECIMAL(10,2) DEFAULT 0.00,
    whatsapp_number VARCHAR(20) DEFAULT NULL,
    items_json TEXT NOT NULL COMMENT 'JSON array of order items',
    status ENUM('pending','sent','failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------
-- Indexes for performance (existing tables)
-- -------------------------
-- Items table: speed up category lookups and search
ALTER TABLE items ADD INDEX IF NOT EXISTS idx_items_category (item_category);
ALTER TABLE items ADD INDEX IF NOT EXISTS idx_items_name (item_name);

-- Categories table: speed up ordering
ALTER TABLE categories ADD INDEX IF NOT EXISTS idx_categories_order (`Order`);

-- Contact submissions: speed up time-based queries
ALTER TABLE contact_submissions ADD INDEX IF NOT EXISTS idx_contact_created (created_at);
