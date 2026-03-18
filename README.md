# Roster para HLL

Aplicación Laravel para gestionar clanes de Hell Let Loose, usuarios, roles, permisos, notas internas y notificaciones dentro de un panel administrativo construido con Livewire.

## Stack principal

- Laravel 12
- PHP 8.5
- Livewire 4
- Flux UI Free
- Laravel Fortify
- Laravel Reverb
- Tailwind CSS 4
- MariaDB
- Pest
- Laravel Sail
- Spatie Laravel Permission

## Qué resuelve el proyecto hoy

- Gestión de autenticación, registro, recuperación de contraseña, verificación de email y doble factor.
- Panel autenticado bajo `sistema/*`.
- Administración de usuarios, roles y permisos.
- Gestión de clanes con reglas de negocio específicas.
- Notas personales por usuario.
- Notificaciones de usuario con soporte para tiempo real vía Reverb/Echo.
- Comandos internos para sincronizar roles y permisos y para inspección de SQL lento en entorno local.

## Arranque local

### Requisitos

- Docker y Docker Compose.
- Node.js y npm solo si vas a ejecutar comandos fuera del contenedor. En local se recomienda usar Sail para todo.

### Primer inicio

```bash
cp .env.example .env
composer install
npm install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm run dev
```

Si prefieres compilar assets una sola vez:

```bash
./vendor/bin/sail npm run build
```

### Servicios expuestos por Docker

- Aplicación: puerto definido por `APP_PORT`, por defecto `80`.
- Vite: puerto definido por `VITE_PORT`, por defecto `5173`.
- MariaDB: puerto definido por `FORWARD_DB_PORT`, por defecto `3306`.
- Mailpit SMTP: puerto `1025`.
- Mailpit UI: puerto `8025`.
- phpMyAdmin: puerto `8080`.
- Reverb: puerto externo definido por `REVERB_FORWARD_PORT`, por defecto `8081`.

## Datos de prueba iniciales

El seeder principal crea:

- Usuario `sudo@mail.com` con rol `sudo`.
- Usuario `test@mail.com` con rol `clan_owner`.
- Un clan de prueba asociado al usuario `test@mail.com`.

En ambos casos la contraseña inicial es `password` y el email queda verificado para entorno local.

## Comandos útiles

### Aplicación

```bash
./vendor/bin/sail up -d
./vendor/bin/sail down
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan migrate:fresh --seed
./vendor/bin/sail artisan test --compact
./vendor/bin/sail php vendor/bin/pint --dirty
```

### Sincronización de roles y permisos

```bash
./vendor/bin/sail artisan blockpc:permissions
./vendor/bin/sail artisan blockpc:permissions --check
./vendor/bin/sail artisan blockpc:permissions --orphans
./vendor/bin/sail artisan blockpc:permissions --prune --ci

./vendor/bin/sail artisan blockpc:roles
./vendor/bin/sail artisan blockpc:roles --check
./vendor/bin/sail artisan blockpc:roles --orphans
./vendor/bin/sail artisan blockpc:roles --prune --ci
```

### Inspección de SQL lento en local

```bash
./vendor/bin/sail artisan sql:watch sistema/notas/*
./vendor/bin/sail artisan sql:watch --off
```

## Estructura del proyecto

```text
app/
	Actions/Fortify/           Acciones de registro y reseteo de contraseña
	Enums/                     Enumeraciones de dominio
	Http/Requests/             Validaciones de formularios HTTP
	Livewire/                  Componentes Livewire con clase PHP
		Actions/                 Acciones de sesión
		Notes/                   CRUD de notas
		Notifications/           Panel y tabla de notificaciones
	Mail/                      Correos de aplicación
	Models/                    Modelos de dominio: User, Clan, Role, Permission, Note
	Notifications/             Notificaciones Laravel
	Policies/                  Políticas de autorización
	Providers/                 Service providers de aplicación y Fortify

blockpc/
	App/Commands/              Comandos artisan propios
	App/Lists/                 Catálogos fuente de roles y permisos
	App/Mixins/                Extensiones reutilizables del framework
	App/Providers/             Providers propios, incluyendo autorización global
	App/Rules/                 Reglas de validación personalizadas
	App/Services/              Servicios de sincronización y lógica transversal
	Traits/                    Traits compartidos entre módulos
	helpers.php                Helpers globales autoloaded por Composer

bootstrap/
	app.php                    Configuración principal de Laravel 12
	providers.php              Registro de providers de la aplicación

config/
	fortify.php                Configuración de autenticación
	livewire.php               Namespaces y layout de componentes view-based
	permission.php             Configuración de Spatie Permission
	reverb.php                 Tiempo real y WebSockets

database/
	factories/                 Factories para tests y seeders
	migrations/                Migraciones de usuarios, permisos, notas, clanes y notificaciones
	seeders/                   Seeders base y sincronización de roles/permisos

resources/
	css/                       Estilos
	js/                        Bootstrap frontend y Echo
	views/
		layouts/                 Layouts Livewire/Blade
		pages/                   Páginas de auth y settings
		system/                  Páginas del panel administrativo
		livewire/                Vistas de componentes Livewire con clase

routes/
	web.php                    Rutas principales y panel sistema
	settings.php               Ajustes de cuenta
	channels.php               Canales de broadcasting
	console.php                Comandos closure simples

tests/
	Feature/                   Tests funcionales de auth, settings, sistema y HLL
	Unit/                      Tests unitarios aislados
```

