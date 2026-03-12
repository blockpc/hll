<?php

namespace Database\Seeders;

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

        User::factory(10)->create();

        $sudo = User::firstOrCreate(
            ['email' => 'sudo@mail.com'],
            User::factory()->make([
                'name' => 'Super Administrador',
                'email' => 'sudo@mail.com',
            ])->toArray(),
        );
        $sudo->syncRoles(['sudo']);

        $testUser = User::firstOrCreate(
            ['email' => 'test@mail.com'],
            User::factory()->make([
                'name' => 'Test User',
                'email' => 'test@mail.com',
            ])->toArray(),
        );
        $testUser->syncRoles(['user']);
    }
}
