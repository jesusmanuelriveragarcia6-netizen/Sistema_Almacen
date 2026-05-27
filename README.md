# Sistema Almacén Inteligente — Centro Logístico CORTEX

Sistema de gestión de almacén de herramientas con control de préstamos, incidencias, mantenimientos y monitoreo inteligente (Cortex AI).

## Requisitos previos

- **XAMPP** (o similar) con PHP 8.2+ y MySQL/MariaDB
- **Composer** ([getcomposer.org](https://getcomposer.org))
- **Node.js** 18+ y **npm** ([nodejs.org](https://nodejs.org))

## Instalación rápida

### 1. Clonar el repositorio

```bash
cd c:\xampp\htdocs
git clone https://github.com/jesusmanuelriveragarcia6-netizen/Sistema_Almacen.git laravel_app
cd laravel_app
```

### 2. Instalar dependencias

```bash
composer install
npm install
```

### 3. Configurar el entorno

```bash
copy .env.example .env
php artisan key:generate
```

### 4. Crear la base de datos

Abre **phpMyAdmin** (`http://localhost/phpmyadmin`) y crea una base de datos llamada:

```
almacen_inteligente
```

> Si usas otro nombre o tienes contraseña de MySQL, edita el archivo `.env` con tus datos.

### 5. Ejecutar migraciones y seeder

```bash
php artisan migrate
php artisan db:seed
```

### 6. Compilar assets (CSS/JS)

```bash
npm run build
```

### 7. Iniciar el servidor

Si usas XAMPP, simplemente inicia Apache y MySQL, luego accede a:

```
http://localhost/laravel_app/public
```

O usa el servidor de desarrollo de PHP:

```bash
php artisan serve
```

Y accede a `http://localhost:8000`

## Credenciales por defecto

| Usuario | Contraseña | Rol |
|---------|-----------|-----|
| `admin` | `Adm!n#2026$SecureX9` | Administrador |

> ⚠️ **Cambia la contraseña del administrador** después del primer inicio de sesión.

## Solución de problemas comunes

### Error "SQLSTATE[HY000] Table not found" o "Base table not found"
Asegúrate de haber ejecutado `php artisan migrate` correctamente.

### Error 500 al acceder
1. Verifica que el archivo `.env` existe (cópialo de `.env.example`)
2. Ejecuta `php artisan key:generate`
3. Ejecuta `php artisan config:clear`

### No me deja iniciar sesión
1. Verifica que ejecutaste `php artisan db:seed` para crear el usuario admin
2. Las credenciales son: usuario `admin`, contraseña `Adm!n#2026$SecureX9`
3. Si cambiaste la `APP_KEY` después de crear usuarios, las contraseñas anteriores no funcionarán

### Error de permisos en storage/
```bash
# En Linux/Mac:
chmod -R 775 storage bootstrap/cache
```

## Estructura del proyecto

```
├── app/Models/          → Modelos (Herramienta, Vale, Trabajador, etc.)
├── app/Http/Controllers → Controladores
├── database/migrations  → Migraciones de BD
├── database/seeders     → Datos iniciales (usuario admin)
├── resources/views      → Vistas Blade
├── public/css           → Estilos CSS
├── routes/web.php       → Rutas de la aplicación
└── routes/auth.php      → Rutas de autenticación
```
