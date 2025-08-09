FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql

COPY my-php-project/ /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite \
    && a2enmod headers

COPY my-php-project/apache-config.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
CMD ["apache2-foreground"]