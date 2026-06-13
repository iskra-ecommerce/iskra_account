-- Iskra Account Installation Script
-- Execute this via phpMyAdmin or MySQL CLI

-- Add language_preference column to oc_customer
ALTER TABLE `oc_customer` ADD COLUMN IF NOT EXISTS `language_preference` VARCHAR(8) DEFAULT NULL AFTER `language_id`;

-- Register as module
INSERT INTO `oc_extension` (`type`, `code`) VALUES ('module', 'iskra_account');

-- Insert default settings
INSERT INTO `oc_setting` (`store_id`, `code`, `key`, `value`) VALUES
(0, 'iskra_account', 'iskra_account_status', '1'),
(0, 'iskra_account', 'iskra_account_default_language', 'ru-ru'),
(0, 'iskra_account', 'iskra_account_cookie_lifetime', '90'),
(0, 'iskra_account', 'iskra_account_password_strength', '1'),
(0, 'iskra_account', 'iskra_account_phone_mask', '1'),
(0, 'iskra_account', 'iskra_account_language_select', '0'),
(0, 'iskra_account', 'iskra_account_password_min_length', '8')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

-- Register events
INSERT INTO `oc_event` (`code`, `trigger`, `action`, `status`, `sort_order`) VALUES
('iskra_account_header', 'catalog/view/common/header/before', 'extension/iskra_account/event/iskra_account.header', 1, 0),
('iskra_account_language_save', 'catalog/model/account/customer/addCustomer/after', 'extension/iskra_account/event/iskra_account.addCustomerAfter', 1, 0)
ON DUPLICATE KEY UPDATE `action` = VALUES(`action`);

-- Note: Template backup and copy must be done manually via PHP script
-- Run: php manual_register.php
