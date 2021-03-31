#!/usr/bin/env bash

docker network create --attachable invoicebox_local

docker volume create local_backend-test-app_database

bin/composer 'install --ignore-platform-reqs --no-scripts'

docker-compose build
docker-compose -p invoicebox-backend-test-app -f ./docker-compose.yml up -d
./bin/console doctrine:schema:update --force