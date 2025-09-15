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
        
        User::factory(3)->create()->each(function ($user) {
            $user->assignRole('user');
        });
        

        User::factory()->create([
            'firstname' => 'Moderator',
            'lastname' => 'User',
            'password' => bcrypt('serbizmoderator123')
        ])->assignRole('moderator');

        User::factory()->create([
            'firstname' => 'Admin',
            'lastname' => 'User',
            'password' => bcrypt('serbizadmin123')
        ])->assignRole('admin');
    }
}
