FROM php:7.4-fpm

RUN apt-get update && \
    apt-get install -y \
    libzip-dev \
    zip 

RUN docker-php-ext-install pdo_mysql zip

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && mv composer /usr/local/bin/composer

WORKDIR /usr/src/app

RUN PATH=$PATH:/usr/src/app/vendor/bin:bin

CMD php-fpm 
