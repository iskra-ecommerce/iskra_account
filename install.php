<?php
namespace Opencart\Admin\Controller\Extension\IskraAccount;

class Install extends \Opencart\System\Engine\Controller {
    public function index(): void {
        $this->load->language('extension/iskra/account');

        // 1. Add language_preference column to oc_customer
        $this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` ADD COLUMN `language_preference` VARCHAR(8) DEFAULT NULL AFTER `language_id`");

        // 2. Insert default settings
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('iskra_account', [
            'iskra_account_default_language' => 'ru-ru',
            'iskra_account_cookie_lifetime' => 90,
            'iskra_account_password_strength' => 1,
            'iskra_account_phone_mask' => 1,
            'iskra_account_language_select' => 1,
            'iskra_account_password_min_length' => 8,
            'iskra_account_status' => 1
        ]);

        // 3. Register events
        $this->load->model('setting/event');
        $events = [
            [
                'code' => 'iskra_account_register',
                'trigger' => 'catalog/view/account/register/before',
                'action' => 'extension/iskra/account.register',
                'status' => 1,
                'sort_order' => 1
            ],
            [
                'code' => 'iskra_account_header',
                'trigger' => 'catalog/view/common/header/before',
                'action' => 'extension/iskra/account.header',
                'status' => 1,
                'sort_order' => 1
            ],
            [
                'code' => 'iskra_account_language_save',
                'trigger' => 'catalog/model/account/customer/addCustomer/after',
                'action' => 'extension/iskra/account.addCustomerAfter',
                'status' => 1,
                'sort_order' => 1
            ],
            [
                'code' => 'iskra_account_language_login',
                'trigger' => 'catalog/model/account/customer/editCustomer/after',
                'action' => 'extension/iskra/account.editCustomerAfter',
                'status' => 1,
                'sort_order' => 1
            ]
        ];
        foreach ($events as $event) {
            $this->model_setting_event->addEvent($event);
        }
    }

    public function uninstall(): void {
        $this->load->model('setting/event');
        $this->model_setting_event->deleteEventsByCode('iskra_account');
    }
}
