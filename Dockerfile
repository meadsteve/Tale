FROM composer:1.7.2

RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install xdebug-2.6.1 \
    && docker-php-ext-enable xdebug

RUN apk add entr # Used by the test watcher

WORKDIR /tools

RUN wget https://github.com/infection/infection/releases/download/0.10.3/infection.phar
RUN wget https://github.com/infection/infection/releases/download/0.10.3/infection.phar.asc
RUN chmod +x infection.phar

WORKDIR /app

# Grab the composer.* files first so we can cache this layer when
# the dependencies haven't changed
COPY composer.json /app/composer.json
RUN composer install

COPY . /app/