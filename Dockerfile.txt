# استخدم صورة PHP مع Apache الرسمية (PHP 8.1 مع Apache)
FROM php:8.1-apache

# انسخ ملفات المشروع إلى مجلد الويب داخل الحاوية
COPY . /var/www/html/

# افتح المنفذ 80 (HTTP)
EXPOSE 80
