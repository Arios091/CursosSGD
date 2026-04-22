# ============================================
# CursosSGD - Despliegue en Docker/Render
# ============================================

## Problema solucionado

Si ves la interfaz mal acomodada o sin CSS/JS, sigue estos pasos:

---

## Paso 1: Compilar assets LOCALMENTE

```bash
# En tu máquina local (NO en el contenedor)
cd CursosSGD

# Instalar dependencias de Node
npm install

# Compilar para producción
npm run prod
```

Esto creará `public/css/` y `public/js/`

---

## Paso 2: Verificar que existe

```bash
ls public/
# Debe existir: css/  js/  index.php  storage -> storage/app/public
```

Si no existe, ejecuta `npm run prod` localmente.

---

## Paso 3: Desplegar

```bash
# Rebuild con assets incluidos
docker-compose build --no-cache
docker-compose up -d
```

---

## Paso 4: Verificar en el contenedor

```bash
# Entrar al contenedor
docker exec -it cursos_sgd_app bash

# Verificar estructura
ls -la public/

# Verificar CSS/JS
ls public/css/
ls public/js/

# Verificar enlace storage
readlink public/storage
```

---

## Archivos editados

| Archivo | Cambio |
|---------|--------|
| `app/Providers/AppServiceProvider.php` | `URL::forceScheme('https')` en producción |
| `app/Http/Middleware/TrustProxies.php` | `$proxies = '*'` para Render |
| `Dockerfile` | DocumentRoot a `/public`, permisos correctos |
| `render-build.sh` | Script para compilar assets |

---

## Checklist de despliegue

- [ ] `npm run prod` ejecutado localmente
- [ ] `public/css/` y `public/js/` existen
- [ ] Enlace simbólico `public/storage` existe
- [ ] `APP_ENV=production` en .env
- [ ] `APP_DEBUG=false` en .env

---

## Solución rápida si ya desplegaste

```bash
# En el contenedor
docker exec -it cursos_sgd_app bash

# Compilar assets (si hay npm)
npm install
npm run prod

# O copiar manualmente desde local
docker cp ./public/css cursos_sgd_app:/var/www/html/public/
docker cp ./public/js cursos_sgd_app:/var/www/html/public/

# Recreate storage link
rm -f public/storage
ln -s ../storage/app/public public/storage

# Fix permissions
chown -R www-data:www-data public storage bootstrap/cache
chmod -R 775 public storage bootstrap/cache
```