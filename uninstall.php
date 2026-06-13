<?php
// Iskra Account - Uninstallation Script

// Remove module registration
$this->db->query("DELETE FROM `" . DB_PREFIX . "extension` WHERE `type` = 'module' AND `code` = 'iskra_account'");

// Remove events
$this->load->model('setting/event');
$this->model_setting_event->deleteEventsByCode('iskra_account');

// Remove settings
$this->load->model('setting/setting');
$this->model_setting_setting->deleteSetting('iskra_account');

// Restore original template
$original = DIR_TEMPLATE . 'account/register.twig';
$backup = DIR_TEMPLATE . 'account/register.twig.bak';

if (file_exists($backup)) {
    copy($backup, $original);
    unlink($backup);
}
