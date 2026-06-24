# Laravel + Vue invoice generator

Проект состоит из:
- backend: laravel https://github.com/antonzmienko/order_pdf_api
- frontend: vue https://github.com/antonzmienko/order_pdf_client

### Запуск проекта локально
1) Создайте родительскую директорию
```
mkdir invoice_gen && cd invoice_gen
```
2) Скопируйте оба репозитрия в родительскую директорию
```
git clone https://github.com/antonzmienko/order_pdf_client.git
git clone https://github.com/antonzmienko/order_pdf_api.git
```
2) Создайте docker-compose.yaml
```
touch docker-compose.yaml
```
```
services:
    api:
        build: ./order_pdf_api
        ports:
        - "3000:3000"
        volumes:
        - ./order_pdf_api:/var/www/html
        environment:
          APP_URL: http://localhost:3000
        working_dir: /var/www/html
    
    client:
        build: ./order_pdf_client
        ports:
            - "5173:5173"
        volumes:
            - ./order_pdf_client:/app
        environment:
            VITE_API_URL: http://localhost:3000
        depends_on:
            - api
```
3) Для инициализации проекта используйте скрипт
```
touch init.sh
```
```
#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"

echo "Building images..."
docker compose build

echo "Initializing API..."
docker compose run --rm --no-deps --entrypoint /usr/local/bin/init.sh api

echo "Initializing client..."
docker compose run --rm --no-deps --entrypoint /usr/local/bin/init.sh client

echo ""
echo "Initialization complete."
echo "Start development: ./dev.sh"
```

4) Для разработки используйте скрипт
```
touch dev.sh
```
```
#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"

docker compose up --build -d

echo "API:    http://localhost:3000"
echo "Client: http://localhost:5173"
echo "Остановка: docker compose down"

```
