# ============================================
# Dockerfile - CursosSGD Laravel en Render
# ============================================
# PHP 7.4 + Composer 2.x
# ============================================

FROM php:7.4-apache

# ============================================
# Dependencias del sistema
# ============================================
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libpq-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libssl-dev \
    zip \
    unzip \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# ============================================
# Extensiones PHP
# ============================================
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql pgsql zip

# ============================================
# Módulos Apache
# ============================================
RUN a2enmod rewrite headers ssl

# ============================================
# Configuración PHP
# ============================================
RUN echo "file_uploads = On" > /usr/local/etc/php/conf.d/uploads.ini && \
    echo "max_execution_time = 300" > /usr/local/etc/php/conf.d/php.ini && \
    echo "post_max_size = 100M" > /usr/local/etc/php/conf.d/uploads2.ini && \
    echo "upload_max_filesize = 100M" > /usr/local/etc/php/conf.d/uploads3.ini && \
    echo "memory_limit = 512M" > /usr/local/etc/php/conf.d/memory.ini && \
    echo "display_errors = Off" >> /usr/local/etc/php/conf.d/php.ini && \
    echo "log_errors = On" >> /usr/local/etc/php/conf.d/php.ini

# ============================================
# ServerName para evitar warnings
# ============================================
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# ============================================
# Composer 2.x
# ============================================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ============================================
# Directorio de trabajo
# ============================================
WORKDIR /var/www/html

# ============================================
# Copiar archivos
# ============================================
COPY . /var/www/html/

# ============================================
# Reescribir configuración de Apache CORRECTAMENTE
# ============================================
RUN echo 'DocumentRoot /var/www/html/public' > /etc/apache2/sites-available/000-default.conf && \
    echo '<Directory /var/www/html/public>' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    Options Indexes FollowSymLinks MultiViews' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    AllowOverride All' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    Require all granted' >> /etc/apache2/sites-available/000-default.conf && \
    echo '</Directory>' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    ErrorLog ${APACHE_LOG_DIR}/error.log' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    CustomLog ${APACHE_LOG_DIR}/access.log combined' >> /etc/apache2/sites-available/000-default.conf

# ============================================
# Instalar dependencias PHP
# ============================================
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --ignore-platform-reqs

# ============================================
# Crear estructura de directorios
# ============================================
RUN mkdir -p storage/logs \
    && mkdir -p storage/framework/cache \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/framework/sessions \
    && mkdir -p bootstrap/cache

# ============================================
# Enlace simbólico storage
# ============================================
RUN rm -f public/storage && \
    ln -s ../storage/app/public public/storage

# ============================================
# Permisos correctos
# ============================================
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/public \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/public

# ============================================
# Limpiar cache de Laravel y Livewire
# ============================================
RUN composer dump-autoload --optimize || true
RUN php artisan config:clear && php artisan view:clear && php artisan route:clear && (php artisan livewire:publish --assets || echo "Skipping livewire assets")

EXPOSE 80

RUN php artisan migrate --force

CMD ["apache2-foreground"]