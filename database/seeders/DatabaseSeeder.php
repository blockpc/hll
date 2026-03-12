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

        $sudo = User::firstOrCreate(
            ['email' => 'sudo@mail.com'],
            [
                'name' => 'Super Administrador',
                'email' => 'sudo@mail.com',
                'password' => 'password',
                'email_verified_at' => now(),
            ]
        );
        $sudo->syncRoles(['sudo']);

        $testUser = User::firstOrCreate(
            ['email' => 'test@mail.com'],
            [
                'name' => 'Test User',
                'email' => 'test@mail.com',
                'password' => 'password',
                'email_verified_at' => now(),
            ]
        );
        $testUser->syncRoles(['user']);
    }
}
