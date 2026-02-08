FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    postgresql-dev \
    libpq \
    && docker-php-ext-install pdo_pgsql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader --no-scripts --prefer-dist

COPY . .



EXPOSE 9000

CMD ["php-fpm"]