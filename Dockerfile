FROM composer:2 as composer
FROM php:8.2-fpm

ENV DEBIAN_FRONTEND noninteractive

# Packages install
RUN apt update && \
    apt install -y --no-install-recommends \
    git \
    unzip \
    && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN usermod -u 1000 www-data
RUN groupmod -g 1000 www-data

RUN mkdir -p /app && chown -R www-data:www-data /app

USER www-data
WORKDIR /app

COPY --link .docker/php.ini /usr/local/etc/php/php.ini

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --link composer.json ./
RUN composer install --prefer-dist --no-progress --no-suggest
