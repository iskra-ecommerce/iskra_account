<?php
namespace Opencart\System\Library\Iskra;

class AccountHelper {
    public function validateFirstName(string $value): ?string {
        $len = mb_strlen($value);
        if ($len < 1 || $len > 32) {
            return 'error_firstname';
        }
        return null;
    }

    public function validateLastName(string $value): ?string {
        $len = mb_strlen($value);
        if ($len < 1 || $len > 32) {
            return 'error_lastname';
        }
        return null;
    }

    public function validateEmail(string $value): ?string {
        if (!preg_match('/^[^\@]+@[^\@]+\.[^\@]+$/', $value)) {
            return 'error_email';
        }
        return null;
    }

    public function validateTelephone(string $value): ?string {
        $digits = preg_replace('/\D/', '', $value);
        if (mb_strlen($digits) < 10 || mb_strlen($digits) > 15) {
            return 'error_telephone';
        }
        return null;
    }

    public function formatPhone(string $value): string {
        $digits = preg_replace('/\D/', '', $value);
        if (mb_strlen($digits) === 11 && ($digits[0] === '7' || $digits[0] === '8')) {
            $digits = '7' . substr($digits, 1);
            return '+7 (' . substr($digits, 1, 3) . ') ' . substr($digits, 4, 3) . '-' . substr($digits, 7, 2) . '-' . substr($digits, 9, 2);
        }
        return $value;
    }

    public function sanitize(array $data): array {
        $allowed = ['customer_group_id', 'firstname', 'lastname', 'email', 'telephone', 'password', 'newsletter', 'agree', 'language_preference', 'custom_field'];
        $result = [];
        foreach ($allowed as $key) {
            if (isset($data[$key])) {
                $result[$key] = $data[$key];
            }
        }
        return $result;
    }
}
