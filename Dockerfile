#Get Composer
FROM composer:2.0 as vendor

WORKDIR /app

COPY composer.json composer.json


RUN composer install \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --no-dev \
    --prefer-dist

COPY . .
RUN composer dump-autoload
