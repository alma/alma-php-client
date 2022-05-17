.DEFAULT_GOAL := help
.PHONY: help, up, stop, remove, composer, test, test-all, integration-test, connect

help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-15s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

##
## Docker tests
##---------------------------------------------------------------------------
up: ## Up PHP test container
	CURRENT_UID=$(id -u):www-data docker-compose -f .docker/docker-compose.yml up -d --build;

stop: ## Stop PHP test container
	CURRENT_UID=$(id -u):www-data docker-compose -f .docker/docker-compose.yml stop;

remove: ## remove PHP test container
	CURRENT_UID=$(id -u):www-data docker-compose -f .docker/docker-compose.yml down;

composer:
	docker exec -it -u www-data:www-data test-php /usr/bin/composer install

test: up composer ## Execute PHPUnit tests
	docker exec -it -u www-data test-php sh -c './vendor/bin/phpunit --testsuite "Alma PHP Client Unit Test Suite"'

integration-test: up composer ## Execute intregration tests
	docker exec -it -u www-data test-php sh -c './vendor/bin/phpunit --testsuite "Alma PHP Client Integration Test Suite"'

test-all: up composer ## Execute All PHPUnit tests
	docker exec -it -u www-data test-php sh -c './vendor/bin/phpunit'

connect: up ## Connect to test container
	docker exec -it -u www-data:www-data test-php /bin/bash

lint: up ## lint the php code
	docker exec -it -u www-data test-php sh -c './vendor/bin/phpcs src/'

fix: up ## lint fix the php code
	docker exec -it -u www-data test-php sh -c './vendor/bin/phpcbf src/ tests/'