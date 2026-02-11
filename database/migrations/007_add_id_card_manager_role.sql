-- ============================================================================
-- Add ID Card Manager Role
-- ============================================================================
-- This migration adds a new 'id_card_manager' role to the system
-- ID Card Managers can only access ID card printing and management features
-- ============================================================================

-- Modify users table to add new role
ALTER TABLE users 
MODIFY COLUMN role ENUM('user', 'admin', 'id_card_manager') 
COLLATE utf8mb4_unicode_ci DEFAULT 'user';

-- Create id_card_print_logs table to track printing activities
CREATE TABLE IF NOT EXISTS `id_card_print_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'User who printed the card',
  `profile_id` int(11) NOT NULL COMMENT 'Profile whose card was printed',
  `print_type` enum('single','bulk') COLLATE utf8mb4_unicode_ci DEFAULT 'single',
  `print_format` enum('pdf','preview') COLLATE utf8mb4_unicode_ci DEFAULT 'pdf',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_profile_id` (`profile_id`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `id_card_print_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `id_card_print_logs_ibfk_2` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create id_card_settings table for customization
CREATE TABLE IF NOT EXISTS `id_card_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `setting_type` enum('text','number','boolean','json') COLLATE utf8mb4_unicode_ci DEFAULT 'text',
  `description` text COLLATE utf8mb4_unicode_ci,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO id_card_settings (setting_key, setting_value, setting_type, description) VALUES
('card_template_version', '1.0', 'text', 'Current ID card template version'),
('enable_bulk_printing', '1', 'boolean', 'Allow bulk printing of ID cards'),
('max_bulk_print_count', '50', 'number', 'Maximum number of cards in bulk print'),
('require_approval', '0', 'boolean', 'Require admin approval before printing'),
('watermark_enabled', '0', 'boolean', 'Add watermark to ID cards')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
