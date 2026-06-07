<?php
namespace Opencart\Admin\Controller\Extension\IskraAccount;

class Uninstall extends \Opencart\System\Engine\Controller {
    public function index(): void {
        // 1. Delete events
        $this->load->model('setting/event');
        $this->model_setting_event->deleteEventsByCode('iskra_account');

        // 2. Delete settings
        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting('iskra_account');

        // 3. Remove column (optional — commented for safety)
        // $this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` DROP COLUMN `language_preference`");
    }
}
