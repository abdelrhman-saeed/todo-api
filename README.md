<h1> Task Management System API </h1>

<h3> Installing </h3>

<h3> to run it with docker then do the next steps: </h3>

<p> docker compose build </p>
<p> docker compose up </p>

<h3> to run it localy </h3>

<p> composer i </p>
<p> cp .env.example .env </p>
<p> change the APP_URL=http://0.0.0.0 to APP_URL=127.0.0.1 in the .env file these changes were made for docker </p>
<p> change DB_HOST=db to DB_HOST=127.0.0.1 or to your host name </p>
<p> and change the DB_PASSWORD=root to DB_PASSWORD=  or to what ever your password</p>

<p> php artisan migrate:refresh && php artisan db:seed </p>
<p> php artisan key:gen </p>
<p> php artisan jwt:secret </p>
<p> php artisan serve </p>