## Convenciones relevantes de la UI

- El proyecto mezcla componentes Livewire con clase en `app/Livewire` y componentes view-based resueltos desde `resources/views`.
- Los namespaces de componentes configurados en Livewire son:
	- `layouts::`
	- `pages::`
	- `system::`
- El layout por defecto para páginas Livewire es `layouts::app`.
- La configuración actual de Livewire usa componentes tipo SFC y permite el prefijo visual `⚡` en los archivos Blade generados.

## Rutas funcionales principales

### Públicas

- `/`: landing inicial.
- `/email/verify/invitation/{id}/{hash}`: verificación de invitaciones por email.

### Ajustes de cuenta

- `/settings/profile`
- `/settings/password`
- `/settings/appearance`
- `/settings/two-factor`

### Panel autenticado

Prefijo común: `sistema/`

- `dashboard`
- `lista-de-notas`
- `mis-notificaciones`
- `permisos/lista-de-permisos`
- `roles/lista-de-roles`
- `roles/nuevo-rol`
- `roles/editar-rol/{role}`
- `usuarios/lista-de-usuarios`
- `usuarios/nuevo-usuario`
- `usuarios/editar-usuario/{user}`
- `clanes/`
- `clanes/nuevo-clan`
- `clanes/editar-clan/{clan}`
- `clanes/ver-clan/{clan}`

## Modelo de autorización

- La aplicación usa Spatie Laravel Permission con modelos propios `Role` y `Permission`.
- Existe un `Gate::before()` global que otorga acceso total a usuarios con rol `sudo` o permiso `super admin`.
- Los roles y permisos del sistema no se mantienen manualmente en base de datos como única fuente de verdad; se sincronizan desde listas internas definidas en `blockpc/App/Lists`.

## Gestión de Roles y Creación de Clanes

### Roles relevantes

- `clan_owner`: administrador de un clan.
- `clan_helper`: asistente de clan con permisos limitados.
- `sudo` y permiso `super admin`: acceso global por autorización central.

### Reglas de creación de clanes

- Un usuario con rol `clan_owner` puede crear clan solo si todavía no tiene uno creado.
- Un usuario que no es `clan_owner`, pero tiene permiso `clans.create`, también puede crear clanes.
- En ese caso debe seleccionar un propietario usando el selector de usuarios del formulario.

### Asignación de propietario al crear un clan

- El propietario no puede ser el mismo usuario que ejecuta la acción cuando aplica selector.
- El propietario seleccionado no puede tener ya un clan creado.
- Si el propietario seleccionado no tiene rol `clan_owner`, se le asigna automáticamente durante la creación del clan.

### Comportamiento para `sudo` y `super admin`

- La visibilidad del selector y la autorización de creación se evalúan con `can('clans.create')`.
- Esto permite que `sudo` y usuarios con permiso `super admin` entren en el flujo de creación, aunque no tengan asignado explícitamente el permiso `clans.create`.

### Notas de consistencia

- El nombre técnico del rol debe ser `clan_owner`.
- Si en base de datos existe un nombre distinto para el mismo rol de negocio, por ejemplo un nombre legado, pueden aparecer resultados inconsistentes en `hasRole()`.

## Cobertura actual de tests

El proyecto ya tiene tests Pest para:

- autenticación y settings;
- sincronización de roles y permisos;
- CRUD y tablas de usuarios, roles y permisos;
- notas;
- políticas y flujos de clanes.

Para ejecutar solo los tests del dominio HLL:

```bash
./vendor/bin/sail artisan test --compact tests/Feature/HLL
```

## Últimos cambios (2026-03-18)

### Roster: seguridad y consistencia por clan

- El binding de rutas para `Roster` ahora se resuelve de forma acotada por clan y slug.
- Se mantiene `getRouteKeyName()` en `slug`, pero la resolución del modelo en ruta exige coincidencia con el clan actual.
- Si la ruta no aporta un clan válido, el binding no devuelve resultados para evitar resoluciones fuera de contexto.

### Roster edit: robustez en guardado y validación

- Se reforzó la autorización para evitar editar un roster que no pertenece al clan de la URL (respuesta 404).
- Se mejoró la gestión de imágenes al actualizar:
	- limpieza de imagen anterior en ruta de éxito;
	- limpieza defensiva de imagen nueva si ocurre una excepción.
- Se ajustaron reglas de validación en edición:
	- `name` único por clan (ignorando el propio roster en la actualización);
	- `faction` obligatoria;
	- `description` limitada a 255 caracteres.

### Roster table: rendimiento y tolerancia a nulos

- Se agregó eager loading de `map` y `centralPoint` para reducir consultas N+1.
- La tabla ahora renderiza valores nulos con fallback (`--`) en mapa, punto central y facción.

### Notas: corrección de modal

- Se corrigió un atributo mal formado en el modal de borrado para que `:closable="false"` se aplique correctamente.

### Tests incorporados/actualizados

- Se agregó un test de regresión para confirmar que el route model binding de roster usa clan + slug.

Para ejecutar solo los tests de edición de rosters:

```bash
./vendor/bin/sail artisan test --compact tests/Feature/System/Rosters/RosterEditTest.php
```
