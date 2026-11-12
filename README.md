# Laravel Shop API

API интернет-магазина на Laravel 8, PHP 7.4 и PostgreSQL 14. Поддерживает регистрацию и вход по JWT, каталог с поиском, корзину, оформление и историю заказов.

## Запуск через Docker

Понадобятся Docker с Compose v2 и базовый образ `laravel-shop-php:7.4-base`. Dockerfile использует этот локальный образ. Если вам передали его архив, загрузите его командой `docker load -i laravel-shop-php-7.4-base.tar`. Если образ недоступен, используйте запуск через PHP ниже.

В корне проекта при первой установке выполните:

```bash
cp .env.example .env
docker compose build app
docker compose run --rm --no-deps app composer install --no-interaction
docker compose run --rm --no-deps app php artisan key:generate
docker compose run --rm --no-deps app php artisan jwt:secret
docker compose up -d
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed --class=DemoCatalogSeeder
```

Сидер добавляет один бренд и два товара. При повторном запуске достаточно `docker compose up -d`. Команда `docker compose stop` останавливает контейнеры, данные PostgreSQL хранятся в отдельном томе.

## Запуск через PHP

Понадобятся PHP 7.4 с драйвером `pdo_pgsql`, Composer 2 и запущенный PostgreSQL 14. Создайте пустую базу и пользователя с правом создавать в ней таблицы. Скопируйте `.env.example` в `.env` и укажите адрес базы в `DB_HOST` (обычно `127.0.0.1`), а также свои `DB_DATABASE`, `DB_USERNAME` и `DB_PASSWORD`.

```bash
composer install --no-interaction
php artisan key:generate
php artisan jwt:secret
php artisan migrate
php artisan db:seed --class=DemoCatalogSeeder
php artisan serve --host=127.0.0.1 --port=8001
```

## Проверка API

API доступен по адресу `http://localhost:8001/api`. Корневой URL `/` возвращает `404`. На свежей базе зарегистрируйте покупателя, затем получите токен:

```bash
curl -fsS http://localhost:8001/api/auth/register \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{"email":"demo@example.test","password":"demo-password","name":"Demo","last_name":"User"}'

curl -fsS http://localhost:8001/api/auth/login \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{"email":"demo@example.test","password":"demo-password"}'
```

Скопируйте `access_token` из ответа и подставьте его в запрос:

```bash
curl -fsS 'http://localhost:8001/api/products?search=screw' \
  -H 'Accept: application/json' \
  -H 'Authorization: Bearer <access_token>'
```

Для показа оформления возьмите GUID из `data[0].guid`, добавьте товар через `POST /api/basket/add` и оформите заказ через `POST /api/orders/submit`. Примеры тел запросов есть в [сценарии показа](LOCAL_SETUP.md#сценарий-показа).

Тесты API запускаются командой `docker compose exec app php artisan test` в Docker или `php artisan test` при запуске через PHP. Перед запуском тестов примените миграции к локальной базе.
