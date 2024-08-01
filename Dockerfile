ARG PHP_VERSION
ARG COMPOSER_VERSION

FROM composer:${COMPOSER_VERSION} as composer
FROM php:${PHP_VERSION}-fpm
ARG PHP_VERSION

ENV DEBIAN_FRONTEND noninteractive

# Install dependencies
RUN apt update && \
    apt install -y --no-install-recommends \
    git \
    zip \
    unzip \
    && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN case ${PHP_VERSION} in \
        8.0|8.1|8.2|8.3 ) pecl install xdebug-3.3.2 && docker-php-ext-enable xdebug;; \
        *) pecl install xdebug-2.9.8 && docker-php-ext-enable xdebug;; \
    esac

RUN usermod -u 1000 www-data
RUN groupmod -g 1000 www-data

RUN mkdir -p /app/vendor && chown -R www-data:www-data /app

USER www-data
WORKDIR /app

COPY --link .docker/php.ini /usr/local/etc/php/php.ini

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --link composer.json ./
RUN composer install --prefer-dist --no-progress
