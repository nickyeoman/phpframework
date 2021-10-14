FROM php:8.0.11-apache-bullseye
LABEL maintainer="Nick Yeoman <c@nickyeoman.com>"

WORKDIR /website

ENV APACHE_DOCUMENT_ROOT /website/public/

RUN apt-get update
RUN apt-get install -y ssl-cert

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN a2enmod expires headers rewrite socache_shmcb ssl

RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN docker-php-ext-install opcache

EXPOSE 80
CMD ["apache2-foreground"]
