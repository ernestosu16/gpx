#!/usr/bin/env bash


chown -R www-data:www-data var/cache var/log public/uploads public/download;

exec "$@"