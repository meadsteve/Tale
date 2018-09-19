FROM composer:1.7.2

RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install xdebug-2.6.1 \
    && docker-php-ext-enable xdebug

WORKDIR /app

# Grab the composer.* files first so we can cache this layer when
# the dependencies haven't changed
COPY composer.json /app/composer.json
RUN composer install

COPY . /app/

CMD ./run_tests.sh
