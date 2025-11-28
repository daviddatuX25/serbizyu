<?php

namespace Database\Seeders;

use App\Domains\Users\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 1 admin user
        User::create([
            'firstname' => 'Admin',
            'lastname' => 'User',
            'password' => bcrypt('password123'),
            'email' => 'admin@localhost',
        ])->assignRole('admin');

        // Create 9 regular users (for a total of 10 users)
        $firstNames = ['John', 'Maria', 'Carlos', 'Ana', 'Pedro', 'Rosa', 'Juan', 'Sofia', 'Miguel'];
        $lastNames = ['Santos', 'Garcia', 'Lopez', 'Rodriguez', 'Martinez', 'Hernandez', 'Flores', 'Morales', 'Reyes'];

        for ($i = 0; $i < 9; $i++) {
            User::create([
                'firstname' => $firstNames[$i],
                'lastname' => $lastNames[$i],
                'password' => bcrypt('password123'),
                'email' => 'user'.($i + 1).'@localhost',
            ])->assignRole('user');
        }

        $this->command->info('Created 1 admin + 9 users (10 total)');
    }
}
