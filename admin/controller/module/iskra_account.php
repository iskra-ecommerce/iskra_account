<?php
namespace Opencart\Admin\Controller\Extension\IskraAccount\Module;

class IskraAccount extends \Opencart\System\Engine\Controller {
	public function index(): void {
		$this->load->language('extension/iskra_account/module/iskra_account');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/iskra_account/module/iskra_account', 'user_token=' . $this->session->data['user_token'])
		];

		$data['save'] = $this->url->link('extension/iskra_account/module/iskra_account.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');
		$data['country_settings'] = $this->url->link('extension/iskra_account/module/iskra_account.countrySettings', 'user_token=' . $this->session->data['user_token']);

		$data['iskra_account_status'] = $this->config->get('iskra_account_status');
		$data['iskra_account_cookie_lifetime'] = $this->config->get('iskra_account_cookie_lifetime') ?: 90;
		$data['iskra_account_password_strength'] = $this->config->get('iskra_account_password_strength');
		$data['iskra_account_phone_mask'] = $this->config->get('iskra_account_phone_mask');
		$data['iskra_account_language_select'] = $this->config->get('iskra_account_language_select');
		$data['iskra_account_country_select'] = $this->config->get('iskra_account_country_select');
		$data['iskra_account_password_min_length'] = $this->config->get('iskra_account_password_min_length') ?: 8;

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		$extra_languages = $this->config->get('iskra_account_extra_languages');
		$data['iskra_account_extra_languages'] = is_array($extra_languages) ? $extra_languages : [];

		$this->load->model('localisation/currency');
		$data['currencies'] = $this->model_localisation_currency->getCurrencies();
		$extra_currencies = $this->config->get('iskra_account_extra_currencies');
		$data['iskra_account_extra_currencies'] = is_array($extra_currencies) ? $extra_currencies : [];

		$this->load->model('localisation/country');
		$data['countries'] = $this->model_localisation_country->getCountries();

