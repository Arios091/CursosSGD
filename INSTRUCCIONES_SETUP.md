# 🎯 INSTRUCCIONES PARA APLICAR LOS CAMBIOS

## Estado Actual
✅ Todos los archivos han sido modificados
✅ No hay errores de sintaxis
✅ Todos los problemas han sido solucionados

## Paso 1: Ejecutar Migraciones (CRÍTICO)
Esta es la parte MÁS IMPORTANTE. La base de datos debe tener los nuevos campos.

```bash
# En la terminal del proyecto
php artisan migrate
```

**Si ya ejecutaste migraciones antes y necesitas rollback:**
```bash
php artisan migrate:rollback
php artisan migrate
```

## Paso 2: Crear un Usuario de Prueba (Opcional)

Si necesitas probar con un usuario docente:

```bash
# Abrir tinker
php artisan tinker

# Crear usuario admin
User::create([
    'name' => 'Admin Test',
    'email' => 'admin@test.com',
    'password' => Hash::make('password'),
    'role' => 'admin'
])

# Crear usuario docente
User::create([
    'name' => 'Docente Test',
    'email' => 'docente@test.com',
    'password' => Hash::make('password'),
    'role' => 'docente'
])

exit
```

## Paso 3: Configurar Almacenamiento (Para imágenes)

```bash
# Crear enlace simbólico para acceder a imágenes públicamente
php artisan storage:link
```

## Paso 4: Verificar que Todo Funciona

1. Ir a `http://tu-app/home` (requiere estar autenticado)
2. Ser admin o docente
3. Hacer clic en "Crear Nuevo Curso"
4. Deberías ver: 
   - Paso 1/3: Datos Básicos
   - Barra de progreso
   - Botones: Cancelar, Siguiente
5. Llenar datos y hacer clic "Siguiente"
6. Deberías ver Paso 2: Módulos
7. Agregar módulos si quieres
8. Siguiente a Paso 3: Resumen
9. Revisar datos y confirmar
10. Hacer clic "Crear Curso"

## Paso 5: Revisar Logs si Hay Problemas

```bash
# Ver últimos 50 líneas del log
tail -50 storage/logs/laravel.log

# Ver log en tiempo real
tail -f storage/logs/laravel.log
```

---

## 🔍 CHECKLIST DE VERIFICACIÓN

Después de hacer los cambios:

- [ ] Ejecutaste `php artisan migrate`
- [ ] Ejecutaste `php artisan storage:link` 
- [ ] Tienes usuarios con roles 'admin' o 'docente'
- [ ] Puedes acceder a `/crear.curso` si estás autenticado como admin/docente
- [ ] El nombre de la ruta se define como `'crear.curso'` (no `'cursos.create.livewire'`).
- [ ] El formulario muestra los 3 pasos correctamente
- [ ] Puedes navegar entre pasos
- [ ] Puedes crear un curso exitosamente
- [ ] El curso aparece en la lista de cursos
- [ ] Puedes editar el curso si eres propietario
- [ ] Los datos guardados son correctos (título, descripción, carga horaria)

---

## 📞 INFORMACIÓN DE CONTACTO / SOPORTE

Si encuentras problemas:

1. Revisa los logs: `storage/logs/laravel.log`
2. Verifica que ejecutaste las migraciones
3. Verifica que tienes un usuario con rol 'docente' o 'admin'
4. Verifica permisos de carpeta `storage/` y `bootstrap/cache/`

---

## 🎓 INFORMACIÓN TÉCNICA

### Flujo de Datos:
1. Usuario rellena formulario (Paso 1)
2. Sistema valida datos (Paso 1)
3. Usuario agrega módulos (Paso 2)
4. Sistema valida módulos (Paso 2)
5. Usuario ve resumen (Paso 3)
6. Usuario confirma creación (Paso 3)
7. Sistema guarda curso, módulos e imagen (si existe)
8. Usuario es redirigido a home con mensaje de éxito

### Ubicación de Archivos:
- Componente Livewire: `app/Http/Livewire/CreateCurso.php`
- Vista del componente: `resources/views/livewire/create-curso.blade.php` (ahora contiene lógica para módulos con materiales y cuestionarios, y evaluación final)
- Wrapper de la vista: `resources/views/cursos/create-livewire.blade.php`
- Imágenes guardadas: `storage/app/public/cursos/`

### Archivos de Configuración:
- Routes: `routes/web.php` (ruta: `/crear.curso`, nombre de ruta `crear.curso`)
- Policy: `app/Policies/CursoPolicy.php`
- Model: `app/Models/Curso.php`, `app/Models/Modulo.php`

---

## ✅ LISTO PARA USAR

Una vez hayas completado estos pasos, el sistema estará completamente funcional.

¿Tienes dudas? Revisa `CAMBIOS_REALIZADOS.md` para más detalles.
