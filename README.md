# Iskra Account — Modern Registration & Language Manager

**OpenCart 4 extension.** Modernises the registration page with password strength meter, phone number mask, and per-user language preference. No core file modifications — works entirely through OpenCart's event system.

## Features

- **Modern Registration Form** — clean Bootstrap 5.3+ compatible UI with icon inputs
- **Password Strength Meter** — real-time visual feedback (Weak → Fair → Good → Strong)
- **Phone Mask** — auto-format phone as +7 (XXX) XXX-XX-XX
- **Password Confirmation** — real-time match check
- **Email Availability Check** — AJAX check on input (debounced)
- **Password Visibility Toggle** — show/hide password button
- **Language Preference** — user picks a language on registration
- **Language Manager** — cookie (90 days) for guests, DB `language_preference` for registered users
- **Admin Configurable Default Language** — set first-visit language in admin panel
- **Dark Mode Support** — respects `prefers-color-scheme: dark`
- **No Core Modifications** — uses events only (3 event hooks)
- **RTL Ready** — inherits OpenCart's `dir` attribute

## Installation

### Via Composer (recommended)
```bash
composer require iskra-ecommerce/iskra_account
```
Then run: **Admin → Extensions → Installer → Install**

### Via OCMod ZIP
1. Download the latest `.ocmod.zip` from [GitHub Releases](https://github.com/iskra-ecommerce/iskra_account/releases)
2. Go to **Admin → Extensions → Installer → Upload**
3. Click **Install** next to `Iskra Account`

### Manual
1. Copy `extension/iskra_account/` into your OpenCart root
2. Run **Admin → Extensions → Installer** — it should appear in the list
3. Click **Install**

## Configuration

**Admin → Extensions → Modules → Iskra Account → Edit**

| Setting | Description | Default |
|---------|-------------|---------|
| Status | Enable/disable the extension | On |
| Default Language | First-visit language code | `ru-ru` |
| Cookie Lifetime | Days to remember guest language | 90 |
| Password Strength | Show/hide strength meter | On |
| Phone Mask | Auto-format phone input | On |
| Language Select | Show language picker on registration | On |
| Min Password Length | Minimum password characters | 8 |

## User Flow

1. **First visit** → site loads in admin-configured default language
2. **Guest switches language** → cookie `iskra_language` set for 90 days
3. **Registration** → user sees a language picker (optional), choice saved to DB
4. **Logged-in user** → language preference from DB overrides cookie
5. **Profile edit** → language can be changed permanently

## Requirements

- OpenCart 4.0+
- PHP 8.1+
- Bootstrap 5.3+ (OpenCart 4 default theme)

## Files Modified

**None.** Extension works purely through OpenCart's event system:
- `catalog/view/account/register/before` — inject template data
- `catalog/view/common/header/before` — load CSS/JS
- `catalog/model/account/customer/addCustomer/after` — save language preference

## Changelog

See [docs/CHANGELOG.md](docs/CHANGELOG.md)

## License

GNU General Public License v3.0 or later — [LICENSE](LICENSE)

## Links

- [GitHub](https://github.com/iskra-ecommerce/iskra_account)
- [OpenCart Marketplace](https://www.opencart.com/index.php?route=marketplace/extension)
