ARG PHP_IMG_TAG=5.6-alpine
FROM php:${PHP_IMG_TAG} AS production

WORKDIR /composer

RUN apk add --no-cache composer
RUN composer self-update
RUN composer init -n --name="alma/php-cs" --description="php-cs" --type="library"

RUN composer config --no-interaction --no-plugins allow-plugins.dealerdirect/phpcodesniffer-composer-installer true
RUN composer require squizlabs/php_codesniffer --no-interaction
RUN composer require phpcompatibility/php-compatibility --no-interaction
RUN composer require phpcompatibility/phpcompatibility-paragonie:"*" --no-interaction

RUN /composer/vendor/bin/phpcs --config-set installed_paths /composer/vendor/escapestudios/symfony2-coding-standard,/composer/vendor/squizlabs/php_codesniffer,/composer/vendor/phpcompatibility/php-compatibility,/composer/vendor/phpcompatibility/phpcompatibility-paragonie

WORKDIR /app

ENTRYPOINT ["/composer/vendor/bin/phpcs"]
CMD ["--version"]