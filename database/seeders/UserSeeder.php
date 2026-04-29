<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name' => 'Admin Sistem',
                'password' => Hash::make('password'),
            ]
        );
        $admin->syncRoles(['admin']);

        // 2. Admisi
        $admisi = User::firstOrCreate(
            ['email' => 'admisi@mail.com'],
            [
                'name' => 'Petugas Admisi',
                'password' => Hash::make('password'),
            ]
        );
        $admisi->syncRoles(['admisi']);

        // 3. Dokter
        $dokter = User::firstOrCreate(
            ['email' => 'dokter@mail.com'],
            [
                'name' => 'Dokter Umum',
                'password' => Hash::make('password'),
            ]
        );
        $dokter->syncRoles(['dokter']);

        // 4. Perawat
        $perawat = User::firstOrCreate(
            ['email' => 'perawat@mail.com'],
            [
                'name' => 'Perawat Jaga',
                'password' => Hash::make('password'),
            ]
        );
        $perawat->syncRoles(['perawat']);

        // 5. Kasir
        $kasir = User::firstOrCreate(
            ['email' => 'kasir@mail.com'],
            [
                'name' => 'Petugas Kasir',
                'password' => Hash::make('password'),
            ]
        );
        $kasir->syncRoles(['kasir']);

        // 6. Apoteker
        $apoteker = User::firstOrCreate(
            ['email' => 'apoteker@mail.com'],
            [
                'name' => 'Apoteker',
                'password' => Hash::make('password'),
            ]
        );
        $apoteker->syncRoles(['apoteker']);

        // 7. Monitoring
        $monitoring = User::firstOrCreate(
            ['email' => 'monitoring@mail.com'],
            [
                'name' => 'Tim Monitoring',
                'password' => Hash::make('password'),
            ]
        );
        $monitoring->syncRoles(['monitoring']);
    }
}
