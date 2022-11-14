FROM php:8.1.0-fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql

RUN apk add --no-cache curl git build-base zlib-dev oniguruma-dev autoconf bash

RUN pecl install xdebug && docker-php-ext-enable xdebug

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./volumes/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN chown www-data:www-data /var/www/html
COPY --chown=www-data:www-data ./ /var/www/html
WORKDIR /var/www/html/app
