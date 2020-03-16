CURRENT_DIRECTORY := $(shell pwd)

test:
	php bin/phpunit

start:
	symfony server:start

diff:
	php bin/console doctrine:migrations:diff

migrate:
	php bin/console doctrine:migrations:migrate