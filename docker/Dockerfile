FROM php:8.1.7-apache
LABEL maintainer="Nick Yeoman <c@nickyeoman.com>"

WORKDIR /website

ENV APACHE_DOCUMENT_ROOT /website/public/

# APT
RUN apt-get update

# IMAP features #
RUN apt-get install -y libc-client-dev libkrb5-dev libssl-dev;

# https://stackoverflow.com/questions/49854767/docker-php7-2-fpm-cant-install-imap-module

RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl
RUN docker-php-ext-install imap
# END IMAP #

# Remove Apt list for space
RUN rm -r /var/lib/apt/lists/*

# Apache settings
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN a2enmod expires headers rewrite socache_shmcb ssl

RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN docker-php-ext-install opcache

EXPOSE 80
CMD ["apache2-foreground"]
