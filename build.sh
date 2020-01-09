#!/usr/bin/env bash

DIR=`pwd`

rm -rf ./dist/
rm -rf /tmp/alma-php-client
mkdir -p /tmp/alma-php-client

cp -r ./* /tmp/alma-php-client/

mkdir ./dist

cd /tmp/alma-php-client/ || exit
rm -rf vendor
composer install --no-dev
zip -9 -r "$DIR/dist/alma-php-client.zip" . --exclude \*dist\* \*.git\* \*.idea\* \*.DS_Store

rm -rf /tmp/alma-php-client
