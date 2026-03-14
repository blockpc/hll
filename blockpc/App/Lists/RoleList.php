<?php

declare(strict_types=1);

namespace Blockpc\App\Lists;

final class RoleList
{
    /**
     * Devuelve todos los roles utilizados por el sistema.
     * Cada arreglo contiene:
     * - name: Nombre del role
     * - display_name: Nombre para mostrar del role
     * - description: Descripción del role
     * - is_editable: Indica si el role es editable o no
     * - guard_name: Nombre del guard (opcional, por defecto 'web')
     * - permissions: Lista de permisos asociados al role
     * [name, display_name, description, is_editable, guard_name (opcional: 'web'), permissions]
     *
     * @return array<int, array{name: string, display_name: string, description: string, is_editable: bool, guard_name: string, permissions: array<int, string>}>
     */
    public static function all(): array
    {
        return [
            ...self::system(),
            ...self::hllRoles(),
        ];
    }

    /**
     * Devuelve los roles por defecto del sistema.
     *
     * @return array<int, array{name: string, display_name: string, description: string, is_editable: bool, guard_name: string, permissions: array<int, string>}>
     */
    private static function system(): array
    {
        return [
            [
                'name' => 'sudo',
                'display_name' => 'Super Administrador',
                'description' => 'Usuario del sistema con acceso total',
                'is_editable' => false,
                'guard_name' => 'web',
                'permissions' => [],
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrador',
                'description' => 'Usuario del sistema con acceso general',
                'is_editable' => true,
                'guard_name' => 'web',
                'permissions' => ['*'],
            ],
            [
                'name' => 'user',
                'display_name' => 'Usuario',
                'description' => 'Usuario por defecto del sistema',
                'is_editable' => true,
                'guard_name' => 'web',
                'permissions' => [
                    'users.index',
                    'roles.index',
                    'permissions.index',
                ],
            ],
        ];
    }

    /**
     * Devuelve los roles para la aplicacion HLL.
     *
     * @return array<int, array{name: string, display_name: string, description: string, is_editable: bool, guard_name: string, permissions: array<int, string>}>
     */
    private static function hllRoles(): array
    {
        return [
            [
                'name' => 'clan_owner',
                'display_name' => 'Administrador de Clan',
                'description' => 'Usuario del sistema con acceso a la gestión de clanes',
                'is_editable' => false,
                'guard_name' => 'web',
                'permissions' => [
                    'clans.index',
                    'clans.show',
                    'clans.create',
                    'clans.edit',
                ],
            ],
            [
                'name' => 'clan_helper',
                'display_name' => 'Ayudante de Clan',
                'description' => 'Usuario del sistema con acceso limitado a la gestión de clanes',
                'is_editable' => false,
                'guard_name' => 'web',
                'permissions' => [
                    'clans.index',
                    'clans.show',
                    'clans.edit',
                ],
            ],
        ];
    }
}
