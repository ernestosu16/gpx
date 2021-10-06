#!/usr/bin/env bash
php bin/console doctrine:database:create --if-not-exists

php bin/console doctrine:schema:update --force --dump-sql

php bin/console app:configurar:nomenclador

php bin/console app:fixtures:import
