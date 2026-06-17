# Iskra Account — расширение регистрации для OpenCart 4

[English version below]

---

## 🇷🇺 Русский

### Возможности

- **Выбор страны** на форме регистрации — телефонный код, валюта и язык подставляются автоматически
- **Основная валюта и язык для страны** — задаёте в админке, расширение само меняет базовую валюту магазина
- **Дополнительные валюты** — выбираете, какие валюты показывать в шапке (EUR, USD, UAH, и т.д.)
- **Дополнительные языки** — выбираете, какие языки показывать в шапке (EN, RU, RO, и т.д.)
- **Автоматический пересчёт** — при выборе другой валюты цены пересчитываются по курсу
- **Текст вместо флагов** — в шапке коды языков (RU, EN) и валют (USD, EUR) вместо картинок
- **Единый стиль** — селекторы языка и валюты выглядят одинаково
- **Блокировка дефолтов** — можно заблокировать выбор страны, языка и валюты под одну страну
- **Индикатор надёжности пароля** — визуальная подсказка при регистрации
- **Маска телефона** — автоматическое форматирование с кодом страны
- **Сохранение языка** — язык запоминается в cookie, сессии и профиле пользователя

### Требования

- OpenCart 4.1+
- PHP 8.4+

### Установка

#### Способ 1: Через установщик OpenCart (рекомендуется)

1. Скачайте последний релиз `iskra_account-2.4.0.ocmod.zip`
2. Зайдите в админку: **Расширения → Установщик → Загрузить**
3. Выберите скачанный ZIP-файл
4. Перейдите: **Расширения → Модификации → Обновить**
5. Перейдите: **Расширения → Расширения → Модули**
6. Найдите **Iskra Account** и нажмите **Установить**
7. После установки нажмите **Редактировать** для настройки

#### Способ 2: Вручную

1. Скопируйте папку `extension/iskra_account/` в корень вашего OpenCart
2. Зайдите в админку: **Расширения → Модификации → Обновить**
3. **Расширения → Расширения → Модули → Iskra Account → Установить**

### Настройка

После установки откройте **Расширения → Расширения → Модули → Iskra Account → Редактировать**.

**Основные настройки:**
- **Статус** — включить/выключить модуль
- **Дополнительные языки сайта** — отметьте, какие языки будут доступны в шапке
- **Дополнительные валюты сайта** — отметьте, какие валюты будут доступны в шапке
- **Страна по умолчанию** — страна, которая будет предвыбрана на регистрации
- **Заблокировать дефолты** — если включено, язык, валюта и страна блокируются для изменения
- **Выбор языка при регистрации** — добавить поле выбора языка на форму
- **Выбор страны при регистрации** — добавить поле выбора страны
- **Маска телефона** — автоформатирование с кодом страны
- **Индикатор пароля** — визуальная подсказка надёжности пароля

**Настройки стран:**
Нажмите **Настроить страны**. Для каждой страны можно задать:
- **Код телефона** (например, +373 для Молдовы)
- **Основной язык** (например, ro-ro для Молдовы)
- **Основная валюта** (например, MDL для Молдовы)
- **Дополнительные валюты** (мультиселект)

### Как это работает

1. Вы заходите в **Настройки стран**, выбираете Молдова, ставите язык `ro-ro`, валюту `MDL`
2. Нажимаете **Сохранить** → `config_currency` магазина автоматически становится `MDL`
3. В **Дополнительные языки** отмечаете `en-gb`, `ru-ru`
4. В **Дополнительные валюты** отмечаете `EUR`, `USD`
5. На витрине: язык переключается на EN/RU/RO, валюта — на MDL/EUR/USD
6. Если убрать все дополнительные валюты — селектор скрывается, валюта принудительно ставится MDL

### Удаление

1. **Расширения → Расширения → Модули → Iskra Account → Удалить**
2. Удалите папку `extension/iskra_account/`
3. **Расширения → Модификации → Обновить**

### История версий

См. [CHANGELOG.md](CHANGELOG.md)

### Лицензия

GNU General Public License v3.0

---

## 🇬🇧 English

### Features

- **Country selection** on registration form — auto-fills phone code, currency and language
- **Default currency & language per country** — set in admin, extension auto-updates store's base currency
- **Extra currencies** — choose which currencies to show in header (EUR, USD, UAH, etc.)
- **Extra languages** — choose which languages to show in header (EN, RU, RO, etc.)
- **Auto price conversion** — prices recalculate when switching currencies
- **Text instead of flags** — language codes (RU, EN) and currency codes (USD, EUR) instead of images
- **Unified style** — language and currency selectors look identical
- **Lock defaults** — lock country, language and currency to one specific country
- **Password strength indicator** — visual hint on registration form
- **Phone mask** — auto-formatting with country code
- **Language persistence** — language saved in cookie, session and user profile

### Requirements

- OpenCart 4.1+
- PHP 8.4+

### Installation

#### Method 1: OpenCart Installer (recommended)

1. Download latest release `iskra_account-2.4.0.ocmod.zip`
2. Admin: **Extensions → Installer → Upload**
3. Select the ZIP file
4. **Extensions → Modifications → Refresh**
5. **Extensions → Extensions → Modules**
6. Find **Iskra Account**, click **Install**
7. Click **Edit** to configure

#### Method 2: Manual

1. Copy `extension/iskra_account/` to your OpenCart root
2. Admin: **Extensions → Modifications → Refresh**
3. **Extensions → Extensions → Modules → Iskra Account → Install**

### Configuration

Admin: **Extensions → Extensions → Modules → Iskra Account → Edit**.

**Main settings:**
- **Status** — enable/disable module
- **Extra Site Languages** — check languages to show in header
- **Extra Site Currencies** — check currencies to show in header
- **Default Country** — country pre-selected on registration
- **Lock Defaults** — when enabled, country/language/currency are locked
- **Language Selection on Registration** — show language picker
- **Country Selection on Registration** — show country picker
- **Phone Mask** — auto-format with country code
- **Password Strength** — visual indicator

**Country settings:**
Click **Configure Countries**. For each country:
- **Phone Code** (e.g., +373 for Moldova)
- **Default Language** (e.g., ro-ro for Moldova)
- **Default Currency** (e.g., MDL for Moldova)
- **Extra Currencies** (multi-select)

### How it works

1. Go to **Country Settings**, set Moldova with language `ro-ro`, currency `MDL`
2. Click **Save** → store's `config_currency` auto-changes to `MDL`
3. In **Extra Languages** check `en-gb`, `ru-ru`
4. In **Extra Currencies** check `EUR`, `USD`
5. Frontend shows language switcher with EN/RU/RO, currency with MDL/EUR/USD
6. Remove all extra currencies → selector hides, currency forced to MDL

### Changelog

See [CHANGELOG.md](CHANGELOG.md)

### License

GNU General Public License v3.0
