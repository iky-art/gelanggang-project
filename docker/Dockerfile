# Gelanggang: PHP 8.3 + Apache. mbstring sudah bawaan image resmi.
FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql \
 && a2enmod rewrite headers \
 && printf '<Directory /var/www/html>\n    AllowOverride All\n    Require all granted\n</Directory>\n' > /etc/apache2/conf-available/gelanggang.conf \
 && a2enconf gelanggang

COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html
