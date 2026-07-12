# cooksy.pp.ua

## WordPress в Docker

## Запуск

1. `docker compose up -d`
2. Открой `http://localhost:8080`
3. Заверши стандартную установку WordPress
4. В админке открой `Внешний вид -> Темы` и активируй тему `Вкусно дома`

## Структура

- `docker-compose.yml` — WordPress + MariaDB
- `wordpress/wp-content/themes/vkusno-doma` — кастомная тема
- `wordpress/wp-content/themes/vkusno-doma/assets/images` — изображения макета

## Примечание

Тема уже содержит сверстанную главную страницу в `front-page.php`. После активации она будет открываться на корне сайта.
