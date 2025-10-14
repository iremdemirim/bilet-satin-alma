# PHP ve Apache içeren resmi bir imajla başla
FROM php:8.2-apache

# Gerekli sistem kütüphanelerini kur (SQLite3'ün kendisi)
# Önce paket listesini güncelle, sonra libsqlite3-dev paketini kur
RUN apt-get update && apt-get install -y libsqlite3-dev

# Gerekli PHP eklentilerini (SQLite için) kur
RUN docker-php-ext-install pdo pdo_sqlite

# Apache'nin rewrite modülünü aktif et (.htaccess'in çalışması için)
RUN a2enmod rewrite

# Proje dosyalarını konteynerin web kök dizinine kopyala
COPY . /var/www/html/

# Web sunucusunun 'data' klasörüne yazma izni ver (SQLite DB dosyası için)
RUN chown -R www-data:www-data /var/www/html/data

# Apache'nin web kök dizinini 'public' klasörü olarak ayarla
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf