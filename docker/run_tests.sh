#!/usr/bin/env bash
set -ex

tests="
    ./vendor/bin/phpstan analyse src --level max
    ./vendor/bin/psalm
    ./vendor/bin/phpunit -c phpunit.xml
    ./vendor/bin/phpcs --standard=PSR2 src/ tests/
"

bash -c "$tests"#!/usr/bin/env bash
