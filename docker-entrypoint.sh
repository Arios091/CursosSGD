#!/bin/bash
# ============================================
# Entry Point - CursosSGD Docker
# ============================================

set -e

echo "============================================"
echo "Configurando CursosSGD..."
echo "============================================"

# Esperar a que PostgreSQL esté listo
echo "Esperando base de datos..."
counter=0
until php artisan migrate:status 2>/dev/null || [ $counter -eq 30 ]; do
    sleep 2
    counter=$((counter+1))
done

# ============================================
# IMPORTANTE: Crear enlace simbólico de storage
# ============================================
echo "Creando enlace simbólico de storage..."
rm -rf /var/www/html/public/storage
ln -sf /var/www/html/storage/app/public /var/www/html/public/storage
echo "✓ Enlace simbólico creado"

# Establecer permisos
echo "Estableciendo permisos..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/public
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/public
echo "✓ Permisos establecidos"

# Limpiar cache
echo "Limpiando cache..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
echo "✓ Cache limpiado"

# Verificar estructura
echo "Verificando estructura..."
if [ -L "/var/www/html/public/storage" ]; then
    echo "✓ public/storage -> $(readlink /var/www/html/public/storage)"
else
    echo "✗ ERROR: public/storage no es enlace simbólico"
fi

if [ -d "/var/www/html/public/build" ]; then
    echo "✓ public/build existe con $(ls /var/www/html/public/build | wc -l) archivos"
else
    echo "✗ AVISO: public/build no existe (CSS/JS no compilado)"
    echo "  Ejecuta 'npm run prod' localmente antes de desplegar"
fi

echo "============================================"
echo "Iniciando Apache..."
echo "============================================"

# Iniciar Apache
exec apache2-foreground