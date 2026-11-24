# TaskFlow

<p align="center">
  <strong>Gestión de actividades de forma organizada y sencilla.</strong>
</p>

<p align="center">
  Aplicación web full stack para crear, organizar y administrar actividades mediante una interfaz desarrollada con Vue 3 y una API REST construida con Laravel.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/Vue.js-3-4FC08D?style=for-the-badge&logo=vuedotjs&logoColor=white" />
  <img src="https://img.shields.io/badge/Quasar-2-1976D2?style=for-the-badge&logo=quasar&logoColor=white" />
</p>

---

## Descripción

TaskFlow es una aplicación web diseñada para facilitar la administración de actividades personales o de equipo.

Permite registrar actividades, consultar sus detalles, modificar información y controlar su progreso mediante diferentes estados y prioridades.

El proyecto utiliza una arquitectura separada: el frontend se comunica con el backend a través de una API REST, lo que permite mantener organizada la lógica de presentación y la gestión de datos.

## Funcionalidades

- Crear nuevas actividades.
- Consultar el listado de actividades.
- Visualizar información individual.
- Editar y actualizar actividades.
- Eliminar actividades con confirmación.
- Buscar actividades por título.
- Filtrar por estado.
- Asignar niveles de prioridad.
- Establecer fechas límite.
- Validar información desde el backend.
- Mostrar estados de carga, error y listas vacías.

## Tecnologías utilizadas

| Área | Tecnología |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| ORM | Eloquent |
| Validaciones | Laravel Form Requests |
| Frontend | Vue 3 |
| Framework UI | Quasar 2 |
| Herramientas frontend | Vite, Axios |
| Base de datos | SQLite / MySQL |
| Pruebas | PHPUnit |

## Arquitectura

El proyecto está dividido en dos partes principales:

```text
TaskFlow/
│
├── backend/
│   └── API REST desarrollada con Laravel
│
├── frontend/
│   └── SPA desarrollada con Vue 3 y Quasar
│
├── docs/
│   └── Documentación adicional
│
└── README.md
```

**Backend:** administra las solicitudes, validaciones, operaciones con la base de datos y respuestas de la API.

**Frontend:** presenta la interfaz de usuario y consume los servicios del backend mediante Axios.

## Instalación y ejecución

### Requisitos

- PHP 8.2 o superior.
- Composer 2.
- Node.js 20 o superior.
- npm.
- Extensiones PHP necesarias para Laravel y el controlador de base de datos elegido.

### 1. Clonar el repositorio

```bash
git clone https://github.com/SaredGarcia/TaskFlow.git
cd TaskFlow
```

### 2. Configurar el backend

Entrar en la carpeta:

```bash
cd backend
```

Instalar dependencias:

```bash
composer install
```

Crear el archivo de entorno:

```bash
cp .env.example .env
```

En Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Generar la clave de aplicación:

```bash
php artisan key:generate
```

### 3. Configurar la base de datos

SQLite es la configuración predeterminada.

Crear el archivo de base de datos:

```bash
touch database/database.sqlite
```

En Windows PowerShell:

```powershell
New-Item database/database.sqlite -ItemType File
```

Ejecutar migraciones:

```bash
php artisan migrate
```

Si se desea utilizar MySQL, configurar las variables de conexión correspondientes en el archivo `.env` y crear previamente la base de datos.

### 4. Iniciar el backend

```bash
php artisan serve
```

La API estará disponible en:

```text
http://127.0.0.1:8000/api
```

### 5. Configurar el frontend

Abrir otra terminal y entrar en:

```bash
cd frontend
```

Instalar dependencias:

```bash
npm install
```

Crear el archivo de entorno:

```bash
cp .env.example .env
```

En Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Iniciar la aplicación:

```bash
npm run dev
```

Abrir la dirección local indicada por Quasar, normalmente:

```text
http://localhost:9000
```

El frontend utiliza la variable `VITE_API_URL` para configurar la dirección de la API.

## API REST

| Método | Endpoint | Descripción |
|---|---|---|
| GET | `/api/activities` | Consultar actividades |
| POST | `/api/activities` | Crear actividad |
| GET | `/api/activities/{id}` | Consultar una actividad |
| PUT / PATCH | `/api/activities/{id}` | Actualizar actividad |
| DELETE | `/api/activities/{id}` | Eliminar actividad |

### Parámetros de búsqueda

El listado permite utilizar:

- `search`: buscar coincidencias en el título.
- `status`: filtrar por estado.

Estados disponibles:

- `pending`
- `in_progress`
- `completed`

Prioridades disponibles:

- `low`
- `medium`
- `high`

Ejemplo:

```bash
curl "http://127.0.0.1:8000/api/activities?search=reporte&status=pending"
```

## Pruebas

Para ejecutar las pruebas automatizadas del backend:

```bash
cd backend
php artisan test
```

## Compilación para producción

Desde la carpeta `frontend`:

```bash
npm run build
```

Los archivos compilados se generan en:

```text
frontend/dist/spa
```

Para publicar la aplicación es necesario configurar el servidor web y la dirección pública de la API.

## Alcance del proyecto

TaskFlow se desarrolló con un enfoque en la administración de actividades y la comunicación entre frontend y backend.

El alcance actual no contempla autenticación ni administración de usuarios.

## Autor

**Sared García**

Desarrollador Web Junior.

- GitHub: [@SaredGarcia](https://github.com/SaredGarcia)

---

<p align="center">
  <strong>SARED × GAPE</strong><br>
  Código, ideas y proyectos.
</p>
