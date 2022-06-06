<h1 align="center">FoodBot</h1>

# Разворачивание проекта

## Все команды подробно описаны в Makefile.

- Клонировать репозиторий с проектом;
- Выполнить команду cp .env.example .env для копирования файла конфигурации;
- В .env файле задать настройки БД:
* DB_DATABASE - База данных
* DB_USERNAME - Пользователь
* DB_PASSWORD - Пароль пользователя 
* DB_ROOT_PASSWORD - Пароль root пользователя
- Для инициализации контейнеров выполнить make up или docker-compose up -d --build;
- Для запуска миграций и заполнения бд начальными данными выполнить make migrate-seed или docker-compose exec app php artisan migrate --seed;

## Переменные конфигурирования бота в .env:
- TELEGRAM_TOKEN - токен телеграм бота;
- FOOD_TELEGRAM_CHAT_ID - общий чат для отправки уведомлений;

- Для регистрации callback-url бота выполнить команду make bot-register или docker-compose exec app php artisan botman:telegram:register;
- Далее необходимо ввести url вида: https://yourapp.domain/bot.
