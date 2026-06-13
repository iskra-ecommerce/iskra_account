-- Iskra Account Uninstallation Script
-- Execute this via phpMyAdmin or MySQL CLI

-- Remove module registration
DELETE FROM `oc_extension` WHERE `type` = 'module' AND `code` = 'iskra_account';

-- Remove events
DELETE FROM `oc_event` WHERE `code` LIKE 'iskra_account%';

-- Remove settings
DELETE FROM `oc_setting` WHERE `code` = 'iskra_account';

-- Remove from extension_install (if exists)
DELETE FROM `oc_extension_install` WHERE `code` = 'iskra_account';

-- Remove modification (if exists)
DELETE FROM `oc_modification` WHERE `code` = 'iskra_account';

-- Note: Template restoration must be done manually
-- Copy backup from catalog/view/template/account/register.twig.bak to register.twig
-- Then delete the .bak file
