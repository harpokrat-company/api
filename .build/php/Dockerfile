FROM composer

ADD ./ /var/www
WORKDIR /var/www
RUN composer install --no-interaction

FROM php:7.2-fpm

RUN docker-php-ext-install pdo pdo_mysql
