# MÓDULO DE ENCUESTAS DE SATISFACCIÓN
## CPIFP Bajo Aragón – MVC de Encuestas

---

## DESCRIPCIÓN GENERAL

Módulo para gestionar encuestas de satisfacción del centro educativo.  
Sigue exactamente la misma arquitectura MVC del resto de la aplicación.

### Tipos de encuesta implementados:
1. **Encuesta de alumnos** – por profesor, módulo y trimestre (3 veces al año)
2. **Encuesta de empresas** – anual, para empresas colaboradoras

---

## ESTRUCTURA DE DIRECTORIOS

```
encuestas/
├── App/
│   ├── config/
│   │   └── configurar.php          ← Ajusta aquí DB, URLs y email
│   ├── controladores/
│   │   ├── Encuestas.php           ← Back-end: CRUD encuestas + resultados
│   │   ├── Empresas.php            ← Back-end: gestión de empresas
│   │   ├── Preguntas.php           ← Back-end: plantilla de preguntas (admin)
│   │   ├── Responder.php           ← Front-end PÚBLICO (sin login)
│   │   ├── Inicio.php              ← Dashboard back-end
│   │   └── Login.php
│   ├── modelos/
│   │   ├── EncuestaModelo.php      ← Toda la lógica de BD de encuestas
│   │   └── EmpresaModelo.php       ← Gestión de empresas
│   ├── vistas/
│   │   ├── inc/
│   │   │   ├── header.php          ← Header con nav (back-end)
│   │   │   ├── header_publico.php  ← Header sin nav (front-end público)
│   │   │   └── footer.php
│   │   ├── encuestas/
│   │   │   ├── index.php           ← Listado con filtros
│   │   │   ├── nueva.php           ← Formulario creación
│   │   │   ├── editar.php          ← Edición
│   │   │   ├── ver.php             ← Detalle + resultados + gráficos
│   │   │   ├── formulario.php      ← PÚBLICO: responder encuesta
│   │   │   ├── gracias.php         ← PÚBLICO: confirmación tras responder
│   │   │   ├── encuesta_cerrada.php
│   │   │   └── encuesta_no_encontrada.php
│   │   ├── empresas/
│   │   │   ├── index.php
│   │   │   ├── nueva.php
│   │   │   └── editar.php
│   │   ├── preguntas/
│   │   │   └── index.php           ← Edición inline de plantilla
│   │   ├── resultados/
│   │   │   └── estadisticas.php    ← Informe global por curso
│   │   └── index.php               ← Dashboard
│   ├── helpers/
│   │   └── funciones.php
│   ├── librerias/                  ← Copiar desde otro MVC del proyecto
│   │   ├── Base.php
│   │   ├── Controlador.php
│   │   ├── Core.php
│   │   ├── Sesion.php
│   │   ├── EnviarEmail.php
│   │   └── externas/
│   │       ├── PHPMailer.php
│   │       └── SMTP.php
│   └── iniciador.php
├── public/
│   ├── index.php
│   ├── .htaccess
│   ├── css/
│   │   └── encuestas.css
│   └── js/
│       └── encuestas.js
└── encuestas_tablas.sql            ← ¡Ejecutar primero en BD!
```

---

## INSTALACIÓN

### 1. Base de datos
Ejecutar el script SQL:
```sql
-- En MySQL/MariaDB, sobre la BD 'calidapp':
SOURCE encuestas_tablas.sql;
```

### 2. Configuración
Editar `App/config/configurar.php`:
- `RUTA_URL` → `/encuestas` (o la ruta donde se sirva)
- Credenciales de BD
- Datos de SMTP para el email

### 3. Librerías
Copiar desde otro MVC del proyecto las librerías externas de PHPMailer:
```
cp -r orientacion/App/librerias/externas encuestas/App/librerias/
```

### 4. Servidor web (Apache)
```apache
# En el VirtualHost o httpd.conf, añadir alias:
Alias /encuestas /ruta/al/proyecto/encuestas/public
<Directory /ruta/al/proyecto/encuestas/public>
    AllowOverride All
    Require all granted
</Directory>
```

---

## ROLES Y PERMISOS

| Rol | id_rol | Acceso |
|-----|--------|--------|
| Profesor | 100 | Solo ve sus propias encuestas y resultados |
| Jefe de departamento | 200 | Ve y crea encuestas, gestiona empresas |
| Equipo directivo / Admin | 300 | Acceso completo + editar plantillas |

---

## FLUJO DE USO

### Encuesta de alumnos:
1. Un usuario con rol ≥ 200 crea una encuesta seleccionando:
   - Tipo: "Encuesta de alumnos"
   - Profesor + Módulo (de `cpifp_profesor_modulo`)
   - Trimestre (1, 2 o 3)
   - Curso académico y fechas
2. El sistema copia automáticamente las preguntas activas de la plantilla
3. Se genera un **enlace público con token único**
4. Se comparte el enlace con el grupo de alumnos (por Moodle, email, etc.)
5. Los alumnos responden sin necesidad de login
6. El profesor (o dirección) consulta resultados en tiempo real

### Encuesta de empresas:
1. Registrar la empresa en "Empresas" → se genera su token de acceso
2. Crear encuesta de tipo "Empresas" vinculada a esa empresa
3. Enviar el enlace (o usar el botón "Enviar email") a la empresa
4. La empresa responde con el enlace personalizado

---

## TABLAS CREADAS (prefijo `en_`)

| Tabla | Descripción |
|-------|-------------|
| `en_tipo_encuesta` | Tipos: alumnos / empresas |
| `en_plantilla_pregunta` | Preguntas editables por tipo |
| `en_encuesta` | Encuesta concreta (histórico completo) |
| `en_pregunta` | Preguntas copiadas a cada encuesta (inmutables) |
| `en_respuesta` | Una fila por usuario que responde |
| `en_respuesta_detalle` | Puntuación (1-10) por pregunta |
| `en_empresa` | Empresas colaboradoras |

---

## RUTAS PRINCIPALES

| URL | Descripción |
|-----|-------------|
| `/encuestas` | Dashboard (back-end) |
| `/encuestas/encuestas` | Listado de encuestas |
| `/encuestas/encuestas/nueva` | Crear encuesta |
| `/encuestas/encuestas/ver/{id}` | Ver resultados |
| `/encuestas/encuestas/estadisticas` | Informe por curso |
| `/encuestas/empresas` | Gestión de empresas |
| `/encuestas/preguntas` | Editar plantilla preguntas |
| `/encuestas/responder/{token}` | **PÚBLICO** – formulario de respuesta |
