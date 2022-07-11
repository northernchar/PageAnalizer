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

lintold:
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

fc:
	git add .
	git commit -m 'fix'

seed:
	php artisan migrate:fresh --seed

lint: 
	composer exec --verbose phpcs -- --standard=PSR12 app/Http/Controllers/UrlController.php
	composer exec --verbose phpcs -- --standard=PSR12 app/Models
	composer exec --verbose phpcs -- --standard=PSR12 tests/Feature
	composer exec --verbose phpcs -- --standard=PSR12 routes/web.php