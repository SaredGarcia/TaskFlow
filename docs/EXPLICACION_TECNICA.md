# TaskFlow — Explicación técnica del proyecto

## 1. ¿Qué es TaskFlow?

TaskFlow es una aplicación web para registrar y dar seguimiento a actividades. El proyecto está dividido en dos aplicaciones independientes: una API REST construida con Laravel y una interfaz SPA construida con Quasar y Vue 3.

El navegador no accede directamente a la base de datos. Todas las operaciones se realizan mediante solicitudes HTTP a la API de Laravel.

## 2. Arquitectura general

```text
Usuario
  |
  v
Frontend Quasar + Vue 3
  |  Axios / JSON / HTTP
  v
API REST Laravel
  |
  +--> Form Requests (validación)
  |
  +--> ActivityController (reglas de operación)
  |
  +--> Eloquent Model
  |
  v
Base de datos SQLite o MySQL
```

### Responsabilidad de cada capa

- **Vue 3:** administra el estado visual, formularios, filtros y eventos.
- **Quasar:** proporciona componentes visuales, diálogos, notificaciones y diseño adaptable.
- **Axios:** realiza las solicitudes HTTP.
- **Laravel Routes:** define las rutas REST.
- **Form Requests:** valida los datos que llegan del cliente.
- **Controller:** coordina las operaciones CRUD.
- **Eloquent:** representa la tabla `activities` como el modelo `Activity`.
- **Migración:** define la estructura de la tabla.

## 3. Modelo de datos

La tabla `activities` contiene:

| Campo | Tipo | Regla |
|---|---|---|
| id | bigint | Identificador autoincremental |
| title | varchar(150) | Obligatorio |
| description | text | Opcional |
| status | varchar(20) | pending, in_progress, completed |
| priority | varchar(20) | low, medium, high |
| due_date | date | Opcional |
| created_at | timestamp | Automático |
| updated_at | timestamp | Automático |

Los valores internos de estado y prioridad se guardan como códigos estables. La interfaz muestra sus equivalentes en español.

## 4. Operaciones disponibles

- **Create:** `POST /api/activities` crea una actividad.
- **Read list:** `GET /api/activities` lista actividades, con búsqueda, filtro y paginación.
- **Read detail:** `GET /api/activities/{id}` consulta una actividad.
- **Update:** `PUT` o `PATCH /api/activities/{id}` modifica sus datos.
- **Delete:** `DELETE /api/activities/{id}` elimina una actividad.

### Búsqueda y filtro

Ejemplo:

`GET /api/activities?search=reporte&status=pending`

El parámetro `search` busca texto dentro del título. El parámetro `status` limita los resultados al estado solicitado.

## 5. Validación

La validación se ejecuta en dos niveles:

1. **Frontend:** evita enviar formularios incompletos y ofrece mensajes inmediatos.
2. **Backend:** Form Requests validan de nuevo la información. Esta validación es la autoridad, porque el cliente puede ser manipulado.

El backend valida:
- Título obligatorio, texto y máximo 150 caracteres.
- Descripción opcional, máximo 2000 caracteres.
- Estado dentro de los tres valores permitidos.
- Prioridad dentro de los tres valores permitidos.
- Fecha opcional con formato `YYYY-MM-DD`.

Cuando la validación falla, Laravel devuelve HTTP 422 y un objeto `errors`.

## 6. Respuestas HTTP

| Código | Significado |
|---|---|
| 200 | Consulta, actualización o eliminación exitosa |
| 201 | Recurso creado |
| 404 | Actividad inexistente |
| 422 | Datos no válidos |
| 500 | Error interno no esperado |

Los listados responden con `data` y `meta`. Las operaciones individuales responden con `data` y un mensaje cuando corresponde.

## 7. Interfaz y experiencia de usuario

La pantalla principal incluye:
- Resumen de actividades cargadas.
- Búsqueda por título.
- Filtro por estado.
- Tarjetas de actividades.
- Etiquetas visuales de estado y prioridad.
- Formulario en diálogo para crear y editar.
- Confirmación antes de eliminar.
- Acción rápida para avanzar el estado.
- Paginación.
- Indicadores de carga, error y lista vacía.
- Notificaciones de éxito o error.

El diseño utiliza una distribución adaptable: en escritorio se muestran tarjetas en dos columnas y en pantallas pequeñas se acomodan en una sola columna.

## 8. Estructura de carpetas

### Backend

- `app/Models/Activity.php`: modelo Eloquent.
- `app/Http/Controllers/Api/ActivityController.php`: operaciones REST.
- `app/Http/Requests/StoreActivityRequest.php`: validación al crear.
- `app/Http/Requests/UpdateActivityRequest.php`: validación al actualizar.
- `routes/api.php`: rutas de la API.
- `database/migrations/`: definición de la tabla.
- `database/seeders/`: datos de ejemplo.
- `tests/Feature/`: pruebas HTTP de la API.

### Frontend

- `src/App.vue`: pantalla principal y lógica de interacción.
- `src/services/api.js`: cliente Axios centralizado.
- `src/css/app.scss`: estilos de la aplicación.
- `src/main.js`: inicialización de Vue y Quasar.
- `quasar.config.js`: configuración del proyecto Quasar.

## 9. Instalación desde cero

### Backend

1. Instalar PHP 8.2+, Composer y extensiones necesarias.
2. Entrar a la carpeta `backend`.
3. Ejecutar `composer install`.
4. Copiar `.env.example` a `.env`.
5. Ejecutar `php artisan key:generate`.
6. Crear `database/database.sqlite` si se usa SQLite.
7. Ejecutar `php artisan migrate`.
8. Iniciar con `php artisan serve`.

### Frontend

1. Instalar Node.js 20+ y npm.
2. Entrar a `frontend`.
3. Ejecutar `npm install`.
4. Copiar `.env.example` a `.env`.
5. Ejecutar `npm run dev`.
6. Abrir la dirección local que muestre Quasar.

Ambos procesos deben permanecer ejecutándose en terminales separadas.

## 10. Pruebas

Desde `backend`, ejecutar `php artisan test`. Las pruebas incluidas comprueban:
- Listado de actividades.
- Creación válida.
- Rechazo de datos incorrectos.
- Actualización y eliminación.

## 11. Decisiones técnicas y límites

- No se agregó autenticación porque el enunciado indica que no se requiere.
- SQLite se utiliza como opción inicial para facilitar la instalación local; Laravel también permite configurar MySQL.
- La API pagina los resultados en grupos de 12.
- El resumen estadístico de la interfaz se calcula sobre la página actualmente cargada, no sobre el total global de la base de datos. Si se desea un tablero global, conviene añadir un endpoint de estadísticas.
- No se incluye despliegue en hosting, AWS ni Docker porque el desafío no lo exige.
- Antes de entregar, se deben ejecutar las pruebas y levantar ambos proyectos en el equipo donde se presentará la evaluación.

## 12. Cómo explicar el proyecto en una entrevista

Una explicación breve y clara:

“Separé TaskFlow en una API REST Laravel y una SPA Quasar con Vue 3. Laravel concentra la validación y las operaciones sobre la base de datos mediante Eloquent. El frontend consume la API con Axios y utiliza componentes Quasar para formularios, diálogos, notificaciones y estados de interfaz. Implementé el CRUD completo, búsqueda por título, filtro por estado y paginación. Elegí SQLite para facilitar la ejecución local, aunque la configuración puede cambiarse a MySQL. También agregué pruebas de integración para las operaciones principales y documentación de instalación.”

Es importante poder explicar cada archivo y ejecutar el proyecto sin depender de una explicación externa.
