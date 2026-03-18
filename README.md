# Тестовое задание: Парсер данных из API

## Описание

Веб-приложение на Laravel для получения данных из тестового API (продажи, заказы, склады, доходы) и сохранения их в базу MySQL. Реализован веб-интерфейс для просмотра таблиц и возможность выборочного обновления каждой таблицы или всех сразу.

## Требования

- PHP 8.1+
- Composer
- MySQL 8.0+
- Laravel 10.x

## Установка

1. Клонируйте репозиторий:

    ```bash
    cd <папка проекта>
    git clone https://github.com/IlyaMayskiy/test-api-parser

2. Установите Composer

    ```bash
    composer install

3. Запуск проекта

    ```bash
    php artisan serve

4. Запуск очереди(для загрузки данных из api в бд)

    ```bash
    php artisan queue:work

## Подключение к базе данных

- DB_CONNECTION=mysql
- DB_HOST=theomasw.beget.tech
- DB_PORT=3306
- DB_DATABASE=theomasw_test
- DB_USERNAME=theomasw_test
- DB_PASSWORD=%*9AQyhWAnxL

- https://center.beget.com/phpMyAdmin/sql.php?server=1&db=theomasw_test&table=jobs&pos=0(phpMyAdmin)
- Логин: theomasw_test
- Пароль: %*9AQyhWAnxL

## Файлы проекта

- Http/Controllers/DataController - основной контроллер, в котором происходит вывод и постановка задач в очередь.
- Jobs/FetchEntityJob - задача для асинхронной загрузки данных из API (вызывается для каждой таблицы отдельно)
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
    - database/migrations/_create_jobs_table - Создание таблиц для очередей (была создана автоматически)

## Заключение

Выполнил: Карасев Илья Алексеевич

Телефон: 9133716016

тг: IlyaMay

Этот README соответствует требованию "не исчерпывающее, но понятное" и будет полезен любому, кто захочет развернуть и протестировать проект.
