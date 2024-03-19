build:
	docker-compose build

up:
	docker-compose up -d

down:
	docker-compose down --remove-orphans

php-bash:
	docker-compose exec php-fpm bash

postgres-bash:
	docker-compose exec postgres bash
