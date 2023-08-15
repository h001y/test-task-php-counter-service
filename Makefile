up:
	docker-compose up -d
php-cli:
	 docker exec -it php-container bash
build:
	sudo rm -R redis-data-master
	sudo rm -R redis-data-slave
	sudo mkdir redis-data-master
	sudo mkdir redis-data-slave
	sudo chown 1001:1001 redis-data-master
	sudo chown 1001:1001 redis-data-slave
	docker-compose build
stop:
	docker-compose stop