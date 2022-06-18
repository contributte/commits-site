.PHONY: install ci phplint phpstan code-checker coding-standards schema tester

install:
	composer update
	php bin/console orm:schema-tool:create
	php tests/import-fixtures.php
	yarn install

ci: phplint phpstan code-checker coding-standards schema tester

phplint:
	composer phplint

phpstan:
	composer phpstan

code-checker:
	composer code-checker

coding-standards:
	composer coding-standards

schema:
	composer schema

tester:
	composer tester
