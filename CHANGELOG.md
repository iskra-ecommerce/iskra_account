# Changelog / История версий

---

## 2.4.0 (2026-06-17)

### Русский
- **Дополнительные языки сайта** — выбор языков для показа в шапке, скрытие если не выбраны
- **Дополнительные валюты сайта** — выбор валют для показа в шапке, скрытие если не выбраны
- **Авто-базовая валюта** — при сохранении настроек страны, config_currency автоматически меняется на валюту страны
- **Текст вместо флагов** — коды языков (RU, EN, RO) и валют (USD, EUR, MDL) вместо картинок и символов
- **Единый стиль** — селекторы языка и валюты выглядят одинаково (.iskra-lang-code)
- **Принудительная установка валюты** — при отсутствии доп. валют, валюта принудительно ставится из страны
- **Удалён мёртвый OCMOD патч** — startup/language.php не работал для pre-actions, логика перенесена в событие header()
- **Исправлен баг** — foreach + ссылка в viewCurrencyBefore() заменён на array_intersect_key()

### English
- **Extra Site Languages** — choose which languages to show in header, hides if none selected
- **Extra Site Currencies** — choose which currencies to show in header, hides if none selected
- **Auto base currency** — saving country settings auto-updates config_currency
- **Text instead of flags** — language codes (RU, EN, RO) and currency codes (USD, EUR, MDL) instead of images and symbols
- **Unified style** — language and currency selectors now share .iskra-lang-code styling
- **Forced currency fallback** — when no extra currencies, country currency is forced
- **Removed dead OCMOD patch** — startup/language.php couldn't work for pre-actions, logic moved to header() event
- **Bugfix** — foreach + reference in viewCurrencyBefore() replaced with array_intersect_key()

---

## 2.3.0 (2026-06-15)

### Русский
- **Дополнительные валюты для страны** — мультиселект в настройках стран
- **Фильтрация валют в шапке** — показываются только разрешённые валюты
- **Блокировка дефолтов** — lock_defaults сбрасывает язык и валюту на дефолт страны
- **Фильтрация через view/*/before** — замена OCMOD-патчей контроллеров на event-based подход
- **Событие валюты** — view/common/currency/before для фильтрации списка

### English
- **Extra currencies per country** — multi-select in country settings
- **Header currency filtering** — only allowed currencies shown
- **Lock defaults** — lock_defaults forces country language/currency
- **Event-based filtering** — replaced OCMOD controller patches with view/*/before events
- **Currency event** — view/common/currency/before for list filtering

---

## 2.2.0 (2026-06-14)

### Русский
- **Блокировка страны** — lock_defaults блокирует поля на форме регистрации
- **Сохранение языка** — язык сохраняется в cookie и восстанавливается между сессиями
- **Язык из страны** — язык подставляется из привязанного к стране
- **loginAfter** — событие восстановления языка из профиля

### English
- **Country lock** — lock_defaults locks registration form fields
- **Language persistence** — language saved to cookie across sessions
- **Country language** — language auto-selected from country settings
- **loginAfter** — event to restore language from user profile

---

## 2.1.0 (2026-06-13)

### Русский
- **Language override** — принудительная установка языка из сессии/cookie/страны
- **Currency override** — принудительная установка валюты из страны
- **AJAX переключение валюты** — setCurrency() без перезагрузки
- **Индикатор пароля** — визуальный индикатор надёжности
- **Маска телефона** — форматирование с кодом страны

### English
- **Language override** — forced language from session/cookie/country
- **Currency override** — forced currency from country
- **AJAX currency switching** — setCurrency() without page reload
- **Password strength** — visual indicator
- **Phone mask** — formatting with country code

---

## 1.1.0 (2026-06-07)

### Русский
- Первый стабильный релиз
- Современная форма регистрации
- OCMOD-модификации шаблонов
- Управление языком интерфейса
- Исправление PHP 8.4 warning

### English
- First stable release
- Modern registration form
- OCMOD template modifications
- Language management
- PHP 8.4 warning fix
