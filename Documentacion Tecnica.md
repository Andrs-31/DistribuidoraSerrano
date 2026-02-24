# Documentación Técnica

**Proyecto:** Distribuidora Serrano

**Resumen:** Esta documentación técnica describe la instalación, requisitos, arquitectura, uso y diseño de la aplicación web "Distribuidora Serrano" desarrollada con Laravel.

## **Información General Del Sistema**

- **Nombre del sistema:** Distribuidora Serrano
- **Propósito:** Gestionar clientes, productos y facturación para una distribuidora local. Incluye panel administrativo, generación de facturas, reportes y control de usuarios.
- **Tecnologías principales:** PHP (>= 8.2), Laravel Framework ^12.0, Composer, Node.js (npm/Vite), MySQL / MariaDB (o PostgreSQL como alternativa), Redis (opcional para cache/colas), Docker (opcional para despliegue).
- **Ubicación del código:** repositorio raíz del proyecto.
- **Modelos principales:** `Cliente`, `Producto`, `Factura`, `FacturaDetalle`, `User`, `Permiso`, `ListaPrecio`, `GrupoCliente`, `Provincia`, `Distrito`.

## **Requisitos del Sistema**

- Requisitos de software (desarrollo/producción):
  - PHP ^8.2 con extensiones recomendadas: `pdo`, `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `zip`.
  - Composer (para dependencias PHP).
  - Node.js (>=16) y npm (para assets con Vite).
  - Servidor web: Nginx o Apache (con PHP-FPM recomendado).
  - Base de datos: MySQL 8 / MariaDB 10.4+ (o PostgreSQL si se adapta).
  - Opcional: Redis para cache/colas, Supervisor para procesos en background.

- Requisitos hardware mínimos (producción pequeña):
  - CPU: 2 vCPU
  - Memoria: 2 GB RAM (4 GB recomendado)
  - Almacenamiento: 20 GB (según tamaño de archivos y backups)

## **Instalación del Sistema**

1. Clonar el repositorio y posicionarse en la carpeta raíz:

```bash
git clone <repo-url> distribuidora-serrano
cd distribuidora-serrano
```

2. Instalar dependencias PHP con Composer:

```bash
composer install --no-interaction --prefer-dist --optimize-autoloader
```

3. Copiar y configurar el archivo de entorno `.env` (ajustar DB, MAIL, APP_URL, etc.):

```bash
cp .env.example .env
php artisan key:generate
# Editar .env con credenciales de BD y otros valores
```

4. Instalar dependencias de Node y compilar assets:

```bash
npm install
# Para desarrollo
npm run dev
# Para producción
npm run build
```

5. Ejecutar migraciones y seeders (producción: usar `--force`):

```bash
php artisan migrate --seed
# En producción
php artisan migrate --force --seed
```

6. Crear enlace de storage (si se usan uploads públicos):

```bash
php artisan storage:link
```

7. Permisos (Linux): asegurar permisos para `storage` y `bootstrap/cache`:

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

8. Iniciar servidor (desarrollo):

```bash
php artisan serve
```

9. Despliegue de producción (resumen):
  - Configurar Nginx con PHP-FPM.
  - Ejecutar `composer install --no-dev` y `npm run build`.
  - Ejecutar `php artisan migrate --force` y `php artisan config:cache`.
  - Configurar Supervisor para `queue:work` si se usa cola.

## **Uso del Sistema (Técnico)**

- Estructura principal del código:
  - `app/Http/Controllers/` — Controladores HTTP por módulo.
  - `app/Models/` — Modelos Eloquent (entidades del dominio).
  - `routes/web.php` — Rutas web.
  - `resources/views/` — Plantillas Blade.
  - `database/migrations/` — Migraciones de esquema.
  - `database/seeders/` — Seeders para datos iniciales.

- Comandos útiles:

```bash
php artisan migrate
php artisan db:seed
php artisan test          # Ejecuta tests automatizados
php artisan tinker        # Console REPL de Laravel
php artisan queue:work    # Procesador de colas
php artisan cache:clear
php artisan config:cache
php artisan route:list
```

- Variables de entorno relevantes (ejemplos):
  - `APP_ENV`, `APP_DEBUG`, `APP_URL`
  - `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
  - `MAIL_*` para envío de correos
  - `QUEUE_CONNECTION` (database, redis, sync)

- Logs y monitoreo:
  - Laravel escribe logs en `storage/logs/laravel.log`.
  - Usar `tail -f storage/logs/laravel.log` para observar errores.

