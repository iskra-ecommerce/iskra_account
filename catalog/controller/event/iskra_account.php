<?php
declare(strict_types=1);
namespace Opencart\Catalog\Controller\Extension\IskraAccount\Event;

class IskraAccount extends \Opencart\System\Engine\Controller {

	public function header(string &$route, array &$args): void {
		if (!$this->config->get('iskra_account_status')) return;

		$this->document->addStyle('extension/iskra_account/resource/css/account.css');
		$this->document->addScript('extension/iskra_account/resource/js/account.js');
		$this->document->addScript('extension/iskra_account/resource/js/password-strength.js');
		$this->document->addScript('extension/iskra_account/resource/js/phone-mask.js');

		$default_country_id = (int)$this->config->get('iskra_account_default_country');
		if ($default_country_id) {
			$q = $this->db->query("SELECT `currency_code`, `language_code` FROM `" . DB_PREFIX . "iskra_country_setting` WHERE `country_id` = '" . $default_country_id . "'");
			if ($q->num_rows) {
				$extra_currencies = $this->config->get('iskra_account_extra_currencies');
				$has_extra = is_array($extra_currencies) && !empty($extra_currencies);
				if ($q->row['currency_code'] && (!$has_extra || $this->config->get('iskra_account_lock_defaults'))) {
					$this->session->data['currency'] = $q->row['currency_code'];
				}

				$extra_languages = $this->config->get('iskra_account_extra_languages');
				$has_extra_lang = is_array($extra_languages) && !empty($extra_languages);
				if ($q->row['language_code'] && (!$has_extra_lang || $this->config->get('iskra_account_lock_defaults'))) {
					$this->session->data['language'] = $q->row['language_code'];
				}
			}

			$extra_currencies = $this->config->get('iskra_account_extra_currencies');
			if (empty($extra_currencies) || !is_array($extra_currencies)) {
				$args['currency'] = '';
			}

			$extra_languages = $this->config->get('iskra_account_extra_languages');
			if (empty($extra_languages) || !is_array($extra_languages)) {
				$args['language'] = '';
			}
		}
	}

	public function viewAccountRegisterBefore(string &$route, array &$data, string &$code, string &$output): void {
		if (!$this->config->get('iskra_account_status')) return;

		$_ = [];
		$language_code = $this->config->get('config_language_catalog') ?: 'en-gb';
		$lang_file = DIR_EXTENSION . 'iskra_account/catalog/language/' . $language_code . '/module/iskra_account.php';
		if (is_file($lang_file)) require($lang_file);

		$data['iskra_phone_mask'] = (bool)$this->config->get('iskra_account_phone_mask');
		$data['iskra_password_strength'] = (bool)$this->config->get('iskra_account_password_strength');
		$data['iskra_password_min_length'] = (int)($this->config->get('iskra_account_password_min_length') ?: 8);
		$data['iskra_language_select'] = (bool)$this->config->get('iskra_account_language_select');
		$data['iskra_country_select'] = (bool)$this->config->get('iskra_account_country_select');

		$data['text_password_strength'] = $_['text_password_strength'] ?? '';
		$data['entry_language'] = $_['entry_language'] ?? '';
		$data['entry_country'] = $_['entry_country'] ?? '';
		$data['text_select'] = $_['text_select'] ?? '';

		if ($data['iskra_language_select']) {
			$this->load->model('localisation/language');
			$languages = $this->model_localisation_language->getLanguages();
			$data['iskra_languages'] = array_filter($languages, fn($l) => $l['status']);
		} else {
			$data['iskra_languages'] = [];
		}

		if ($data['iskra_country_select']) {
			$this->load->model('localisation/country');
			$countries = $this->model_localisation_country->getCountries();
			$country_settings = [];
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "iskra_country_setting`");
			foreach ($query->rows as $row) {
				$country_settings[(int)$row['country_id']] = [
					'phone_code' => $row['phone_code'],
					'currency_code' => $row['currency_code'],
					'language_code' => $row['language_code'] ?? ''
				];
			}
			$data['iskra_countries'] = $countries;
			$data['iskra_country_settings'] = $country_settings;
		} else {
			$data['iskra_countries'] = [];
			$data['iskra_country_settings'] = [];
		}

		$default_phone = '+7';
		if (!empty($data['iskra_country_settings'])) {
			$first = reset($data['iskra_country_settings']);
			if (!empty($first['phone_code'])) $default_phone = $first['phone_code'];
		}
		$data['iskra_phone_placeholder'] = $default_phone . ' (___) ___-__-__';
		$data['iskra_currency_switch_url'] = $this->url->link('extension/iskra_account/event/iskra_account.setCurrency', 'language=' . $language_code);
		$data['iskra_check_email_url'] = $this->url->link('extension/iskra_account/account.checkEmail', 'language=' . $language_code);

		$default_country_id = (int)$this->config->get('iskra_account_default_country');
		$data['iskra_default_country_id'] = $default_country_id;
		$data['iskra_lock_defaults'] = (bool)$this->config->get('iskra_account_lock_defaults');

