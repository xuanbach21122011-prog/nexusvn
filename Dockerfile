FROM php:8.2-cli
RUN docker-php-ext-install pdo_pgsql
WORKDIR /usr/src/app
COPY . .
CMD php -S 0.0.0.0:$PORT -t .
