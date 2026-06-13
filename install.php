<?php
// Iskra Account - Installation Script

// Add language_preference column to oc_customer
$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` ADD COLUMN IF NOT EXISTS `language_preference` VARCHAR(8) DEFAULT NULL AFTER `language_id`");

// Register as module
$this->db->query("INSERT INTO `" . DB_PREFIX . "extension` SET `type` = 'module', `code` = 'iskra_account'");

// Insert default settings
$this->load->model('setting/setting');
$this->model_setting_setting->editSetting('iskra_account', [
    'iskra_account_status' => 1,
    'iskra_account_default_language' => 'ru-ru',
    'iskra_account_cookie_lifetime' => 90,
    'iskra_account_password_strength' => 1,
    'iskra_account_phone_mask' => 1,
    'iskra_account_language_select' => 0,
    'iskra_account_password_min_length' => 8
]);

// Register events
$this->load->model('setting/event');
$events = [
    [
        'code' => 'iskra_account_header',
        'trigger' => 'catalog/view/common/header/before',
        'action' => 'extension/iskra_account/event/iskra_account.header',
        'status' => 1,
        'sort_order' => 1
    ],
    [
        'code' => 'iskra_account_language_save',
        'trigger' => 'catalog/model/account/customer/addCustomer/after',
        'action' => 'extension/iskra_account/event/iskra_account.addCustomerAfter',
        'status' => 1,
        'sort_order' => 1
    ]
];
foreach ($events as $event) {
    $this->model_setting_event->addEvent($event);
}

// Backup and copy registration template
$original = DIR_TEMPLATE . 'account/register.twig';
$backup = DIR_TEMPLATE . 'account/register.twig.bak';
$new = DIR_EXTENSION . 'iskra_account/catalog/view/template/account_register.twig';

if (file_exists($original) && !file_exists($backup)) {
    copy($original, $backup);
}
if (file_exists($new)) {
    copy($new, $original);
}
