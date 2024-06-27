# Gunakan image resmi PHP 8.2 dengan Apache
FROM php:8.2-apache

# Set environment variable
ENV DEBIAN_FRONTEND=noninteractive
ENV APP_ENV=docker-build

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    mariadb-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install exif \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install opcache \
    && docker-php-ext-install intl \
    && docker-php-ext-install xml \
    && docker-php-ext-install zip

# Install Composer versi 2.2
RUN curl -sS https://getcomposer.org/installer | php -- --version=2.2.0 --install-dir=/usr/bin --filename=composer

# Set working directory
WORKDIR /var/www/html

# Salin file aplikasi Laravel ke container
COPY . .

# Install dependencies menggunakan Composer
RUN composer install --no-interaction --optimize-autoloader

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Jalankan storage link
# RUN php artisan optimize:clear
RUN php artisan storage:link
# RUN chmod -R 755 public/storage
# RUN chown -R www-data:www-data public/storage

# Copy dan set file Apache config untuk Laravel
COPY ./apache/laravel.conf /etc/apache2/sites-available/000-default.conf

# Enable Apache mod_rewrite untuk Laravel
RUN a2enmod rewrite

# Copy Cloud SQL Proxy dan entrypoint script
COPY cloud_sql_proxy /cloud_sql_proxy
COPY entrypoint.sh /entrypoint.sh

COPY ta-sentinel-photo-profile.json /ta-sentinel-photo-profile.json

# Berikan izin eksekusi untuk entrypoint
RUN chmod +x /entrypoint.sh

# Expose port yang diberikan oleh Cloud Run
ENV PORT 8080
EXPOSE 8080

# Jalankan Apache melalui entrypoint script
CMD ["/entrypoint.sh"]
