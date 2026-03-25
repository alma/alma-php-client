ARG PHP_VERSION
ARG COMPOSER_VERSION

FROM composer:${COMPOSER_VERSION} as composer
FROM php:${PHP_VERSION}-fpm
ARG PHP_VERSION

ENV DEBIAN_FRONTEND noninteractive

# Update sources.list to support both outdated Debian archives (Stretch for PHP 5.6, Buster for PHP 7.1+)
RUN . /etc/os-release && \
    if [ "$VERSION_CODENAME" = "stretch" ]; then \
        rm -rf /var/lib/apt/lists/* && \
        echo "deb [trusted=yes] http://archive.debian.org/debian stretch main" > /etc/apt/sources.list && \
        echo "deb [trusted=yes] http://archive.debian.org/debian-security stretch/updates main" >> /etc/apt/sources.list; \
    elif [ "$VERSION_CODENAME" = "buster" ]; then \
        rm -rf /var/lib/apt/lists/* && \
        echo "deb [trusted=yes] http://archive.debian.org/debian buster main" > /etc/apt/sources.list && \
        echo "deb [trusted=yes] http://archive.debian.org/debian-security buster/updates main" >> /etc/apt/sources.list; \
    fi

# Install dependencies
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    git \
    zip \
    unzip \
    jq \
    && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

COPY --link .docker/php.ini /usr/local/etc/php/php.ini

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN case ${PHP_VERSION} in \
        8.0|8.1|8.2|8.3 ) pecl install xdebug-3.3.2 && docker-php-ext-enable xdebug;; \
        *) pecl install xdebug-2.9.8 && docker-php-ext-enable xdebug;; \
    esac

RUN usermod -u 1000 www-data && \
    groupmod -g 1000 www-data

RUN mkdir -p /app/vendor && \
    chown -R www-data:www-data /app

COPY --link composer.json /app/composer.json

# Patch composer.json for older PHP versions compatibility
RUN case ${PHP_VERSION} in \
        7.1 ) \
            jq 'del(."require-dev"."roave/security-advisories") | ."require-dev"."phpunit/phpunit" = "^7.5"' \
            /app/composer.json > /app/composer.tmp.json && \
            mv /app/composer.tmp.json /app/composer.json;; \
    esac && \
    chown www-data:www-data /app/composer.json

USER www-data
WORKDIR /app

RUN composer install --prefer-dist --no-progress --ignore-platform-reqs
