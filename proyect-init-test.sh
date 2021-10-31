#!/usr/bin/env bash
php bin/console doctrine:database:create --if-not-exists --env=test

php bin/console doctrine:schema:update --force --dump-sql --env=test

php bin/console app:configurar:nomenclador --env=test

php bin/console app:fixtures:import --env=test
