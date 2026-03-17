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
            $testUser = User::firstOrCreate(
                ['email' => 'test@mail.com'],
                [
                    'name' => 'Test User',
                    'password' => 'password',
                ]
            );
            if ($testUser->wasRecentlyCreated) {
                $testUser->markEmailAsVerified();
            }
            $testUser->syncRoles(['clan_owner']);

            if (! Clan::query()->where('slug', 'miopes-y-mancos')->exists()) {
                $clan = Clan::factory()->withOwner($testUser)->create([
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
                    ['name' => 'carlos', 'role' => RoleSquadTypeEnum::AutomaticRifleman->value],
                    ['name' => 'pepe', 'role' => RoleSquadTypeEnum::Assault->value],
                    ['name' => 'juan', 'role' => RoleSquadTypeEnum::Rifleman->value],
                    ['name' => 'luis', 'role' => RoleSquadTypeEnum::Rifleman->value],
                    ['name' => 'diego', 'role' => RoleSquadTypeEnum::Medic->value],
                    ['name' => 'fran', 'role' => RoleSquadTypeEnum::Engineer->value],
                    ['name' => 'jose', 'role' => RoleSquadTypeEnum::AutomaticRifleman->value],
                    ['name' => 'mario', 'role' => RoleSquadTypeEnum::Assault->value],
                    ['name' => 'antonio', 'role' => RoleSquadTypeEnum::Rifleman->value],
                    ['name' => 'roberto', 'role' => RoleSquadTypeEnum::Rifleman->value],
                    ['name' => 'sergio', 'role' => RoleSquadTypeEnum::Medic->value],
                    ['name' => 'alberto', 'role' => RoleSquadTypeEnum::Rifleman->value],
                    ['name' => 'ricardo', 'role' => RoleSquadTypeEnum::AutomaticRifleman->value],
                    ['name' => 'fernando', 'role' => RoleSquadTypeEnum::Assault->value],
                    ['name' => 'javier', 'role' => RoleSquadTypeEnum::Engineer->value],
                    ['name' => 'carlos2', 'role' => RoleSquadTypeEnum::Rifleman->value],
                    ['name' => 'miguel', 'role' => RoleSquadTypeEnum::Medic->value],
                    ['name' => 'pablo', 'role' => RoleSquadTypeEnum::Rifleman->value],
                    ['name' => 'alvaro', 'role' => RoleSquadTypeEnum::AutomaticRifleman->value],
                    ['name' => 'jorge', 'role' => RoleSquadTypeEnum::Assault->value],
                    ['name' => 'santy', 'role' => RoleSquadTypeEnum::Rifleman->value],
                ]);
            }
        }

    }
}
