# Proyecto Laravel con Vue.js - Firma Digital Cáceres

Este es un proyecto Laravel con Vue.js que incluye autenticación, registro y un panel de administración funcional.

## Características

- ✅ Autenticación completa (Login y Registro)
- ✅ Panel de administración funcional
- ✅ Gestión de usuarios (CRUD)
- ✅ Diseño con paleta de colores personalizada:
  - Primario: #C41230
  - Secundario: #425968
  - Acento: #949CA1
- ✅ Tipografía Arial

## Requisitos

- PHP >= 8.1
- Composer
- Node.js y npm
- Base de datos (MySQL, PostgreSQL, SQLite, etc.)

## Instalación

1. **Clonar o navegar al proyecto:**
   ```bash
   cd firma_digital_caceres
   ```

2. **Instalar dependencias de PHP:**
   ```bash
   composer install
   ```

3. **Instalar dependencias de Node:**
   ```bash
   npm install
   ```

4. **Configurar el archivo .env:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configurar la base de datos en .env:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nombre_de_tu_base_de_datos
   DB_USERNAME=tu_usuario
   DB_PASSWORD=tu_contraseña
   ```

6. **Ejecutar las migraciones:**
   ```bash
   php artisan migrate
   ```

7. **Compilar los assets:**
   ```bash
   npm run build
   ```

   O para desarrollo con recarga automática:
   ```bash
   npm run dev
   ```

8. **Iniciar el servidor:**
   ```bash
   php artisan serve
   ```

## Uso

### Acceso a la aplicación

- **URL:** http://localhost:8000
- **Login:** http://localhost:8000/login
- **Registro:** http://localhost:8000/register
- **Dashboard:** http://localhost:8000/dashboard (requiere autenticación)
- **Administración:** http://localhost:8000/admin (requiere autenticación)

### Funcionalidades del Panel de Administración

El panel de administración permite:
- Ver lista de usuarios
- Crear nuevos usuarios
- Editar usuarios existentes
- Eliminar usuarios
- Paginación de resultados

## Estructura del Proyecto

```
firma_digital_caceres/
├── app/
│   └── Http/
│       └── Controllers/
│           └── AdminController.php
├── resources/
│   ├── js/
│   │   ├── Pages/
│   │   │   ├── Auth/
│   │   │   │   ├── Login.vue
│   │   │   │   └── Register.vue
│   │   │   ├── Admin/
│   │   │   │   ├── Index.vue
│   │   │   │   ├── Create.vue
│   │   │   │   └── Edit.vue
│   │   │   └── Dashboard.vue
│   │   └── Layouts/
│   │       ├── AuthenticatedLayout.vue
│   │       └── GuestLayout.vue
│   └── css/
│       └── app.css
├── routes/
│   └── web.php
└── tailwind.config.js
```

## Personalización de Colores

Los colores están definidos en `tailwind.config.js`:

- `primary`: #C41230 (Rojo principal)
- `secondary`: #425968 (Azul grisáceo)
- `accent`: #949CA1 (Gris claro)

Puedes usar estos colores en tus componentes Vue con las clases de Tailwind:
- `bg-primary`, `text-primary`, `border-primary`
- `bg-secondary`, `text-secondary`, `border-secondary`
- `bg-accent`, `text-accent`, `border-accent`

## Tecnologías Utilizadas

- **Backend:** Laravel 10
- **Frontend:** Vue.js 3 con Inertia.js
- **Estilos:** Tailwind CSS
- **Autenticación:** Laravel Breeze

## Comandos Útiles

```bash
# Desarrollo con recarga automática
npm run dev

# Compilar para producción
npm run build

# Ejecutar migraciones
php artisan migrate

# Crear un nuevo usuario administrador (tinker)
php artisan tinker
>>> User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password')])
```

## Notas

- Asegúrate de tener configurada correctamente tu base de datos antes de ejecutar las migraciones.
- El proyecto utiliza Laravel Breeze con Vue.js para la autenticación.
- Todos los textos están en español.

## Licencia

Este proyecto es de código abierto.
