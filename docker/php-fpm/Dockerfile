FROM php:7.4.1-fpm

WORKDIR "/app"

RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-source delete

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version