#!/usr/bin/env bash
 set -ex

./vendor/bin/phpstan analyse src --level max
./vendor/bin/phpunit -c phpunit.xml
./vendor/bin/phpcs --standard=PSR2 src/ tests/