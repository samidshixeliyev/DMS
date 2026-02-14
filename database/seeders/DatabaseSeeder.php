<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create default admin user
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin',
                'surname' => 'User',
                'password' => Hash::make('admin123'),
                'user_role' => 'admin',
                'is_deleted' => false,
            ]
        );

        // Create default manager user
        User::firstOrCreate(
            ['username' => 'manager'],
            [
                'name' => 'Manager',
                'surname' => 'User',
                'password' => Hash::make('manager123'),
                'user_role' => 'manager',
                'is_deleted' => false,
            ]
        );

        // Create default regular user
        User::firstOrCreate(
            ['username' => 'user'],
            [
                'name' => 'Regular',
                'surname' => 'User',
                'password' => Hash::make('user123'),
                'user_role' => 'user',
                'is_deleted' => false,
            ]
        );
    }
}
