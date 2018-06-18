#!/usr/bin/env bash
 set -ex

./vendor/bin/phpcs --standard=PSR2 src/ tests/
./vendor/bin/phpunit tests
./vendor/bin/phpstan analyse src --level max