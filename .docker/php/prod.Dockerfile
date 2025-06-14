FROM composer as composer-build
WORKDIR /var/www/html 
COPY composer.json composer.lock /var/www/html/

RUN mkdir -p /var/www/html/database/{factories,seeders} \
    && composer install --no-dev --prefer-dist --no-scripts --no-autoloader --no-progress --ignore-platform-reqs 

#NPM dependencies
FROM node:14 as npm-build
WORKDIR /var/www/html 
COPY package.json package-lock.json vite.config.js /var/www/html/
COPY resources/  /var/www/html/resources/
COPY public/ /var/www/html/public/

RUN npm ci
RUN npm run build





# Actual production image
FROM php:8.1-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install --quiet --yes --no-install-recommends \
zip unzip \
&& docker-php-ext-install opcache pdo pdo_mysql \
&& pecl install -o -f redis-5.3.7 \
&& docker-php-ext-enable redis

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY .docker/php/opcache.ini $PHP_INI_DIR/conf.d/


COPY --from=composer /usr/bin/composer /usr/bin/composer 

COPY --chown=www-data --from=composer-build /var/www/html/vendor/ /var/www/html/vendor/
COPY --chown=www-data --from=npm-build /var/www/html/public/ /var/www/html/public/
COPY --chown=www-data . /var/www/html/

RUN composer dump -o \
    && composer check-platform-reqs \
    && rm -rf /usr/bin/composer

# RUN groupadd --gid 1000 appuser \
#     && useradd --uid 1000 -g appuser \
#         -G www-data,root --shell /bin/bash \
#         --create-home appuser

# USER appuser