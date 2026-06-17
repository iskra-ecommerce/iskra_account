<?php
declare(strict_types=1);
namespace Opencart\Catalog\Model\Extension\IskraAccount\Module;

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

	public function getCountryExtraCurrencies(int $country_id): array {
		$query = $this->db->query("SELECT `currency_code` FROM `" . DB_PREFIX . "iskra_country_currency` WHERE `country_id` = '" . (int)$country_id . "' ORDER BY `currency_code` ASC");
		$result = [];
		foreach ($query->rows as $row) {
			$result[] = $row['currency_code'];
		}
		return $result;
	}
}