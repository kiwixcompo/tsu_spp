-- Add id_card_generated column to profiles table
-- This tracks whether an ID card has been generated for a profile

ALTER TABLE `profiles` 
ADD COLUMN `id_card_generated` TINYINT(1) DEFAULT 0 COMMENT 'Whether ID card has been generated' AFTER `qr_code_path`,
ADD COLUMN `id_card_generated_at` DATETIME NULL COMMENT 'When ID card was generated' AFTER `id_card_generated`,
ADD COLUMN `id_card_generated_by` INT NULL COMMENT 'Admin user who generated the ID card' AFTER `id_card_generated_at`;

-- Add index for faster queries
ALTER TABLE `profiles` ADD INDEX `idx_id_card_generated` (`id_card_generated`);

-- Add foreign key for generated_by
ALTER TABLE `profiles` 
ADD CONSTRAINT `fk_id_card_generated_by` 
FOREIGN KEY (`id_card_generated_by`) REFERENCES `users`(`id`) 
ON DELETE SET NULL;
