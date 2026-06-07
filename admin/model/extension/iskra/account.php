<?php
namespace Opencart\Admin\Model\Extension\Iskra;

class Account extends \Opencart\System\Engine\Model {
    public function install(): void {
        $this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` ADD COLUMN `language_preference` VARCHAR(8) DEFAULT NULL AFTER `language_id`");
    }

    public function uninstall(): void {
        // Keep column data on uninstall for safety
        // $this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` DROP COLUMN `language_preference`");
    }
}
