up:
	docker-compose up -d
php-cli:
	 docker exec -it php-container bash
build: clean-redis-folders create-redis-folders install-deps up
	docker-compose run php-service composer install
stop:
	docker-compose stop
clean-redis-folders:
	sudo rm -Rf redis-data-master
	sudo rm -Rf redis-data-slave
create-redis-folders:
	sudo mkdir -p redis-data-master
	sudo mkdir -p redis-data-slave
	sudo chown 1001:1001 redis-data-master
	sudo chown 1001:1001 redis-data-slave
install-deps: clean-redis-folders create-redis-folders
	docker-compose build
test:
	docker-compose exec -it --user root php-container bash bin/phpunit