# Локальная установка laravel_shop_api

API доступен по адресу `http://localhost:8001/api`. Корневой маршрут возвращает `404`.

## Работа с проектом
Первичная установка описана в [README](README.md). Команды ниже выполняются в корне проекта.

Запуск:
    docker compose up -d
Миграции и демокаталог:
    docker compose exec app php artisan migrate
    docker compose exec app php artisan db:seed --class=DemoCatalogSeeder
Остановка с сохранением базы:
    docker compose stop
Логи:
    docker compose logs --tail=100 app
Artisan:
    docker compose exec app php artisan route:list
Зависимости:
    docker compose exec app composer install
Тесты:
    docker compose exec app php artisan test

PostgreSQL хранит данные в томе `postgres-data`.
Порт API привязан к 127.0.0.1. PostgreSQL доступен внутри сети Docker.
Контейнеры перезапускаются вместе с Docker, если не остановлены вручную.

## Локальный пользователь
Зарегистрируйте покупателя и войдите по [примерам из README](README.md#проверка-api).
Полученный `access_token` передавайте в заголовке `Authorization: Bearer <token>`.

## Сценарий показа

1. `GET /api/products?search=screw` находит отвёртку. Скопируйте `data[0].guid`.
2. `POST /api/basket/add` принимает `{"basket":[{"nomenclature_guid":"<guid>","amount":2}]}`. Повторите запрос с другим количеством и проверьте результат через `GET /api/basket`.
3. `POST /api/orders/submit` принимает GUID выбранных товаров из корзины. Количество берётся из корзины, цена — из каталога. Сумма каждой позиции округляется до копеек. Корзина после оформления сохраняется.
4. `GET /api/orders` возвращает историю текущего пользователя с позициями и зафиксированными ценами.

Пример тела запроса оформления:

```json
{
  "products": ["d0000000-0000-4000-8000-000000000002"],
  "delivery_method": "Самовывоз",
  "delivery_address": "Пункт самовывоза",
  "delivery_company": "Nord Tools",
  "contact_name": "Тестовый покупатель",
  "contact_phone": "+70000000000",
  "is_delivery_today": false
}
```

## Что настроено и исправлено
- Отдельные контейнеры приложения и PostgreSQL 14.
- Установлены версии composer.lock, Laravel 8.83.27 и PHP 7.4.33.
- Созданы .env, ключ приложения и JWT_SECRET. .env исключён из Git.
- Применены миграции, включая точное хранение цен и сумм заказа.
- Исправлен default('false') на default(false) в миграции orders.
- Имена файлов моделей и FormRequest приведены к именам классов для PSR-4/Linux.
- Исправлен регистр импорта Nomenclature в контроллере.

Dockerfile использует локальный образ `laravel-shop-php:7.4-base`. В README описан также запуск через PHP.

## Проверка
Вход, поиск, изменение количества в корзине, оформление и история проверены HTTP-запросами.
Тесты API проверяют регистрацию, права, защиту последнего администратора, изоляцию корзин и заказов, а также откат заказа при ошибке записи позиции.
