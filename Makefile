.DEFAULT_GOAL := help
.PHONY: help, up, test, connect

help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-15s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

##
## Docker tests
##---------------------------------------------------------------------------
up: ## Up PHP test container
	CURRENT_UID=$(id -u):www-data docker-compose up -d --build;

test: ## Execute PHPUnit tests
	docker exec -it -u www-data test-php sh -c './vendor/bin/phpunit'

connect: ## Connect to test container
	docker exec -it -u www-data:www-data test-php /bin/bash