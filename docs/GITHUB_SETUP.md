# Пошаговая инструкция: GitHub для новичка

## Что такое GitHub?

GitHub — это облачное хранилище кода. Вы пишете расширение локально (на своём компьютере), а потом отправляете на GitHub — туда могут зайти другие люди, скачать, посмотреть код, предложить улучшения.

---

## 1. Регистрация на GitHub (если ещё нет)

1. Откройте браузер: https://github.com/signup
2. Введите:
   - **Email** → ваш рабочий email
   - **Password** → придумайте пароль
   - **Username** → например, `yourname` (будет виден всем)
3. Подтвердите email (придёт письмо)
4. Всё, вы зарегистрированы

---

## 2. Создание организации iskra-ecommerce

Организация — это группа, под которой будут выходить все наши расширения.

1. Зайдите: https://github.com/organizations/plan
2. Нажмите **Create organization** (бесплатный план **Free**)
3. Заполните:
   - **Organization name**: `iskra-ecommerce`
   - **Contact email**: ваш email
   - **Description**: `OpenCart 4: Искра — modern extensions for OpenCart 4`
4. Нажмите **Next** → **Submit**
5. Подтвердите (может попросить подтвердить email)

Готово! Теперь вы владелец организации `iskra-ecommerce`.

---

## 3. Создание Personal Access Token (для доступа из консоли)

Токен — это цифровой ключ, который позволит отправлять код из консоли на GitHub.

1. Зайдите в настройки: https://github.com/settings/tokens
2. Нажмите **Generate new token** → **Fine-grained token**
3. Заполните:
   - **Token name**: `iskra-dev`
   - **Expiration**: `90 days`
   - **Repository access**: `All repositories`
4. В разделе **Permissions** найдите **Contents** → поставьте `Read and write`
5. Найдите **Workflows** → поставьте `Read and write`
6. Нажмите **Generate token**
7. **Скопируйте токен** (он показывается один раз!):

   ```
   ghp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
   ```

Сохраните его в блокноте — он понадобится для первого git push.

---

## 4. Создание репозитория iskra_account

Репозиторий — это папка проекта на GitHub.

1. Зайдите: https://github.com/iskra-ecommerce
2. Нажмите **New** (или вкладка Repositories → New)
3. Заполните:
   - **Repository name**: `iskra_account`
   - **Description**: `Modern Registration & Language Manager for OpenCart 4`
   - **Public** (обязательно, чтобы все видели)
   - **Initialize with README**: **НЕ ставить** галочку (мы сделаем локально)
   - **Add .gitignore**: **None**
   - **License**: **None**
4. Нажмите **Create repository**

После создания вы увидите страницу с инструкцией. Закройте её — мы будем отправлять код из консоли.

---

## 5. Настройка SSH-ключа (чтобы не вводить пароль каждый раз)

Это необязательно, но удобно. Если пропустите — будете вводить логин и токен при каждом push.

### Windows (OpenServer):

1. Откройте **Git Bash** (или PowerShell)
2. Выполните:
```bash
ssh-keygen -t ed25519 -C "ваш_email@example.com"
```
3. Нажимайте Enter на все вопросы (оставить по умолчанию)
4. Скопируйте ключ в буфер:
```bash
type %USERPROFILE%\.ssh\id_ed25519.pub
```
5. Выделите и скопируйте ВСЁ, что вывелось (начинается с `ssh-ed25519 AAA...`)
6. Зайдите: https://github.com/settings/keys
7. Нажмите **New SSH key**
8. Вставьте ключ, назовите его `Windows`, нажмите **Add SSH key**

---

## 6. Отправка кода на GitHub (первый раз)

Вся эта настройка делается один раз. Дальше вы просто делаете `git push`.

### Шаг 6.1: Откройте PowerShell в папке расширения

```bash
cd C:\OSPanel651\home\opencart40\extension\iskra_account
```

### Шаг 6.2: Инициализируйте git (один раз)

```bash
git init
git branch -M main
```

### Шаг 6.3: Привяжите к вашему email

```bash
git config user.email "ваш_email@example.com"
git config user.name "Ваше Имя"
```

### Шаг 6.4: Привяжите к удалённому репозиторию

**Через SSH (если настроили ключ):**
```bash
git remote add origin git@github.com:iskra-ecommerce/iskra_account.git
```

**Через HTTPS (с токеном):**
```bash
git remote add origin https://github.com/iskra-ecommerce/iskra_account.git
```

### Шаг 6.5: Сделайте первый коммит

```bash
git add .
git commit -m "v1.0.0: Initial release — modern registration + language manager"
```

### Шаг 6.6: Отправьте на GitHub

**Через SSH:**
```bash
git push -u origin main
```

**Через HTTPS (будет запрос логина и пароля — введите токен вместо пароля):**
```bash
git push -u origin main
```

Если спросит логин — введите ваш GitHub username.
Если спросит пароль — вставьте токен (тот самый `ghp_...`).

### Шаг 6.7: Создайте метку релиза

```bash
git tag v1.0.0
git push origin v1.0.0
```

После этого GitHub Action автоматически соберёт `.ocmod.zip` и создаст Release.

### Шаг 6.8: Проверьте

1. Зайдите: https://github.com/iskra-ecommerce/iskra_account
2. Должны быть видны все файлы
3. Нажмите **Releases** → там будет **v1.0.0** с файлом `iskra_account-v1.0.0.ocmod.zip`

---

## 7. Обычный рабочий день (как обновлять)

Когда вы что-то изменили в коде:

```bash
cd C:\OSPanel651\home\opencart40\extension\iskra_account
git add .
git commit -m "Что сделали: добавили то-то, исправили то-то"
git push origin main
```

Если делаете новый релиз:

```bash
git tag v1.0.1
git push origin v1.0.1
```

---

## 8. Частые проблемы и решения

### `git push` просит логин/пароль — каждый раз
→ Решение: настройте SSH-ключ (шаг 5)
→ Или сохраните токен: `git config --global credential.helper store`

### `Failed to push: Repository not found`
→ Проверьте название репозитория: `git remote -v`
→ Вы должны быть владельцем организации `iskra-ecommerce`

### `Permission denied (publickey)`
→ Не настроен SSH-ключ. Используйте HTTPS + токен, или настройте SSH.

### `! [rejected] main -> main (fetch first)`
→ Кто-то уже отправил код до вас. Сделайте: `git pull origin main --rebase` → потом снова `git push`
