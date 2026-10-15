# Telegram Subscription Bot

[![Webhook and trial checks](https://github.com/Sultanbro/telegabot/actions/workflows/tests.yml/badge.svg)](https://github.com/Sultanbro/telegabot/actions/workflows/tests.yml)

Backend Telegram-бота на **PHP / Laravel 8** для пробного доступа к закрытому чату и каналу. Обрабатывает команды пользователя, формирует приглашения и содержит плановые проверки участия и срока доступа.

## Возможности

- `/start`: сохранение пользователя и сообщение с доступными командами.
- `/getTrial`: временные ссылки на чат и канал с ограничением на одного участника.
- Учёт выданного пробного доступа отдельно для каждого пользователя.
- Проверка членства и даты окончания доступа через команды планировщика.
- Webhook с проверкой заголовка `X-Telegram-Bot-Api-Secret-Token`.

В тексте приветствия присутствует `/pay`, но самостоятельный платёжный сценарий этой команды не реализован. Репозиторий не следует воспринимать как готовый сервис платных подписок.

## Устройство

| Файл | Назначение |
| --- | --- |
| [TelegramController](app/Http/Controllers/TelegramController.php) | Обработка команд, приглашения, проверка участников |
| [VerifyTelegramWebhook](app/Http/Middleware/VerifyTelegramWebhook.php) | Проверка секрета входящего webhook |
| [routes/api.php](routes/api.php) | `POST /api/telegram/webhook` |
| [app/Models](app/Models) | Пользователи Telegram, группы и пробный доступ |
| [app/Console/Commands](app/Console/Commands) | Проверки доступа и регистрация webhook |
| [app/Console/Kernel.php](app/Console/Kernel.php) | Периодичность выполнения команд |

Используются Laravel 8, Eloquent, Telegram Bot SDK и Laravel HTTP Client. Старые открытые GET-маршруты настройки webhook и создания приглашений удалены; токен бота не используется как путь webhook.

## Проверки одной командой

```sh
git clone https://github.com/Sultanbro/telegabot.git
cd telegabot
docker compose -f compose.test.yml run --build --rm tests
```

Docker устанавливает зависимости из `composer.lock`; PHP 8.1 выбран для воспроизведения существующего стека. **8 тестов** проверяют webhook, закрытие технических маршрутов, начальную страницу и выдачу пробного доступа разным пользователям. SQLite работает в памяти, Telegram SDK и HTTP-ответы подменяются. Сообщения, приглашения и изменения реальных чатов при тестах не отправляются. GitHub Actions запускает те же проверки.

## Подготовка собственного бота

1. Установите зависимости совместимым PHP и Composer: `composer install`.
2. Скопируйте `.env.example` в `.env`, создайте ключ через `php artisan key:generate`.
3. Настройте отдельную базу в `DB_*`, выполните `php artisan migrate`.
4. Заполните `TELEGRAM_BOT_TOKEN`, `CHAT_ID`, `CHANNEL_id` и добавьте бота администратором только нужных тестовых чатов.
5. Задайте `TELEGRAM_WEBHOOK_URL=https://<ваш-домен>/api/telegram/webhook` и отдельный случайный `TELEGRAM_WEBHOOK_SECRET` из 32–256 символов `A–Z`, `a–z`, `0–9`, `_`, `-`.
6. После настройки HTTPS выполните `php artisan telegram:webhook`. Эта команда регистрирует адрес у Telegram; автоматические тесты её сетевую часть не выполняют.

Для планировщика предусмотрены команды `VerSud`, `CheckPay`, `CheckDay`. Команды могут проверять участников и отзывать доступ, поэтому сначала проверьте их на собственных тестовых чатах. Набор тестов не покрывает полный жизненный цикл подписки и все фоновые задачи. Обновление PHP/Laravel перед производственным использованием — отдельная задача.