## **Arquitectura del Sistema**

- Patrón: MVC (Model-View-Controller) con servicios auxiliares y middleware.
- Flujo de una petición:
  1. El cliente hace una petición HTTP a una ruta definida en `routes/web.php`.
  2. La petición pasa por middleware global y por ruta (autenticación, CSRF, etc.).
  3. El controlador correspondiente procesa la lógica, consulta modelos (`Eloquent`) y/o servicios.
  4. Se retorna una vista Blade o JSON (API) al cliente.

- Componentes externos integrados:
  - Paquetes composer: `barryvdh/laravel-dompdf` (PDF), `anhskohbo/no-captcha` (reCAPTCHA), entre otros.

## **Descripción de Módulos**

- **Módulo Clientes**
  - Rutas: gestión CRUD de clientes, búsqueda, importación/exportación.
  - Modelos: `Cliente`, `GrupoCliente`, `Provincia`, `Distrito`.
  - Controladores: crear/editar/listar clientes, validaciones y reglas de negocio.

- **Módulo Productos**
  - Rutas: CRUD de productos, listas de precios.
  - Modelos: `Producto`, `ListaPrecio`.

- **Módulo Facturación**
  - Rutas: creación de facturas, detalle de factura, impresión (PDF), reportes.
  - Modelos: `Factura`, `FacturaDetalle`.
  - Integraciones: generación de PDF con `barryvdh/laravel-dompdf`.

- **Módulo Usuarios y Permisos**
  - Rutas: login, registro administrativo, asignación de permisos.
  - Modelos: `User`, `Permiso`.

- **Módulo Reportes**
  - Rutas: reportes de ventas, productos más vendidos, clientes por volumén.

## **Base de Datos**

- Motor: MySQL / MariaDB (ejemplo de configuración en `.env`).
- Tablas clave y campos (resumen):
  - `clientes` (id, nombre, documento, direccion, telefono, email, grupo_cliente_id, provincia_id, distrito_id, timestamps)
  - `productos` (id, sku, nombre, descripcion, precio_unitario, stock, timestamps)
  - `facturas` (id, cliente_id, usuario_id, fecha, total, estado, timestamps)
  - `factura_detalles` (id, factura_id, producto_id, cantidad, precio_unitario, subtotal)
  - `users`, `permissions`, `password_resets`, `migrations`, etc.

- Relaciones principales:
  - `Factura` 1:N `FacturaDetalle`
  - `Cliente` 1:N `Factura`
  - `Producto` 1:N `FacturaDetalle`

- Índices y claves foráneas: definir en migraciones para `cliente_id`, `producto_id`, `factura_id` y demás.

- Backups (ejemplo MySQL):

```bash
mysqldump -u dbuser -p dbname > backups/db_backup_$(date +%F).sql
# Restaurar
mysql -u dbuser -p dbname < backups/db_backup.sql
```

## **Posibles Mejoras Futuras**

- Implementar API RESTful o GraphQL para integraciones (POS, aplicaciones móviles).
- Añadir Docker + docker-compose para desarrollo y despliegue reproducible.
- Integrar CI/CD (GitHub Actions / GitLab CI) para tests automáticos y despliegue.
- Mejorar cobertura de tests (unitarios y feature) y adicionar pruebas E2E.
- Usar Redis para cache y colas para mejorar rendimiento bajo carga.
- Implementar paginación y criterios de búsqueda avanzados (ElasticSearch o Algolia).
- Internacionalización (i18n) si se requiere multi-idioma.
- Implementar mecanismos de monitoreo y alertas (Prometheus, Grafana, Sentry para errores).
- Harden security: CSP, rate-limiting, WAF, revisión de dependencias y actualizaciones periódicas.

## **Anexos — Comandos Rápidos**

- Instalar dependencias y preparar entorno:

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
php artisan migrate --seed
```

- Ejecutar tests:

```bash
php artisan test
```

- Limpieza de caches:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## **Información de Versiones (extraída de composer.json)**

- PHP: ^8.2
- Laravel Framework: ^12.0
- Paquetes relevantes: `anhskohbo/no-captcha`, `barryvdh/laravel-dompdf`, `laravel/tinker`.

---

Si desea, puedo:
- Generar un diagrama ER más detallado (image/SVG) a partir de las migraciones.
- Crear un `README` corto para despliegue en servidor.
- Añadir sección de integración continua con ejemplo de pipeline.

Archivo generado: [Documentacion Tecnica.md](Documentacion%20Tecnica.md)
