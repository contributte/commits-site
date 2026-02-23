.PHONY: install ci phplint phpstan coding-standards schema tester

install:
	composer update --ignore-platform-req=php+
	php bin/console orm:schema-tool:create
	php tests/import-fixtures.php
	yarn install

ci: phplint phpstan coding-standards schema tester

phplint:
	composer phplint

phpstan:
	composer phpstan

coding-standards:
	composer coding-standards

schema:
	composer schema

tester:
	composer tester
