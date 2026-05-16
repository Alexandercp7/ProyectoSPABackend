# ProyectoSPA — Backend (Laravel)

API REST desarrollada en **Laravel** que sirve de backend para el sistema de gestión de taller automotriz ProyectoSPA. Autenticación con Sanctum, control de roles con Spatie Permission y base de datos MySQL.

---

## Tecnologías

| | |
|---|---|
| Framework | Laravel 11 |
| Autenticación | Laravel Sanctum |
| Roles y permisos | Spatie Laravel Permission |
| Base de datos | MySQL |
| PHP | 8.2+ |

---

## Instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/Alexandercp7/ProyectoSPABackend.git
cd ProyectoSPABackend

# 2. Instalar dependencias
composer install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos en .env
DB_DATABASE=proyectospa
DB_USERNAME=root
DB_PASSWORD=

# 5. Migraciones y seeders
php artisan migrate --seed

# 6. Levantar servidor
php artisan serve
# → http://localhost:8000
```

---

## Roles de usuario

| Rol | Acceso |
|---|---|
| `admin` | Acceso completo a todos los endpoints |
| `gerente` | Finanzas, KPIs, reportes, agenda de pagos |
| `tecnico` | Órdenes de trabajo, inventario |
| `recepcionista` | Clientes, vehículos, creación de órdenes |

---

## Endpoints principales (`/api/v1`)

### Auth (público)
| Método | Ruta | Descripción |
|---|---|---|
| POST | `/auth/login` | Iniciar sesión |
| POST | `/auth/logout` | Cerrar sesión |
| GET | `/auth/me` | Usuario autenticado |
| PUT | `/auth/password` | Cambiar contraseña |

### Órdenes de Trabajo
| Método | Ruta | Roles |
|---|---|---|
| GET | `/work-orders` | admin, técnico, recepcionista, gerente |
| GET | `/work-orders/{id}` | admin, técnico, recepcionista, gerente |
| POST | `/work-orders` | admin, recepcionista |
| PATCH | `/work-orders/{id}/status` | admin, técnico |
| PATCH | `/work-orders/{id}/close` | admin, técnico |
| POST | `/work-orders/{id}/parts` | admin, técnico |
| POST | `/work-orders/{id}/services` | admin, técnico |
| POST | `/work-orders/{id}/photos` | admin, técnico |
| POST | `/work-orders/{id}/notes` | admin, técnico |
| DELETE | `/work-orders/{id}` | admin |

### Clientes y Vehículos
| Método | Ruta | Roles |
|---|---|---|
| GET/POST | `/clients` | Todos / admin, recepcionista |
| GET/PUT/DELETE | `/clients/{id}` | admin, recepcionista |
| GET/POST | `/vehicles` | Todos / admin, recepcionista |
| GET | `/vehicles/proxy` | Todos (proxy API externa) |

### Inventario
| Método | Ruta | Roles |
|---|---|---|
| GET/POST | `/inventory` | admin, técnico |
| PUT/DELETE | `/inventory/{id}` | admin, técnico |
| GET | `/inventory/movements` | admin, técnico |

### Finanzas
| Método | Ruta | Roles |
|---|---|---|
| GET/POST | `/finance/daily-cash` | admin, gerente |
| GET/POST | `/finance/accounts-receivable` | admin, gerente |
| GET/POST | `/finance/accounts-payable` | admin, gerente |

### Precios
| Método | Ruta | Roles |
|---|---|---|
| GET/POST | `/prices` | admin |
| PUT/DELETE | `/prices/{id}` | admin |

### Otros módulos
| Ruta | Descripción |
|---|---|
| `/employees` | Gestión de empleados |
| `/contacts` | Directorio de proveedores/contactos |
| `/activities` | Registro de actividades |
| `/kpis` | Indicadores de rendimiento |
| `/payments-agenda` | Agenda de pagos |
| `/portal/{token}` | Portal de cliente (público) |
| `/surveys/{token}` | Encuestas de satisfacción (público) |

---

## Estructura del proyecto

```
app/
├── Http/
│   └── Controllers/
│       └── Api/
│           ├── Auth/
│           ├── WorkOrderController.php
│           ├── ClientController.php
│           ├── VehicleController.php
│           ├── InventoryController.php
│           ├── FinanceController.php
│           ├── PriceController.php
│           ├── KpiController.php
│           └── ...
├── Models/
│   ├── WorkOrder.php
│   ├── Client.php
│   ├── Vehicle.php
│   ├── InventoryItem.php
│   └── ...
└── Services/
database/
└── migrations/
routes/
└── api.php
```

---

## Conexión con el frontend

El frontend Angular usa un proxy (`proxy.conf.json`) que redirige `/api/*` a `http://localhost:8000/api`. Asegúrate de que ambos servidores estén corriendo simultáneamente.
