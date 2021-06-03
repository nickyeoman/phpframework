FROM php:8.0.3-apache-buster
LABEL maintainer="Nick Yeoman <c@nickyeoman.com>"

WORKDIR /website

ENV APACHE_DOCUMENT_ROOT /website/public/

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN a2enmod rewrite

RUN docker-php-ext-install mysqli pdo pdo_mysql

EXPOSE 80
CMD ["apache2-foreground"]
