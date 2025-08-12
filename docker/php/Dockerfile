FROM php:8.3-fpm-alpine

ARG TZ=America/New_York
ENV TZ=${TZ}

# Alpine uses apk (not apt). tzdata lets the container know your TZ.
RUN apk update && apk add --no-cache \
    tzdata \
    libzip-dev \
    zip \
    unzip \
    git \
    nano \
    mariadb-client \
    bash \
    && docker-php-ext-install pdo pdo_mysql mysqli

# Optional: Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php

# Note: PHP does not expand env vars inside .ini. Use concrete values here.
COPY php.ini /usr/local/etc/php/conf.d/zz-techassist.ini

WORKDIR /var/www/html
