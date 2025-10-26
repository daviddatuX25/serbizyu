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
        
        User::factory(2)->create([
            'firstname' => 'First',
            'lastname' => 'User',
            'password' => bcrypt('password123'),
            'email' => 'user@localhost',
        ])->assignRole('user');

        User::factory()->create([
            'firstname' => 'Moderator',
            'lastname' => 'User',
            'password' => bcrypt('password123'),
            'email' => 'mopderator@localhost',
        ])->assignRole('moderator');

        User::factory()->create([
            'firstname' => 'Admin',
            'lastname' => 'User',
            'password' => bcrypt('password123'),
            'email' => 'admin@localhost',
        ])->assignRole('admin');
    }
}
