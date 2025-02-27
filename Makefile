# Description: Makefile for Laravel project

# Commands
start: ### Start docker containers
	docker-compose up -d

stop: ### Stop docker containers
	docker-compose down

restart: stop start ### Restart docker containers

composer-install: ### Install composer dependencies
	docker-compose exec app composer install

migrate: ### Run migrations
	docker-compose exec app php artisan migrate

rollback: ### Rollback migrations
	docker-compose exec app php artisan migrate:rollback

reset-local-env: ### Reset local environment
	docker-compose exec app php artisan migrate:fresh
	docker-compose exec app php artisan db:seed --class=StartLocalSeeder
