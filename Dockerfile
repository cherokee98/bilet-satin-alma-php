FROM php:8.1-apache

# SQLite extension'ları kur
RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite

# Apache root dizinini ayarla
ENV APACHE_DOCUMENT_ROOT=/var/www/html/php
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# Proje dosyalarını kopyala
COPY . /var/www/html/

# İzinleri ayarla
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