		if ($default_country_id && isset($country_settings[$default_country_id]) && !empty($country_settings[$default_country_id]['phone_code'])) {
			$data['iskra_phone_placeholder'] = $country_settings[$default_country_id]['phone_code'] . ' (___) ___-__-__';
		}
	}

	public function addCustomerAfter(string &$route, array &$args, &$output): void {
		if (!$this->config->get('iskra_account_status')) return;
		$customer_id = (int)$output;
		if (!$customer_id) return;

		$language = $args['language_preference'] ?? '';
		$country_id = isset($args['country_preference']) ? (int)$args['country_preference'] : 0;
		$currency = $args['currency_preference'] ?? '';

		$sets = [];
		if ($language) $sets[] = "`language_preference` = '" . $this->db->escape($language) . "'";
		if ($country_id) $sets[] = "`country_preference` = '" . (int)$country_id . "'";
		if ($currency) $sets[] = "`currency_preference` = '" . $this->db->escape($currency) . "'";

		if ($sets) {
			$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET " . implode(', ', $sets) . " WHERE `customer_id` = '" . (int)$customer_id . "'");
		}
	}

	public function setCurrency(): void {
		$json = [];
		$code = $this->request->post['code'] ?? '';
		if ($code) {
			$this->load->model('localisation/currency');
			$currency_info = $this->model_localisation_currency->getCurrencyByCode($code);
			if ($currency_info) {
				$this->session->data['currency'] = $code;
				$option = ['expires' => time() + 60 * 60 * 24 * 30, 'path' => '/', 'SameSite' => 'Lax'];
				setcookie('currency', $code, $option);
				$json['success'] = true;
				$json['code'] = $code;
			} else {
				$json['error'] = 'Invalid currency code';
			}
		} else {
			$json['error'] = 'No currency code provided';
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function languageSaveAfter(string &$route, array &$args, &$output): void {
		if (!$this->config->get('iskra_account_status')) return;
		$code = $this->request->post['code'] ?? '';
		if (!$code) return;

		$this->session->data['language'] = $code;
		$lifetime = (int)($this->config->get('iskra_account_cookie_lifetime') ?: 90);
		$option = ['expires' => time() + 60 * 60 * 24 * $lifetime, 'path' => '/', 'SameSite' => 'Lax'];
		setcookie('language', $code, $option);

		if ($this->customer->isLogged()) {
			$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET `language_preference` = '" . $this->db->escape($code) . "' WHERE `customer_id` = '" . (int)$this->customer->getId() . "'");
		}
	}

	public function viewCurrencyBefore(string &$route, array &$data, string &$code, &$output): void {
		if (!$this->config->get('iskra_account_status')) return;
		$extra_currencies = $this->config->get('iskra_account_extra_currencies');
		if (empty($extra_currencies) || !is_array($extra_currencies)) return;

		$allowed = $extra_currencies;
		$default_country_id = (int)$this->config->get('iskra_account_default_country');
		if ($default_country_id) {
			$q = $this->db->query("SELECT `currency_code` FROM `" . DB_PREFIX . "iskra_country_setting` WHERE `country_id` = '" . $default_country_id . "' AND `currency_code` != ''");
			if ($q->num_rows && !in_array($q->row['currency_code'], $allowed)) {
				$allowed[] = $q->row['currency_code'];
			}
		}
		$allowed = array_unique($allowed);

		$keys = array_flip($allowed);
		$filtered = array_intersect_key($data['currencies'], $keys);
		if (!empty($filtered)) {
			$data['currencies'] = $filtered;
		}
	}

	public function viewLanguageBefore(string &$route, array &$data, string &$code, &$output): void {
		if (!$this->config->get('iskra_account_status')) return;
		$extra_languages = $this->config->get('iskra_account_extra_languages');
		if (empty($extra_languages) || !is_array($extra_languages)) return;

		$filtered = [];
		foreach ($data['languages'] as $lang) {
			if (in_array($lang['code'], $extra_languages)) {
				$filtered[] = $lang;
			}
		}

		if ($this->config->get('iskra_account_default_country')) {
			$default_country_id = (int)$this->config->get('iskra_account_default_country');
			$query = $this->db->query("SELECT `language_code` FROM `" . DB_PREFIX . "iskra_country_setting` WHERE `country_id` = '" . $default_country_id . "' AND `language_code` != ''");
			if ($query->num_rows) {
				$country_lang = $query->row['language_code'];
				$found = false;
				foreach ($filtered as $lang) {
					if ($lang['code'] === $country_lang) { $found = true; break; }
				}
				if (!$found) {
					foreach ($data['languages'] as $lang) {
						if ($lang['code'] === $country_lang) { $filtered[] = $lang; break; }
					}
				}
			}
		}

		$data['languages'] = $filtered;
	}

	public function loginAfter(string &$route, array &$args, &$output): void {
		if (!$this->config->get('iskra_account_status')) return;
		if ($this->customer->isLogged() && empty($this->session->data['language'])) {
			$this->load->model('account/customer');
			$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
			if ($customer_info && !empty($customer_info['language_preference'])) {
				$this->session->data['language'] = $customer_info['language_preference'];
			}
		}
	}
}