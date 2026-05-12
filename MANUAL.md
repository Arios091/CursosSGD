# Manual de Usuario — Sistema de Gestión de Docencia (Cursos SGD)

## Roles de usuario

| Rol | Permisos |
|---|---|
| **Estudiante** | Inscribirse en cursos, ver materiales, rendir evaluaciones, obtener certificado |
| **Docente** | Crear/editar/eliminar sus propios cursos, además de lo que hace un estudiante |
| **Admin** | Gestionar todos los cursos (crear, editar, eliminar cualquier curso) |
| **Admin Global** | Igual que Admin + gestión de usuarios y configuración de la página |

---

## 1. Registro e inicio de sesión

1. Ve a la página principal y haz clic en **"Registrarse"**
2. Completa tus datos (nombre, correo, contraseña)
3. Inicia sesión con tu correo y contraseña
4. Serás redirigido al **Dashboard** (`/home`)

---

## 2. Explorar cursos (Dashboard)

En el Dashboard verás:
- **Cursos disponibles** — todos los cursos públicos
- **Mis cursos** — cursos en los que estás inscrito (progreso, estado)
- **Estadísticas** — total de cursos, completados, tasa de aprobación

Para inscribirte en un curso, haz clic en **"Comenzar curso"**.

---

## 3. Ver curso y progreso

### Estudiante
1. Desde el Dashboard, haz clic en **"Ver curso"** en el curso inscrito
2. Se abre el **visor del curso** con:
   - **Barra lateral izquierda**: lista de módulos y materiales
   - **Panel central**: contenido del material (video o PDF)
3. El progreso se guarda automáticamente:
   - **Video**: al ver el 90% se marca como completado
   - **PDF**: al hacer scroll hasta el final se marca como completado
4. Al completar todos los materiales de un módulo, aparece el botón **"Rendir cuestionario"**

### Docente/Admin
- Puedes ver cualquier curso (incluso sin estar inscrito)
- Puedes editar módulos, materiales y preguntas desde la barra lateral

---

## 4. Módulos y materiales

Cada curso se compone de **módulos** (unidades). Cada módulo contiene **materiales** que pueden ser:

| Tipo | Descripción |
|---|---|
| **Video** | Reproducción en línea (YouTube, Vimeo, Google Drive) |
| **PDF** | Documento para leer en línea o descargar |
| **Cuestionario** | Evaluación del módulo (si aplica) |

### Navegación
- Haz clic en un material de la barra lateral para verlo
- El sistema recuerda tu último material visto
- Los materiales completados tienen un check verde

---

## 5. Cuestionarios

### Cuestionario de módulo
1. Completa todos los materiales del módulo
2. Haz clic en **"Rendir cuestionario"**
3. Responde las preguntas (selección múltiple)
4. Envía tus respuestas
5. El sistema muestra tu nota y las respuestas correctas/incorrectas
6. **Se requiere 100%** para aprobar el módulo

### Evaluación final
1. Completa todos los módulos y sus cuestionarios
2. Desde el visor del curso, haz clic en **"Evaluación final"**
3. Responde y envía
4. **Se requiere 80%** para aprobar el curso

---

## 6. Certificado

Al aprobar la evaluación final:
1. El curso se marca como **completado**
2. Serás redirigido a la página de felicitaciones
3. Podrás **ver** o **descargar** tu certificado en PDF
4. El certificado incluye un **código único** y un **código QR** para verificación

### Verificar un certificado
Cualquier persona (sin login) puede verificar un certificado en:
```
https://cursossgd.onrender.com/verificar/{codigo}
```
El código está impreso en el certificado.

---

## 7. Perfil

En `/perfil` puedes ver:
- Tu información personal
- Lista de cursos completados con enlaces a certificados
- Tu rol en el sistema

---

## 8. Gestión de cursos (Docente/Admin)

### Crear curso
1. Haz clic en **"Crear curso"** en el menú
2. **Paso 1**: Datos generales (título, descripción, categoría, carga horaria, imagen)
   - **Imagen recomendada**: 1200 × 675 px (relación 16:9), formato JPG o PNG, máximo 5 MB
   - Las imágenes se muestran en tarjetas de 300×160 px y thumbnails de 80×60 px usando `object-fit: cover`; una imagen 16:9 se verá correctamente en todos los tamaños sin recortes importantes
3. **Paso 2**: Agregar módulos y materiales:
   - **Video**: URL de YouTube, Vimeo o Google Drive
   - **PDF**: Subir archivo PDF
   - **Cuestionario**: Agregar preguntas con opciones (marcar la correcta)
4. **Paso 3**: Revisar y guardar

### Editar curso
1. Desde el visor del curso, haz clic en **"Editar"**
2. Puedes modificar, agregar o eliminar módulos, materiales y preguntas

### Eliminar curso
1. Desde la lista de cursos (`/cursos`), busca el curso
2. Haz clic en **"Eliminar"** y confirma en la ventana emergente

---

## 9. Administración (Admin Global)

### Gestión de usuarios
Ruta: `/admin/usuarios`
- Ver lista de usuarios
- Ver detalle de cada usuario (cursos, progreso)
- Cambiar rol de usuario
- Restablecer contraseña
- Eliminar usuario

### Configuración de página
Ruta: `/admin/page-settings`
- Personalizar título y subtítulo de la página principal
- Cambiar colores institucionales
- Editar información de contacto (teléfono, email, dirección)
- **Imágenes del carrusel**: se recomienda **1200 × 675 px** (relación 16:9), formato JPG o PNG, máximo **5 MB**

---

## 10. Políticas importantes

- **Un curso a la vez**: solo puedes tener un curso en progreso simultáneamente
- **Material bloqueado**: los materiales de un módulo deben verse en orden
- **Cuestionario bloqueado**: debes completar todos los materiales antes de rendir el cuestionario
- **Evaluación final bloqueada**: debes completar todos los módulos y aprobar sus cuestionarios antes de acceder a la evaluación final
- **Aprobación de módulo**: se requiere 100% de respuestas correctas
- **Aprobación de evaluación final**: se requiere 80% de respuestas correctas

---

## 11. Solución de problemas comunes

| Problema | Causa probable | Solución |
|---|---|---|
| No puedo inscribirme | Ya tienes un curso en progreso | Termina o reinicia el curso actual |
| No veo el botón "Comenzar" | Ya estás inscrito | Ve a "Mis cursos" en el Dashboard |
| El video no carga | URL no soportada | Usa YouTube, Vimeo o Google Drive |
| El PDF no se muestra | Archivo no subido correctamente | Re-subir el PDF desde Editar curso |
| Error 500 al abrir curso | Sesión expirada | Cierra sesión y vuelve a iniciar |
| No puedo crear cursos | Tu rol no tiene permisos | Contacta al administrador |
