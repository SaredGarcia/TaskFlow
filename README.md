# TaskFlow — Gestor de actividades

Aplicación web para administrar actividades personales o de equipo. Incluye una API REST en Laravel y una SPA desarrollada con Quasar + Vue 3.

## Tecnologías

- Backend: PHP 8.2+, Laravel 12, Eloquent, Form Requests.
- Frontend: Node.js 20+, Vue 3, Quasar 2, Vite, Axios.
- Base de datos: SQLite por defecto (también compatible con MySQL mediante `.env`).
- Pruebas: PHPUnit.

## Requisitos previos

- PHP 8.2 o superior y extensiones habituales de Laravel (`pdo`, `pdo_sqlite` o `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`).
- Composer 2.
- Node.js 20 o superior y npm.
- Git (opcional).

## Estructura

```text
TaskFlow/
├── backend/       # API REST Laravel
├── frontend/      # SPA Quasar + Vue 3
├── docs/          # Documentación adicional
└── README.md
```

## 1. Configurar el backend

Abre una terminal en `TaskFlow/backend`:

```bash
composer install
cp .env.example .env
php artisan key:generate
```

SQLite (configuración predeterminada):

```bash
touch database/database.sqlite
php artisan migrate
```

En Windows PowerShell, crea el archivo con:

```powershell
New-Item database/database.sqlite -ItemType File
```

El `.env.example` ya está preparado para SQLite. Si prefieres MySQL, configura `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` en `.env`, crea la base de datos y ejecuta `php artisan migrate`.

Inicia la API:

```bash
php artisan serve
```

La API queda disponible en `http://127.0.0.1:8000/api`.

## 2. Configurar el frontend

En otra terminal, desde `TaskFlow/frontend`:

```bash
npm install
cp .env.example .env
npm run dev
```

En Windows PowerShell puedes copiar con:

```powershell
Copy-Item .env.example .env
```

Abre la URL local que indique Quasar (normalmente `http://localhost:9000`). El frontend consume exclusivamente la API Laravel. La variable `VITE_API_URL` permite cambiar la dirección base; por defecto apunta a `http://127.0.0.1:8000/api`.

## 3. Compilar para producción

Desde `frontend`:

```bash
npm run build
```

El resultado se genera en `frontend/dist/spa`. Para servirlo en producción, configura un servidor web para entregar esos archivos y configura `VITE_API_URL` con la URL pública de la API.

## 4. Endpoints REST

| Método | Ruta | Uso |
|---|---|---|
| GET | `/api/activities` | Listar, buscar y filtrar |
| POST | `/api/activities` | Crear |
| GET | `/api/activities/{id}` | Consultar detalle |
| PUT/PATCH | `/api/activities/{id}` | Actualizar |
| DELETE | `/api/activities/{id}` | Eliminar |

Parámetros opcionales de listado:
- `search`: busca coincidencias en el título.
- `status`: `pending`, `in_progress` o `completed`.

Ejemplos:

```bash
curl "http://127.0.0.1:8000/api/activities?search=reporte&status=pending"
curl "http://127.0.0.1:8000/api/activities/1"
```

Crear una actividad:

```bash
curl -X POST http://127.0.0.1:8000/api/activities \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"title":"Preparar presentación","description":"Revisar diapositivas","status":"pending","priority":"high","due_date":"2026-12-01"}'
```

Valores permitidos:
- `status`: `pending`, `in_progress`, `completed`.
- `priority`: `low`, `medium`, `high`.
- `due_date`: fecha `YYYY-MM-DD` o `null`.

Las respuestas exitosas usan la estructura `data` y, para listados, `meta`. Los errores de validación devuelven HTTP 422 con `message` y `errors`. Los recursos inexistentes devuelven HTTP 404.

## 5. Pruebas

Desde `backend`:

```bash
php artisan test
```

## 6. Decisiones y alcance

- No se implementa autenticación ni administración de usuarios porque el reto explícitamente no lo solicita.
- La fecha de creación se genera automáticamente con Eloquent.
- La eliminación requiere confirmación en la interfaz.
- Los estados y prioridades se almacenan con valores internos en inglés y se presentan en español.
- El backend valida los datos aunque el frontend también valide los formularios.
- Se incluyen estados de carga, error, lista vacía y confirmaciones.
