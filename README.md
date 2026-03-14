# Roster para HLL

Esta aplicación permite generar plantillas de jugadores para los clanes de Hell Let Loose.

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

- El propietario no puede ser el mismo usuario que ejecuta la acción (cuando aplica selector).
- El propietario seleccionado no puede tener ya un clan creado.
- Si el propietario seleccionado no tiene rol `clan_owner`, se le asigna automáticamente durante la creación del clan.

### Comportamiento para `sudo` y `super admin`

- La visibilidad del selector y la autorización de creación se evalúan con `can('clans.create')`.
- Esto permite que `sudo` y usuarios con permiso `super admin` entren en el flujo de creación, aunque no tengan asignado explícitamente el permiso `clans.create`.

### Notas de consistencia

- El nombre técnico del rol debe ser `clan_owner`.
- Si en base de datos existe un nombre distinto para el mismo rol de negocio (por ejemplo, un nombre legado), pueden aparecer resultados inconsistentes en `hasRole()`.
