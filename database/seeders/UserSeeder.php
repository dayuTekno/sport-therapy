<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        // Operator
        $operator = User::firstOrCreate(
            ['email' => 'operator@mail.com'],
            [
                'name' => 'Operator',
                'password' => Hash::make('password'),
            ]
        );
        $operator->assignRole('operator');
    }
}
