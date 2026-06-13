<?php
namespace Opencart\Catalog\Controller\Extension\IskraAccount\Event;

class IskraAccount extends \Opencart\System\Engine\Controller {
    public function register(string &$route, array &$args): void {
        if (!$this->config->get('iskra_account_status')) {
            return;
        }

        $this->load->model('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();
        $active = array_filter($languages, fn($l) => $l['status']);

        $flagMap = [
            'en-gb' => '🇬',
            'ru-ru' => '🇺',
            'uk-ua' => '🇺🇦',
            'kk-kz' => '🇰🇿',
            'be-by' => '🇧🇾',
            'ro-ro' => '🇷🇴'
        ];

        foreach ($active as &$lang) {
            $lang['flag_emoji'] = $flagMap[$lang['code']] ?? '🌐';
        }

        $cookie_lang = $this->request->cookie['iskra_language'] ?? '';
        $default_lang = $this->config->get('iskra_account_default_language') ?: 'ru-ru';

        $args['iskra_languages'] = $active;
        $args['iskra_selected_language'] = $cookie_lang ?: $default_lang;
        $args['iskra_password_strength'] = (bool)$this->config->get('iskra_account_password_strength');
        $args['iskra_phone_mask'] = (bool)$this->config->get('iskra_account_phone_mask');
        $args['iskra_language_select'] = (bool)$this->config->get('iskra_account_language_select');
        $args['iskra_password_min_length'] = (int)($this->config->get('iskra_account_password_min_length') ?: 8);
    }

    public function header(string &$route, array &$args): void {
        if (!$this->config->get('iskra_account_status')) {
            return;
        }

        $this->document->addStyle('extension/iskra_account/resource/css/account.css');
        $this->document->addScript('extension/iskra_account/resource/js/account.js');
        $this->document->addScript('extension/iskra_account/resource/js/password-strength.js');
        $this->document->addScript('extension/iskra_account/resource/js/phone-mask.js');
    }

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

    public function editCustomerAfter(string &$route, array &$args, &$output): void {
        if (!$this->config->get('iskra_account_status')) {
            return;
        }

        $customer_id = (int)($args['customer_id'] ?? 0);
        $language = $args['language_preference'] ?? '';

        if ($customer_id && $language) {
            $this->db->query("UPDATE `" . DB_PREFIX . "customer` SET `language_preference` = '" . $this->db->escape($language) . "' WHERE `customer_id` = '" . $customer_id . "'");
        }
    }
}
