<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Users\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // collection acnt be assigned role so have each function
        
        User::create([
            'firstname' => 'First',
            'lastname' => 'User',
            'password' => bcrypt('password123'),
            'email' => 'user@localhost',
        ])->assignRole('user');

        User::create([
            'firstname' => 'Moderator',
            'lastname' => 'User',
            'password' => bcrypt('password123'),
            'email' => 'moderator@localhost',
        ])->assignRole('moderator');

        User::create([
            'firstname' => 'Admin',
            'lastname' => 'User',
            'password' => bcrypt('password123'),
            'email' => 'admin@localhost',
        ])->assignRole('admin');
    }
}
