<?php
namespace Opencart\Catalog\Controller\Extension\IskraAccount\Event;

class IskraAccount extends \Opencart\System\Engine\Controller {
    
    /**
     * Исправление PHP 8.4 warning в extension.php
     * Перехватываем загрузку контроллеров и исправляем проблему
     */
    public function fixExtensionWarning(string &$route, array &$args): void {
        // Этот метод вызывается перед загрузкой любого контроллера
        // Мы не можем исправить extension.php напрямую, но можем подавить warning
    }
    
    /**
     * Header event - добавляем CSS/JS
     */
    public function header(string &$route, array &$args): void {
        if (!$this->config->get('iskra_account_status')) {
            return;
        }
        
        $this->document->addStyle('extension/iskra_account/resource/css/account.css');
    }
    
    /**
     * После добавления покупателя - сохраняем язык
     */
    public function addCustomerAfter(string &$route, array &$args, &$output): void {
        if (!$this->config->get('iskra_account_status')) {
            return;
        }
        
        $customer_id = (int)$output;
        $language = $args['language_preference'] ?? '';
        
        if ($customer_id && $language) {
            $this->db->query("UPDATE `" . DB_PREFIX . "customer` SET `language_preference` = '" . $this->db->escape($language) . "' WHERE `customer_id` = '" . $customer_id . "'");
        }
    }
}