		$data['iskra_account_default_country'] = (int)$this->config->get('iskra_account_default_country');
		$data['iskra_account_lock_defaults'] = (bool)$this->config->get('iskra_account_lock_defaults');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/iskra_account/module/iskra_account', $data));
	}

	public function save(): void {
		$this->load->language('extension/iskra_account/module/iskra_account');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/iskra_account/module/iskra_account')) {
			$json['error']['warning'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('setting/setting');

			$post = $this->request->post;
			if (!isset($post['iskra_account_extra_languages']) || !is_array($post['iskra_account_extra_languages'])) {
				$post['iskra_account_extra_languages'] = [];
			}
			if (!isset($post['iskra_account_extra_currencies']) || !is_array($post['iskra_account_extra_currencies'])) {
				$post['iskra_account_extra_currencies'] = [];
			}

			$this->model_setting_setting->editSetting('iskra_account', $post);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function install(): void {
		$column_exists = $this->db->query("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '" . DB_PREFIX . "customer' AND COLUMN_NAME = 'language_preference'")->row['cnt'];
		if (!$column_exists) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` ADD COLUMN `language_preference` VARCHAR(8) DEFAULT NULL AFTER `language_id`");
		}

		$column_exists = $this->db->query("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '" . DB_PREFIX . "customer' AND COLUMN_NAME = 'country_preference'")->row['cnt'];
		if (!$column_exists) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` ADD COLUMN `country_preference` INT(11) DEFAULT NULL AFTER `language_preference`");
		}

		$column_exists = $this->db->query("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '" . DB_PREFIX . "customer' AND COLUMN_NAME = 'currency_preference'")->row['cnt'];
		if (!$column_exists) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` ADD COLUMN `currency_preference` VARCHAR(3) DEFAULT NULL AFTER `country_preference`");
		}

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "iskra_country_currency` (
			`country_id` int(11) NOT NULL,
			`currency_code` varchar(3) NOT NULL DEFAULT '',
			PRIMARY KEY (`country_id`, `currency_code`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "iskra_country_setting` (
			`country_id` int(11) NOT NULL,
			`phone_code` varchar(10) NOT NULL DEFAULT '',
			`currency_code` varchar(3) NOT NULL DEFAULT '',
			`language_code` varchar(8) NOT NULL DEFAULT '',
			PRIMARY KEY (`country_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

		$column_exists = $this->db->query("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '" . DB_PREFIX . "iskra_country_setting' AND COLUMN_NAME = 'language_code'")->row['cnt'];
		if (!$column_exists) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "iskra_country_setting` ADD COLUMN `language_code` VARCHAR(8) NOT NULL DEFAULT '' AFTER `currency_code`");
		}

		$this->load->model('setting/setting');
		$this->model_setting_setting->editSetting('iskra_account', [
			'iskra_account_status' => 1,
			'iskra_account_extra_languages' => [],
			'iskra_account_extra_currencies' => [],
			'iskra_account_cookie_lifetime' => 90,
			'iskra_account_password_strength' => 1,
			'iskra_account_phone_mask' => 1,
			'iskra_account_language_select' => 0,
			'iskra_account_country_select' => 0,
			'iskra_account_password_min_length' => 8,
			'iskra_account_default_country' => 0,
			'iskra_account_lock_defaults' => 0
		]);

		$default_country_id = (int)$this->config->get('iskra_account_default_country');
		if ($default_country_id) {
			$q = $this->db->query("SELECT `currency_code` FROM `" . DB_PREFIX . "iskra_country_setting` WHERE `country_id` = '" . $default_country_id . "' AND `currency_code` != ''");
			if ($q->num_rows) {
				$this->model_setting_setting->editValue('config', 'config_currency', $q->row['currency_code']);
			}
		}

		$this->load->model('setting/event');
		$events = [
			['code' => 'iskra_account_header', 'description' => 'Iskra Account - add CSS/JS to header', 'trigger' => 'catalog/view/common/header/before', 'action' => 'extension/iskra_account/event/iskra_account.header', 'status' => 1, 'sort_order' => 1],
			['code' => 'iskra_account_register_vars', 'description' => 'Iskra Account - inject variables into register page', 'trigger' => 'catalog/view/account/register/before', 'action' => 'extension/iskra_account/event/iskra_account.viewAccountRegisterBefore', 'status' => 1, 'sort_order' => 0],
			['code' => 'iskra_account_language_save', 'description' => 'Iskra Account - save language preference on registration', 'trigger' => 'catalog/model/account/customer/addCustomer/after', 'action' => 'extension/iskra_account/event/iskra_account.addCustomerAfter', 'status' => 1, 'sort_order' => 1],
			['code' => 'iskra_account_language_persist', 'description' => 'Iskra Account - save language to session/cookie/profile', 'trigger' => 'controller/common/language/save/after', 'action' => 'extension/iskra_account/event/iskra_account.languageSaveAfter', 'status' => 1, 'sort_order' => 0],
			['code' => 'iskra_account_login', 'description' => 'Iskra Account - set session language from profile on login', 'trigger' => 'controller/common/header/before', 'action' => 'extension/iskra_account/event/iskra_account.loginAfter', 'status' => 1, 'sort_order' => 0],
			['code' => 'iskra_account_currency_filter', 'description' => 'Iskra Account - filter header currencies list', 'trigger' => 'catalog/view/common/currency/before', 'action' => 'extension/iskra_account/event/iskra_account.viewCurrencyBefore', 'status' => 1, 'sort_order' => 0],
			['code' => 'iskra_account_language_filter', 'description' => 'Iskra Account - filter header languages list', 'trigger' => 'catalog/view/common/language/before', 'action' => 'extension/iskra_account/event/iskra_account.viewLanguageBefore', 'status' => 1, 'sort_order' => 0],
		];
		foreach ($events as $event) {
			$this->model_setting_event->addEvent($event);
		}

		$this->load->model('setting/modification');
		$existing = $this->model_setting_modification->getModificationByCode('iskra_account');
		if (empty($existing)) {
			$ocmod_xml_file = DIR_EXTENSION . 'iskra_account/ocmod/iskra_account.ocmod.xml';
			if (is_file($ocmod_xml_file)) {
				$xml_content = file_get_contents($ocmod_xml_file);
				$this->model_setting_modification->addModification([
					'extension_install_id' => 0,
					'name' => 'Iskra Account',
					'description' => 'Модуль регистрации на базе Iskra: страна, язык, маска телефона, блокировка дефолтов.',
					'code' => 'iskra_account',
					'author' => 'Iskra Team',
					'version' => '2.4.0',
					'link' => '',
					'xml' => $xml_content,
					'status' => 1
				]);
			}
		}
	}

	public function uninstall(): void {
		$this->load->model('setting/event');
		$this->model_setting_event->deleteEventByCode('iskra_account_header');
		$this->model_setting_event->deleteEventByCode('iskra_account_register_vars');
		$this->model_setting_event->deleteEventByCode('iskra_account_language_save');
		$this->model_setting_event->deleteEventByCode('iskra_account_language_persist');
		$this->model_setting_event->deleteEventByCode('iskra_account_login');
		$this->model_setting_event->deleteEventByCode('iskra_account_currency_filter');
		$this->model_setting_event->deleteEventByCode('iskra_account_language_filter');

		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('iskra_account');

		$this->load->model('setting/modification');
		$modification = $this->model_setting_modification->getModificationByCode('iskra_account');
		if (!empty($modification)) {
			$this->model_setting_modification->deleteModification((int)$modification['modification_id']);
		}

		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "iskra_country_currency`");
	}

	public function countrySettings(): void {
		$this->load->language('extension/iskra_account/module/iskra_account_country');
		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];
		$data['breadcrumbs'][] = ['text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])];
		$data['breadcrumbs'][] = ['text' => $this->language->get('text_extension'), 'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module')];
		$data['breadcrumbs'][] = ['text' => $this->language->get('heading_title'), 'href' => $this->url->link('extension/iskra_account/module/iskra_account.countrySettings', 'user_token=' . $this->session->data['user_token'])];

		$data['save'] = $this->url->link('extension/iskra_account/module/iskra_account.countrySettingsSave', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('extension/iskra_account/module/iskra_account', 'user_token=' . $this->session->data['user_token']);

		$this->load->model('localisation/country');
		$countries = $this->model_localisation_country->getCountries();

		$this->load->model('extension/iskra_account/module/iskra_account');
		$settings = $this->model_extension_iskra_account_module_iskra_account->getCountrySettings();

		$this->load->model('localisation/currency');
		$currencies = $this->model_localisation_currency->getCurrencies();

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['countries'] = [];
		foreach ($countries as $country) {
			$country_id = (int)$country['country_id'];
			$data['countries'][] = [
				'country_id' => $country_id,
				'name' => $country['name'],
				'iso_code_2' => $country['iso_code_2'],
				'phone_code' => $settings[$country_id]['phone_code'] ?? '',
				'currency_code' => $settings[$country_id]['currency_code'] ?? '',
				'language_code' => $settings[$country_id]['language_code'] ?? '',
				'extra_currencies' => $this->model_extension_iskra_account_module_iskra_account->getCountryExtraCurrencies($country_id)
			];
		}

		$data['currencies'] = $currencies;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/iskra_account/module/iskra_account_country', $data));
	}

	public function countrySettingsSave(): void {
		$this->load->language('extension/iskra_account/module/iskra_account_country');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/iskra_account/module/iskra_account')) {
			$json['error']['warning'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('extension/iskra_account/module/iskra_account');

			$countries = $this->request->post['country'] ?? [];
			foreach ($countries as $country_id => $data) {
				$phone_code = preg_replace('/[^0-9+]/', '', $data['phone_code'] ?? '');
				$currency_code = strtoupper(preg_replace('/[^a-zA-Z]/', '', $data['currency_code'] ?? ''));
				$language_code = strtolower(preg_replace('/[^a-zA-Z_-]/', '', $data['language_code'] ?? ''));
				$this->model_extension_iskra_account_module_iskra_account->editCountrySetting((int)$country_id, $phone_code, $currency_code, $language_code);

				$extra_currencies = $data['extra_currencies'] ?? [];
				if (is_array($extra_currencies)) {
					$this->model_extension_iskra_account_module_iskra_account->editCountryExtraCurrencies((int)$country_id, $extra_currencies);
				}

				if ($currency_code) {
					$this->load->model('setting/setting');
					$this->model_setting_setting->editValue('config', 'config_currency', $currency_code);
				}
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
