FROM php:8.3-apache

RUN apt-get update && apt-get install -y libzip-dev zip openssh-client
RUN docker-php-ext-install zip

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY docker/php/*.ini $PHP_INI_DIR/conf.d/
RUN ln -s "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN docker-php-ext-install pdo_mysql

WORKDIR /var/www/html
