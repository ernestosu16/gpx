#!/usr/bin/env bash

# Iniciando el servicio de tareas programadas
bin/console cron:start

chown -R www-data:www-data var/cache var/log public/uploads public/download;

bin/console assets:install
exec "$@"