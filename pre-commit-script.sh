#!/bin/bash

# Run this script before committing!!!

#rm -rf vendor
#
#composer install -n
#

bin/console cache:clear --env=dev
bin/console cache:clear --env=test
bin/console cache:clear --env=prod

vendor/bin/phpstan clear-result-cache

vendor/bin/php-cs-fixer fix lib
#vendor/bin/php-cs-fixer fix tests
vendor/bin/phpstan analyse lib --level=8
#vendor/bin/phpstan analyse tests --level=8


# Make test db and schema here
#/var/www/html$ mysql test < <db.sql>

#vendor/bin/phpunit