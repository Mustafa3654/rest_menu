-- Upgrade script for security/auth and settings/contact schema alignment.
-- Run this on the same database used by the application.

START TRANSACTION;

-- Password hashes require wider storage than the legacy varchar(20).
ALTER TABLE `users`
  MODIFY COLUMN `userpassword` VARCHAR(255) NOT NULL;

-- Add missing settings columns only if they do not already exist.
DROP PROCEDURE IF EXISTS add_column_if_missing;
DELIMITER $$
CREATE PROCEDURE add_column_if_missing(
    IN p_table VARCHAR(64),
    IN p_column VARCHAR(64),
    IN p_definition TEXT
)
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = p_table
          AND COLUMN_NAME = p_column
    ) THEN
        SET @sql = CONCAT(
            'ALTER TABLE `', p_table, '` ADD COLUMN `', p_column, '` ', p_definition
        );
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$
DELIMITER ;

CALL add_column_if_missing('settings', 'home_bg', 'VARCHAR(255) DEFAULT NULL');
CALL add_column_if_missing('settings', 'menu_bg', 'VARCHAR(255) DEFAULT NULL');
CALL add_column_if_missing('settings', 'contact_bg', 'VARCHAR(255) DEFAULT NULL');
CALL add_column_if_missing('settings', 'restaurant_maps', 'TEXT DEFAULT NULL');
CALL add_column_if_missing('settings', 'opening_title', 'VARCHAR(255) DEFAULT NULL');
CALL add_column_if_missing('settings', 'chat_id', 'VARCHAR(255) DEFAULT NULL');
CALL add_column_if_missing('settings', 'bot_token', 'VARCHAR(255) DEFAULT NULL');
CALL add_column_if_missing('settings', 'exchange_rate', 'DECIMAL(12,2) NOT NULL DEFAULT 90000.00');
CALL add_column_if_missing('settings', 'display_currency', 'ENUM(''LBP'',''USD'',''BOTH'') NOT NULL DEFAULT ''LBP''');

DROP PROCEDURE IF EXISTS add_column_if_missing;

-- Contact form storage table used by contact.php.
CREATE TABLE IF NOT EXISTS `contact_submissions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
