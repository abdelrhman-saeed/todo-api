#!/bin/sh

php artisan key:gen

php artisan migrate:refresh

php artisan db:seed

exec "$@"