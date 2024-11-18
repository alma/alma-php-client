#!/usr/bin/env bash

# Remove /api/v1 from REPORT_PORTAL_ENDPOINT (reportportal/agent-php-PHPUnit requires only host)
export REPORT_PORTAL_HOST=${REPORT_PORTAL_ENDPOINT/\/api\/v1/}

# Add secrets values in tests/reportportal_phpunit_conf_template.xml
# Following environment variables are required:
# REPORT_PORTAL_API_KEY
# REPORT_PORTAL_HOST
# PHP_VERSION
envsubst < tests/reportportal_phpunit_conf_template.xml > tests/reportportal_phpunit_conf.xml

# Add conf for ReportPortal extension in phpunit.ci.xml
# Inserts content of file tests/reportportal_phpunit_conf.xml before </phpunit> end tag in phpunit.ci.xml
sed -i $'/<\/phpunit>/{e cat tests/reportportal_phpunit_conf.xml\n}' phpunit.ci.xml

# Add ReportPortal extension to composer.json
# reportportal/phpunit has no stable version, so we set minimum stability to dev only when running tests
composer config minimum-stability dev
# This is not supported by all versions of PHP, so we need to install it separately
composer require --dev reportportal/phpunit
