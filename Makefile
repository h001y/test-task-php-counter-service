up:
	docker-compose up -d
php-cli:
	 docker exec -it php-container bash
build:
	docker-compose build
stop:
	docker-compose stop