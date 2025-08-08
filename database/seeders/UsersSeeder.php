<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        User::create([
            'name' => 'Venue Owner',
            'email' => 'venue@example.com',
            'password' => Hash::make('password'),
            'role' => 'vendor'
        ]);

        User::create([
            'name' => 'Service Provider',
            'email' => 'service@example.com',
            'password' => Hash::make('password'),
            'role' => 'vendor'
        ]);

        User::create([
            'name' => 'Regular Client',
            'email' => 'client@example.com',
            'password' => Hash::make('password'),
            'role' => 'client'
        ]);
    }
}
