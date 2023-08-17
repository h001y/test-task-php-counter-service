# Сервис по сохранению счетчика гео

# RU
## Установка 
### 1. git clone  h001y/test-task-php-counter-service
### 2. make build

## Другие команды
### Поднятие уже существующих контейнеров
### make up

### Очистка папок redis-slave, redis-master
### make clean-redis-folders

### Создание папок для редиса и права
### make create-redis-folders

## Что внутри
### Сервис достаточно простой - под капотом контейнеры nginx, php, redis-slav, redis-master
### При запросе /update/ru происходит запись в мастер базу
### При запросе /statistic происходит чтение из slave базы и "запекание" кеша. Частота сброса и перезапекания кеша 7 секунд

## Кэш файловой системы
### Для выдерживания нагрузки больше 1000 rps кеш хранится в папке var/cache/cached_data.json после запекания сохраняется флаг last_modified все последующие запросы к ручке считывают json из файловой системы. Количество запросов в базу в таком случае ровно N - кол-ву запросов


# EN
## Installation
### 1. git clone  h001y/test-task-php-counter-service
### 2. make build

## Another commands
### Raising existing containers
### make up

### Delete folders redis-slave, redis-master
### make clean-redis-folders

### Create folders and chown
### make create-redis-folders

## Whats inside ?
### The service is simple enough - there are containers under the hood nginx, php, redis-slav, redis-master
### The query /update/en writes to the master database
### The /statistic request reads from the slave base and "bakes" the cache. Frequency of cache reset and reloading is 7 seconds

## File system cache
### To withstand load more than 1000 rps cache is stored in var/cache/cached_data.json folder after baking last_modified flag is stored all subsequent requests to the handle read json from the file system. The number of queries to the database in this case is equal to N - the number of queries
