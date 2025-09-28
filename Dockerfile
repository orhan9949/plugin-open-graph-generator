FROM php:8.2-fpm

RUN apt-get update && apt-get install -y libmagickwand-dev imagemagick default-mysql-client libfreetype6-dev libjpeg62-turbo-dev libpng-dev
RUN pecl install imagick
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-install pdo_mysql mysqli opcache

RUN docker-php-ext-enable mysqli opcache gd imagick

RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
RUN chmod +x wp-cli.phar
RUN mv wp-cli.phar /usr/local/bin/wp