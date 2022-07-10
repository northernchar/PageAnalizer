start:
	php artisan serve

setup:
	composer install
	php artisan key:gen --ansi
	php artisan migrate
	php artisan db:seed
	npm ci
	npm run dev
	make ide-helper

watch:
	npm run watch

migrate:
	php artisan migrate

console:
	php artisan tinker

log:
	tail -f storage/logs/laravel.log

test:
	php artisan test

test-coverage:
	XDEBUG_MODE=coverage php artisan test --coverage-clover build/logs/clover.xml

deploy:
	git push heroku main

lint:
	composer phpcs

lint-fix:
	composer phpcbf

ide-helper:
	php artisan ide-helper:eloquent
	php artisan ide-helper:gen
	php artisan ide-helper:meta
	php artisan ide-helper:mod -n

fpush:
	git add .
	git commit -m 'fix'
	git push

fcommit:
	git add .
	git commit -m 'fix'

hpush:
	git add .
	git commit -m 'fix'
	git push heroku main