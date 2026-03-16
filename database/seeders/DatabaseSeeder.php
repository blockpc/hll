<?php

namespace Database\Seeders;

use App\Models\Clan;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

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
            Clan::factory()->withOwner($testUser)->create([
                'alias' => 'MYM',
                'slug' => 'miopes-y-mancos',
                'name' => 'Miopes y Mancos',
                'description' => 'Comunidad internacional de habla hispana no competitiva.',
            ]);
        }
    }
}
