# Iskra Account / Искра Аккаунт

Modern registration form and language manager for OpenCart 4.  
Современная форма регистрации и управление языком для OpenCart 4.

[English version below / Русская версия выше]

---

## 🇷🇺 Русская версия

### Описание
Iskra Account — расширение для OpenCart 4, которое добавляет:
- Современный дизайн формы регистрации
- Индикатор надёжности пароля
- Маску телефона +7 (XXX) XXX-XX-XX
- Управление языком интерфейса
- Исправление PHP 8.4 warning

### Требования
- OpenCart 4.0+
- PHP 8.1+
- MySQL/MariaDB

### Установка

#### Способ 1: Ручная установка (рекомендуется)

1. Скопируйте папку `extension/iskra_account/` в корень вашего OpenCart
2. Выполните SQL скрипт `install.sql` через phpMyAdmin
3. Очистите кэш модификаций: **Расширения → Модификации → Обновить**
4. Проверьте страницу регистрации

#### Способ 2: Через установщик (не работает в OpenCart 4.1.0.3)

⚠️ **Внимание:** В OpenCart 4.1.0.3 установщик расширений имеет баг — модули не появляются в списке. Используйте ручную установку.

### Настройка

После установки настройки сохраняются в таблице `oc_setting` с кодом `iskra_account`:

| Параметр | Ключ | Значение по умолчанию |
|----------|------|----------------------|
| Статус | `iskra_account_status` | 1 |
| Язык по умолчанию | `iskra_account_default_language` | ru-ru |
| Время жизни cookie | `iskra_account_cookie_lifetime` | 90 |
| Индикатор пароля | `iskra_account_password_strength` | 1 |
| Маска телефона | `iskra_account_phone_mask` | 1 |
| Выбор языка | `iskra_account_language_select` | 0 |
| Мин. длина пароля | `iskra_account_password_min_length` | 8 |

### Удаление

1. Выполните SQL скрипт `uninstall.sql`
2. Удалите папку `extension/iskra_account/`
3. Очистите кэш модификаций

### Поддержка
- [GitHub Issues](https://github.com/iskra-ecommerce/iskra_account/issues)
- [Документация OpenCart](https://docs.opencart.com)

---

## 🇬🇧 English Version

### Description
Iskra Account is an OpenCart 4 extension that adds:
- Modern registration form design
- Password strength indicator
- Phone number mask +7 (XXX) XXX-XX-XX
- Language interface management
- PHP 8.4 warning fix

### Requirements
- OpenCart 4.0+
- PHP 8.1+
- MySQL/MariaDB

### Installation

#### Method 1: Manual Installation (Recommended)

1. Copy `extension/iskra_account/` folder to your OpenCart root
2. Run SQL script `install.sql` via phpMyAdmin
3. Clear modification cache: **Extensions → Modifications → Refresh**
4. Check registration page

#### Method 2: Via Installer (Not Working in OpenCart 4.1.0.3)

⚠️ **Warning:** OpenCart 4.1.0.3 has a bug — modules don't appear in the list. Use manual installation.

### Configuration

After installation, settings are stored in `oc_setting` table with code `iskra_account`:

| Parameter | Key | Default Value |
|-----------|-----|---------------|
| Status | `iskra_account_status` | 1 |
| Default Language | `iskra_account_default_language` | ru-ru |
| Cookie Lifetime | `iskra_account_cookie_lifetime` | 90 |
| Password Strength | `iskra_account_password_strength` | 1 |
| Phone Mask | `iskra_account_phone_mask` | 1 |
| Language Select | `iskra_account_language_select` | 0 |
| Min Password Length | `iskra_account_password_min_length` | 8 |

### Uninstallation

1. Run SQL script `uninstall.sql`
2. Delete `extension/iskra_account/` folder
3. Clear modification cache

### Support
- [GitHub Issues](https://github.com/iskra-ecommerce/iskra_account/issues)
- [OpenCart Documentation](https://docs.opencart.com)

---

## License / Лицензия

GNU General Public License v3.0 — [LICENSE](LICENSE)
