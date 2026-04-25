FROM php:8.2-apache

# Włącz mod_rewrite (wymagane dla routera MVC)
RUN a2enmod rewrite

# Zainstaluj rozszerzenie PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

RUN docker-php-ext-install calendar

# Ustaw DocumentRoot na /var/www/html
# Zezwól na .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

EXPOSE 80
