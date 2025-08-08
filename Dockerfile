FROM php:8.1-apache

# نسخ ملفات التطبيق
COPY . /var/www/html/

# نسخ ملف إعدادات Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# تفعيل الإعدادات
RUN a2enmod rewrite && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html