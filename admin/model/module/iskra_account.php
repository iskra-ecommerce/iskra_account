<?php
declare(strict_types=1);
namespace Opencart\Admin\Model\Extension\IskraAccount\Module;

class IskraAccount extends \Opencart\System\Engine\Model {
	public function getCountrySettings(): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "iskra_country_setting`");
		$result = [];
		foreach ($query->rows as $row) {
			$result[(int)$row['country_id']] = [
				'phone_code' => $row['phone_code'],
				'currency_code' => $row['currency_code'],
				'language_code' => $row['language_code'] ?? ''
			];
		}
		return $result;
	}

	public function editCountrySetting(int $country_id, string $phone_code, string $currency_code, string $language_code = ''): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "iskra_country_setting` WHERE `country_id` = '" . (int)$country_id . "'");
		if ($phone_code !== '' || $currency_code !== '' || $language_code !== '') {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "iskra_country_setting` SET
				`country_id` = '" . (int)$country_id . "',
				`phone_code` = '" . $this->db->escape($phone_code) . "',
				`currency_code` = '" . $this->db->escape($currency_code) . "',
				`language_code` = '" . $this->db->escape($language_code) . "'");
		}
	}

	public function getCountryExtraCurrencies(int $country_id): array {
		$query = $this->db->query("SELECT `currency_code` FROM `" . DB_PREFIX . "iskra_country_currency` WHERE `country_id` = '" . (int)$country_id . "' ORDER BY `currency_code` ASC");
		$result = [];
		foreach ($query->rows as $row) {
			$result[] = $row['currency_code'];
		}
		return $result;
	}

	public function editCountryExtraCurrencies(int $country_id, array $codes): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "iskra_country_currency` WHERE `country_id` = '" . (int)$country_id . "'");
		$codes = array_unique(array_filter(array_map('strtoupper', $codes)));
		foreach ($codes as $code) {
			if (preg_match('/^[A-Z]{3}$/', $code)) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "iskra_country_currency` SET
					`country_id` = '" . (int)$country_id . "',
					`currency_code` = '" . $this->db->escape($code) . "'");
			}
		}
	}
}