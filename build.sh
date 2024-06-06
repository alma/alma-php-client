#!/usr/bin/env bash

rm -rf dist/ vendor/
mkdir -p ./dist
composer install --no-dev
zip -9 -r "dist/alma-php-client.zip" src/ README.md CHANGELOG.md LICENSE composer.json
