# ============================================
# Dockerfile - CursosSGD Laravel Application
# ============================================

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

# Instalar extensiones de PHP (Añadí bcmath y dom que Laravel suele pedir)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_pgsql pgsql zip bcmath xml

# Habilitar módulos de Apache
RUN a2enmod rewrite headers

# Configurar el DocumentRoot de Apache para que apunte a /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos de la aplicación
COPY . /var/www/html/

# --- SOLUCIÓN AL ERROR: exit code 2 ---
# Usamos --no-scripts para evitar que Laravel intente ejecutar Artisan durante el build
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --no-scripts

# Establecer permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Crear enlace simbólico para storage
RUN php artisan storage:link || true

EXPOSE 80

CMD ["apache2-foreground"]