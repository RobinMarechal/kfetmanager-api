.PHONY: tests install

install: composer.json composer.lock
	composer update

tests:
	vendor/bin/phpunit