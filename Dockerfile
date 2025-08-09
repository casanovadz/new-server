FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html

# نسخ الملفات الفردية بدلاً من المجلد
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf
COPY retrieve_data.php get_ip.php db_connect.php index.php .htaccess favicon.ico ./

RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite \
    && a2enmod headers

EXPOSE 80
CMD ["apache2-foreground"]