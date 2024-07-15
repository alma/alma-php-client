#!/usr/bin/env bash

set -ex

# Copy folder to /tmp
cp -r . /tmp/alma-php-client
cd /tmp/alma-php-client

# Replace string in files
string=': void'
grep -r -l "$string" tests/ | xargs sed -i "s/$string//g"

# Run tests
composer exec phpunit --verbose -- --configuration phpunit.dist.xml --testsuite "Alma PHP Client Integration Test Suite"
