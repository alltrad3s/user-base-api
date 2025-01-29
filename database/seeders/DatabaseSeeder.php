<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $roles = [
            ['name' => 'admin'],
            ['name' => 'user'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // Create admin user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('12345678'), // Add a default password
            'role' => 'admin' // Assign admin role
        ]);

        // Optionally create more users with 'user' role
        User::factory(10)->create([
            'role' => 'user'
        ]);
    }
}