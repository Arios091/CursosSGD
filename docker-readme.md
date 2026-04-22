# ============================================
# CursosSGD - Docker Setup
# ============================================

## Requisitos previos

- Docker instalado
- Docker Compose instalado

## Estructura de archivos

```
CursosSGD/
├── Dockerfile           # Imagen de la aplicación
├── docker-compose.yml   # Orquestación de servicios
├── .env.docker       # Variables de entorno para Docker
└── .dockerignore    # Archivos excluidos del build
```

## quickstart

### 1. Configurar variables de entorno

```bash
# Copiar variables de entorno
cp .env.docker .env

# Generar nueva clave (opcional)
php artisan key:generate
```

### 2. Construir e iniciar servicios

```bash
# Construir e iniciar todos los servicios
docker-compose up -d --build

# Ver logs
docker-compose logs -f app
```

### 3. Instalar dependencias

```bash
# Instalar dependencias de PHP
docker-compose exec app composer install

# Instalar dependencias de Node
docker-compose exec app npm install
```

### 4. Configurar base de datos

```bash
# Crear tablas
docker-compose exec app php artisan migrate

# Crear usuario admin (opcional)
docker-compose exec app php artisan db:seed
```

### 5. Compilar assets

```bash
# Desarrollo
docker-compose exec app npm run dev

# Producción
docker-compose exec app npm run prod
```

## Servicios disponibles

| Servicio | Puerto | Descripción |
|----------|--------|----------|
| app      | 8000   | Aplicación Laravel |
| db       | 5432   | PostgreSQL |
| mail     | 8025   | Mailhog Web UI |
| SMTP     | 1025   | Servidor de correo |

## Comandos útiles

```bash
# Ver estado de contenedores
docker-compose ps

# Reiniciar servicios
docker-compose restart

# Detener servicios
docker-compose stop

# Eliminar contenedores
docker-compose down

# Eliminar contenedores y volúmenes
docker-compose down -v

# Acceso a shell del contenedor
docker-compose exec app bash

# Ejecutar comandos artisan
docker-compose exec app php artisan

# Ver logs de un servicio
docker-compose logs -f db
```

## Desarrollo

Para desarrollo local, usa `http://localhost:8000`

## Producción

Para producción, considera:
- Usar `APP_ENV=production`
- Configurar HTTPS
- Usar base de datos externa (RDS, etc.)
- Configurar cache Redis
- Usar cola de trabajos con Redis
- Configurar S3 para archivos
- Usar Cloudflare o similar

## Problemas comunes

### Error de permisos

```bash
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chown -R www-data:www-data /var/www/html/bootstrap/cache
```

### Migraciones fallidas

```bash
docker-compose exec app php artisan migrate:fresh --seed
```

### Instalar composer

```bash
docker-compose run --rm composer install
```