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
# Composer 2.x (CRÍTICO - Corregido)
# ============================================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ============================================
# Configurar Apache DocumentRoot hacia public
# ============================================
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf && \
    sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf && \
    echo '<Directory "/var/www/html/public">' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    Options Indexes FollowSymLinks MultiViews' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    AllowOverride All' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    Require all granted' >> /etc/apache2/sites-available/000-default.conf && \
    echo '</Directory>' >> /etc/apache2/sites-available/000-default.conf

# ============================================
# Directorio de trabajo
# ============================================
WORKDIR /var/www/html

# ============================================
# Copiar archivos de aplicación
# ============================================
COPY . /var/www/html/

# ============================================
# Instalar dependencias PHP
# --ignore-platform-reqs evita bloqueos por librerías del SO
# ============================================
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --ignore-platform-reqs

# ============================================
# Crear estructura de directorios necesaria
# ============================================
RUN mkdir -p storage/logs \
    && mkdir -p storage/framework/cache \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/framework/sessions \
    && mkdir -p bootstrap/cache \
    && echo "✓ Directorios creados"

# ============================================
# Enlace simbólico storage (CRÍTICO para assets)
# ============================================
RUN if [ -d "storage/app/public" ]; then \
        rm -f public/storage && \
        ln -s ../storage/app/public public/storage && \
        echo "✓ Storage link creado"; \
    fi

# ============================================
# Permisos correctos para www-data
# ============================================
RUN chown -R www-data:www-data storage bootstrap/cache public && \
    chmod -R 775 storage bootstrap/cache public && \
    echo "✓ Permisos establecidos"

# ============================================
# Limpiar cache de Laravel
# ============================================
RUN php artisan config:clear \
    && php artisan cache:clear \
    && php artisan view:clear \
    && php artisan route:clear \
    && echo "✓ Cache limpiado"

# ============================================
# Exponer puerto
# ============================================
EXPOSE 80

CMD ["apache2-foreground"]