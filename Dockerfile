FROM php:alpine

WORKDIR /app

COPY . .

RUN curl -s https://getcomposer.org/installer | php

RUN mv ./composer.phar /usr/local/bin/composer

RUN composer i

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN ["mv", ".env.example", ".env"]

RUN ["php", "artisan", "jwt:secret"]

ENTRYPOINT [ "./entrypoint.sh" ]

CMD ["php", "artisan", "serve", "--host", "0.0.0.0"]