FROM php:8.1-apache

# تثبيت التبعيات
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql

# نسخ الملفات مباشرة (بدون مجلد فرعي)
COPY .htaccess apache-config.conf db_connect.php favicon.ico get_ip.php index.php retrieve_data.php /var/www/html/

# تكوين Apache
RUN cp /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/000-default.conf.bak && \
    cp /var/www/html/apache-config.conf /etc/apache2/sites-available/000-default.conf && \
    chown -R www-data:www-data /var/www/html && \
    a2enmod rewrite headers && \
    a2ensite 000-default

EXPOSE 80
CMD ["apache2-foreground"]