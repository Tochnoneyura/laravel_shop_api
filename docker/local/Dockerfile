# Local PHP 7.4 base already available on this PC.
FROM laravel-shop-php:7.4-base
COPY --from=composer:2.2 /usr/bin/composer /usr/local/bin/composer
WORKDIR /var/www/html
ENV COMPOSER_HOME=/tmp/composer
ENTRYPOINT []
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]