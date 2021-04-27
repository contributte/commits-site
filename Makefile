.PHONY: install code-checker phpstan schema tests

install:
	composer update

code-checker:
	vendor/bin/code-checker --eol --fix --strict-types -d app/
	vendor/bin/code-checker --eol --fix --strict-types -d tests/

phpstan:
	vendor/bin/phpstan analyse

schema:
	rm -rf var/temp/cache
	php bin/console orm:schema-tool:create
	php bin/console dbal:import fixtures.sql
	php bin/console orm:validate-schema --skip-sync

tests:
	vendor/bin/tester -j 8 -C --colors 1 --log var/log/tests.log --temp var/temp tests/
