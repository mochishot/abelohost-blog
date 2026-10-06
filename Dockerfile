FROM composer:2 AS dependencies
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --ignore-platform-req=ext-pdo_mysql --no-scripts

FROM php:8.4-apache
RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
WORKDIR /var/www/html
COPY . .
COPY --from=dependencies /app/vendor ./vendor
RUN mkdir -p var/smarty && chown -R www-data:www-data var/smarty
