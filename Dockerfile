# ============================================
# Dockerfile - CursosSGD Laravel Application
# ============================================

# Usar imagen oficial de PHP con Apache
FROM php:8.1-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl \
    libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_pgsql pgsql zip

# Habilitar módulos de Apache
RUN a2enmod rewrite headers ssl

# Configurar PHP
RUN echo "file_uploads = On" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/php.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/php.ini \
    && echo "upload_max_filesize = 64M" >> /usr/local/etc/php/conf.d/php.ini \
    && echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/php.ini \
    && echo "display_errors = Off" >> /usr/local/etc/php/conf.d/php.ini \
    && echo "log_errors = On" >> /usr/local/etc/php/conf.d/php.ini

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos de la aplicación
COPY . /var/www/html/

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Establecer permisos correctos
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Crear enlace simbólico para storage
RUN ln -s /var/www/html/storage/app/public /var/www/html/public/storage 2>/dev/null || true

# Exponer puerto 80
EXPOSE 80

# Puerto 443 para HTTPS
EXPOSE 443

# Iniciar Apache
CMD ["apache2-foreground"]