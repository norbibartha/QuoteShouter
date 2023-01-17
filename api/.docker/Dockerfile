FROM php:8.0-fpm

RUN apt update \
    && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install intl opcache pdo pdo_mysql sockets \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && pecl install -o -f redis \
    && docker-php-ext-enable redis

WORKDIR /var/www/api

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

