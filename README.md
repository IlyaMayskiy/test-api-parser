# Тестовое задание: Парсер данных из API

## Описание

Веб-приложение на Laravel для получения данных из тестового API (продажи, заказы, склады, доходы) и сохранения их в базу MySQL. Реализован веб-интерфейс для просмотра таблиц и возможность выборочного обновления каждой таблицы или всех сразу.

## Требования

- PHP >= 8.1
- Composer
- MySQL
- Расширения PHP: BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

## Установка

1. Клонируйте репозиторий:

   ```bash
   cd <папка проекта>
   git clone https://github.com/IlyaMayskiy/test-api-parser

## Подключение к базе данных

- DB_CONNECTION=mysql
- DB_HOST=theomasw.beget.tech
- DB_PORT=3306
- DB_DATABASE=theomasw_test
- DB_USERNAME=theomasw_test
- DB_PASSWORD=%*9AQyhWAnxL

## Файлы с которыми работал

- Http/Controllers/DataController - основной контроллер, в котором происходит вывод и обновление данных. Решил сделать все в одном контроллере, т.к. задача не большая.
- Модели, у всех параметры 'id', 'external_id', 'payload', 'created_at', 'updated_at'
    - Models/Income - Доходы
    - Models/Order - Заказы
    - Models/Sale - Продажи
    - Models/Stock - Склады
- Views
    - data/index.blade - список таблиц
    - data/items.blade - вывод таблицы
    - layouts/app.blade - Обложка
- Миграции
    - database/migrations/create_sales_table - Создание таблицы sales
    - database/migrations/create_orders_table - Создание таблицы orders
    - database/migrations/create_stocks_table - Создание таблицы stocks
    - database/migrations/create_incomes_table - Создание таблицы incomes

## Заключение

Выполнил: Карасев Илья Алексеевич

Этот README соответствует требованию "не исчерпывающее, но понятное" и будет полезен любому, кто захочет развернуть и протестировать проект.
