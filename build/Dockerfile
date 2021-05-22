FROM php:8.0-fpm-alpine

WORKDIR /app

RUN apk add --update nginx postgresql-dev \
    && docker-php-ext-install pdo_pgsql \
    && rm -rf /var/cache/apk/*
