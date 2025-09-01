ARG PHP_IMG_TAG=8.3-alpine
FROM php:${PHP_IMG_TAG} AS production

WORKDIR /composer

RUN apk add --no-cache php-tokenizer
RUN apk add --no-cache composer
RUN composer self-update
RUN composer init -n --name="alma/php-cs" --description="php-cs" --type="library"

# PHP CS Fixer
RUN composer require friendsofphp/php-cs-fixer --no-interaction

WORKDIR /app

ENTRYPOINT ["/composer/vendor/bin/php-cs-fixer"]
CMD ["--version"]