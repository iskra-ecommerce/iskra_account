<?php
namespace Opencart\Admin\Controller\Extension\IskraAccount;

class Uninstall extends \Opencart\System\Engine\Controller {
    public function index(): void {
        $this->load->model('setting/event');
        $this->model_setting_event->deleteEventsByCode('iskra_account');

        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting('iskra_account');

        // Remove OCMOD modification
        $this->db->query("DELETE FROM `" . DB_PREFIX . "modification` WHERE `code` = 'iskra_account'");

        // Restore original template
        $original = DIR_TEMPLATE . 'account/register.twig';
        $backup = DIR_TEMPLATE . 'account/register.twig.bak';

        if (file_exists($backup)) {
            copy($backup, $original);
            unlink($backup);
        }
    }
}
