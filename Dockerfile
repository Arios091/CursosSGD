# ============================================
# Dockerfile - CursosSGD Laravel en Render
# ============================================

FROM php:7.4-apache

# Instalar dependencias del sistema
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

# Instalar extensiones de PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql pgsql zip

# Habilitar módulos de Apache
RUN a2enmod rewrite headers ssl

# Configurar PHP para producción
RUN echo "file_uploads = On" > /usr/local/etc/php/conf.d/uploads.ini && \
    echo "max_execution_time = 300" > /usr/local/etc/php/conf.d/php.ini && \
    echo "post_max_size = 100M" > /usr/local/etc/php/conf.d/uploads2.ini && \
    echo "upload_max_filesize = 100M" > /usr/local/etc/php/conf.d/uploads3.ini && \
    echo "memory_limit = 512M" > /usr/local/etc/php/conf.d/memory.ini && \
    echo "display_errors = Off" >> /usr/local/etc/php/conf.d/php.ini && \
    echo "log_errors = On" >> /usr/local/etc/php/conf.d/php.ini

# Configurar Apache - DocumentRoot debe apuntar a public
RUN echo '<VirtualHost *:80>"' > /etc/apache2/sites-available/000-default.conf && \
    echo "    ServerName localhost" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    ServerAdmin webmaster@localhost" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    DocumentRoot /var/www/html/public" >> /etc/apache2/sites-available/000-default.conf && \
    echo ' ' >> /etc/apache2/sites-available/000-default.conf && \
    echo "    <Directory /var/www/html/public>" >> /etc/apache2/sites-available/000-default.conf && \
    echo "        Options Indexes FollowSymLinks MultiViews" >> /etc/apache2/sites-available/000-default.conf && \
    echo "        AllowOverride All" >> /etc/apache2/sites-available/000-default.conf && \
    echo "        Require all granted" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    </Directory>" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    ErrorLog \${APACHE_LOG_DIR}/error.log" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    CustomLog \${APACHE_LOG_DIR}/access.log combined" >> /etc/apache2/sites-available/000-default.conf && \
    echo "</VirtualHost>" >> /etc/apache2/sites-available/000-default.conf

# Instalar Composer
COPY --from=composer:1 /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos
COPY . /var/www/html/

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# ============================================
# GESTIÓN DE ASSETS - CRÍTICO
# ============================================
# Verificar si existe public/build o public/css/public/js
# Si no existe, significa que npm run prod no se ejecutó

RUN if [ ! -d "public/build" ] && [ ! -d "public/css" ]; then \
        echo "============================================" && \
        echo "AVISO: Assets no compilados!" && \
        echo "Ejecuta 'npm install && npm run prod'" && \
        echo "antes de desplegar." && \
        echo "============================================"; \
    fi

# Crear enlace simbólico storage
RUN if [ -d "storage/app/public" ]; then \
        rm -f public/storage && \
        ln -s ../storage/app/public public/storage && \
        echo "✓ Storage link creado"; \
    fi

# Crear directorio bootstrap/cache
RUN mkdir -p bootstrap/cache && \
    mkdir -p storage/logs && \
    mkdir -p storage/framework/cache && \
    mkdir -p storage/framework/views && \
    mkdir -p storage/framework/sessions

# Permisos correctos - www-data después de copiar
RUN chown -R www-data:www-data storage bootstrap/cache public && \
    chmod -R 775 storage bootstrap/cache public && \
    echo "✓ Permisos establecidos"

# Limpiar cache de Laravel
RUN php artisan config:clear && \
    php artisan cache:clear && \
    php artisan view:clear && \
    php artisan route:clear && \
    echo "✓ Cache limpiado"

# Exponer puerto 80
EXPOSE 80

CMD ["apache2-foreground"]