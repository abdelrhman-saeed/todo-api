# Task Management System API

## Installing
> to run it with docker then do the next steps: </h3>

```cmd

docker compose build
docker compose up

```

> to run it localy

```cmd
composer i
cp .env.example .env
```
> change the .env file
- set APP_URL=127.0.0.1
- set DB_HOST=127.0.0.1
- set DB_PASSWORD=

```cmd

php artisan migrate:refresh && php artisan db:seed
php artisan key:gen
php artisan jwt:secret
php artisan serve

```
