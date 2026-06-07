<?php
namespace Opencart\System\Library\Iskra;

class LanguageManager {
    private object $config;
    private object $request;
    private object $db;
    private object $customer;
    private object $session;

    public function __construct(object $registry) {
        $this->config = $registry->get('config');
        $this->request = $registry->get('request');
        $this->db = $registry->get('db');
        $this->customer = $registry->get('customer');
        $this->session = $registry->get('session');
    }

    public function detect(): string {
        // 1. Logged in — from DB
        if ($this->customer->isLogged()) {
            $customer_id = $this->customer->getId();
            $query = $this->db->query("SELECT `language_preference` FROM `" . DB_PREFIX . "customer` WHERE `customer_id` = '" . (int)$customer_id . "'");
            if ($query->num_rows && $query->row['language_preference']) {
                return $query->row['language_preference'];
            }
        }

        // 2. Guest with cookie
        if (isset($this->request->cookie['iskra_language'])) {
            return $this->request->cookie['iskra_language'];
        }

        // 3. Default from admin settings
        return $this->config->get('iskra_account_default_language') ?: 'ru-ru';
    }

    public function set(string $code, int $lifetimeDays = 90): void {
        $expire = time() + ($lifetimeDays * 86400);
        setcookie('iskra_language', $code, $expire, '/', '', false, true);
        $_COOKIE['iskra_language'] = $code;
    }

    public function setForCustomer(int $customer_id, string $code): void {
        $this->db->query("UPDATE `" . DB_PREFIX . "customer` SET `language_preference` = '" . $this->db->escape($code) . "' WHERE `customer_id` = '" . (int)$customer_id . "'");
        $this->set($code);
    }

    public function get(string $code): string {
        static $languages = null;
        if ($languages === null) {
            $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "language` WHERE `status` = '1'");
            $languages = $query->rows;
        }
        foreach ($languages as $lang) {
            if ($lang['code'] === $code) {
                return $lang['language_id'];
            }
        }
        $first = reset($languages);
        return $first['language_id'] ?? '1';
    }
}
