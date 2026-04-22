# ============================================
# Dockerfile - CursosSGD (Entorno PHP 7.4)
# ============================================

FROM php:7.4-apache

# Instalar dependencias del sistema compatibles con PHP 7.4
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
    libonig-dev \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP (Añadido mbstring y bcmath que PHP 7.4 suele requerir)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_pgsql pgsql zip bcmath mbstring xml

# Habilitar módulos de Apache
RUN a2enmod rewrite headers

# Configurar el DocumentRoot de Apache para Laravel (/public)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Instalar Composer (Versión estable)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos
COPY . /var/www/html/

# Instalación de dependencias respetando el lock actual
# Usamos --ignore-platform-reqs por si el sistema de Render tiene librerías de SO más nuevas
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --no-scripts --ignore-platform-reqs

# Permisos para Storage y Cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Enlace simbólico (silencioso por si ya existe)
RUN php artisan storage:link || true

EXPOSE 80

CMD ["apache2-foreground"]