<?php
namespace Opencart\System\Library\Iskra;

class PasswordStrength {
    public function check(string $password): array {
        $score = 0;
        $requirements = [];

        // Length
        $len = mb_strlen($password);
        if ($len >= 8) $score += 20;
        elseif ($len >= 6) $score += 10;
        if ($len >= 12) $score += 10;
        if ($len >= 16) $score += 5;

        // Lowercase
        if (preg_match('/[a-zа-яё]/u', $password)) {
            $score += 15;
        } else {
            $requirements[] = 'lowercase';
        }

        // Uppercase
        if (preg_match('/[A-ZА-ЯЁ]/u', $password)) {
            $score += 15;
        } else {
            $requirements[] = 'uppercase';
        }

        // Numbers
        if (preg_match('/[0-9]/', $password)) {
            $score += 15;
        } else {
            $requirements[] = 'number';
        }

        // Symbols
        if (preg_match('/[^a-zA-Zа-яА-ЯёЁ0-9]/u', $password)) {
            $score += 20;
        } else {
            $requirements[] = 'symbol';
        }

        $score = min(100, $score);

        $level = 'weak';
        if ($score >= 80) $level = 'very-strong';
        elseif ($score >= 60) $level = 'strong';
        elseif ($score >= 40) $level = 'medium';

        return [
            'score' => $score,
            'level' => $level,
            'requirements' => $requirements
        ];
    }
}
