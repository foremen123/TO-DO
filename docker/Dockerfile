FROM php:8.4-fpm

RUN apt-get update && apt-get install -y\
    curl\
    zip\
    unzip\
    git\
    vim

RUN docker-php-ext-install pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www
