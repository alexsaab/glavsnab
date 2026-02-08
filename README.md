# 🛒 Headless Product Catalog API

Простое REST API для управления товарами. Написано на Symfony 6 с использованием Doctrine ORM, PostgreSQL и Docker.

## 📦 Функциональность

- Добавление товара
- Получение товара по ID
- Обновление товара
- Удаление товара

Каждый товар имеет:
- Название (`name`)
- Цена (`price`)
- Статус: `active` / `inactive`
- Дата и время создания (`createdAt`)

## ⚙️ Технологии

- **Symfony 6**
- **Doctrine ORM** + Migrations
- **PostgreSQL** (в Docker)
- **REST API**, ответы в JSON
- **Тесты**: PHPUnit (функциональные и unit)
- **Docker** + `docker-compose`

---

## 🚀 Запуск проекта

### 1. Клонируй репозиторий

`git clone git@github.com:alexsaab/glavsnab.git cd glavsnab`


### 2. Запусти контейнеры

`docker-compose up -d`


> Поднимет: PHP, Nginx, PostgreSQL.

---

### 3. Установи зависимости Composer


`docker-compose exec php composer install`


---

### 4. Создай базу данных и примените миграции

`
docker-compose exec php bin/console doctrine:database:create
docker-compose exec php bin/console doctrine:migrations:migrate --no-interaction
`

---

### 5. Доступ к API

API будет доступно по адресу: `http://localhost:8080`

---

## 🧪 Тестирование

Для запуска тестов используйте следующую команду:

`docker-compose exec php bin/phpunit`
