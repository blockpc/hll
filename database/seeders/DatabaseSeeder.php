<?php

namespace Database\Seeders;

use App\Enums\RoleSquadTypeEnum;
use App\Models\Clan;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(MapSeeder::class);

        $sudo = User::firstOrCreate(
            ['email' => 'sudo@mail.com'],
            [
                'name' => 'Super Administrador',
                'password' => 'password',
            ]
        );
        if ($sudo->wasRecentlyCreated) {
            $sudo->markEmailAsVerified();
        }
        $sudo->syncRoles(['sudo']);

        if (app()->environment('local')) {
            $ownerUser = User::firstOrCreate(
                ['email' => 'owner@mail.com'],
                [
                    'name' => 'Owner Clan',
                    'password' => 'password',
                ]
            );
            if ($ownerUser->wasRecentlyCreated) {
                $ownerUser->markEmailAsVerified();
            }
            $ownerUser->syncRoles(['clan_owner']);

            $helperUser = User::firstOrCreate(
                ['email' => 'helper@mail.com'],
                [
                    'name' => 'Helper Clan',
                    'password' => 'password',
                ]
            );
            if ($helperUser->wasRecentlyCreated) {
                $helperUser->markEmailAsVerified();
            }
            $helperUser->syncRoles(['clan_helper']);

            if (! Clan::query()->where('slug', 'miopes-y-mancos')->exists()) {
                $clan = Clan::factory()->withOwner($ownerUser)->withHelper($helperUser)->create([
                    'alias' => 'MYM',
                    'slug' => 'miopes-y-mancos',
                    'name' => 'Miopes y Mancos',
                    'description' => 'Comunidad internacional de habla hispana no competitiva.',
                ]);

                // Agregar algunos soldiers al clan
                $clan->soldiers()->createMany([
                    ['name' => 'xunxillo', 'role' => RoleSquadTypeEnum::SquadLeader->value],
                    ['name' => 'santosmex', 'role' => RoleSquadTypeEnum::Rifleman->value],
                    ['name' => 'nero', 'role' => RoleSquadTypeEnum::Commander->value],
                    ['name' => 'latin', 'role' => RoleSquadTypeEnum::Medic->value],
                    ['name' => 'mendez', 'role' => RoleSquadTypeEnum::Rifleman->value],
                    ['name' => 'manolo', 'role' => RoleSquadTypeEnum::Assault->value],
                    ['name' => 'jeff_alfa', 'role' => RoleSquadTypeEnum::Medic->value],
                    ['name' => 'laykan', 'role' => RoleSquadTypeEnum::Rifleman->value],
                    ['name' => 'daplis', 'role' => RoleSquadTypeEnum::Rifleman->value],
                    ['name' => 'mustanvr', 'role' => RoleSquadTypeEnum::AutomaticRifleman->value],
                    ['name' => 'dgo_echo', 'role' => RoleSquadTypeEnum::Assault->value],
                    ['name' => 'sebas163', 'role' => RoleSquadTypeEnum::Rifleman->value],
                    ['name' => 'pato1910', 'role' => RoleSquadTypeEnum::Rifleman->value],
                    ['name' => 'potxibass', 'role' => RoleSquadTypeEnum::AutomaticRifleman->value],
                    ['name' => 'donpepito', 'role' => RoleSquadTypeEnum::Assault->value],
                    ['name' => 'cap_winters', 'role' => RoleSquadTypeEnum::Engineer->value],
                    ['name' => 'monty_365', 'role' => RoleSquadTypeEnum::Engineer->value],
                    ['name' => 'grayskull', 'role' => RoleSquadTypeEnum::Engineer->value],
                ]);
            }
        }

    }
}
