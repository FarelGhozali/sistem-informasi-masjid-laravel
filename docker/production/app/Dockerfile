FROM php:8.5-fpm-bookworm

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends autoconf g++ make libicu-dev libpq-dev libzip-dev; \
    docker-php-ext-install -j"$(nproc)" pdo_pgsql intl zip; \
    apt-get purge -y autoconf g++ make libicu-dev libpq-dev libzip-dev; \
    rm -rf /var/lib/apt/lists/*

RUN { \
      echo 'memory_limit=256M'; \
      echo 'opcache.enable=1'; \
      echo 'opcache.validate_timestamps=0'; \
      echo 'opcache.memory_consumption=192'; \
      echo 'opcache.max_accelerated_files=20000'; \
    } > /usr/local/etc/php/conf.d/zz-masjid.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install \
      --no-dev \
      --no-interaction \
      --no-scripts \
      --prefer-dist \
      --no-progress \
      --optimize-autoloader

COPY . .

RUN php artisan package:discover --ansi \
 && chown -R www-data:www-data storage bootstrap/cache

COPY docker/production/app/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["entrypoint.sh"]
