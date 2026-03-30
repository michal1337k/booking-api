FROM php:8.4-fpm

RUN docker-php-ext-install pdo pdo_mysql
RUN apt-get update && apt-get install -y git unzip zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www