FROM docker.iranrepo.ir/php:8.1-fpm
RUN docker-php-ext-install pdo pdo_mysql

RUN mkdir /var/www/.composer \
        && chown www-data:www-data /var/www/.composer
COPY --from=docker.iranrepo.ir/composer:2 /usr/bin/composer /usr/bin/composer

RUN apt-get update --fix-missing -y \
        && apt-get upgrade -y \
        && apt-get install -y nano htop procps

RUN apt-get install -y libcurl4-openssl-dev
RUN docker-php-ext-install curl
########## SSL ##########
RUN apt-get install -y --no-install-recommends openssl

RUN apt update

######### Mysql client ######
RUN apt-get install -y default-mysql-client

RUN apt-get install -y --no-install-recommends libzip-dev unzip \
     && docker-php-ext-install zip \
