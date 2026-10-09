<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Administrator SaaS (Superadmin Platform Global, clinic_id null)
        $saasAdmin = User::firstOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name' => 'Administrator SaaS',
                'password' => Hash::make('password'),
                'clinic_id' => null,
            ]
        );
        $saasAdmin->clinic_id = null;
        $saasAdmin->save();
        $saasAdmin->syncRoles(['saas_admin']);

        // 2. Administrator Klinik (Admin Klinik RSTBDI, clinic_id 1)
        $clinicAdmin = User::firstOrCreate(
            ['email' => 'admin.rstbdi@mail.com'],
            [
                'name' => 'Admin Klinik RSTBDI',
                'password' => Hash::make('password'),
                'clinic_id' => 1,
            ]
        );
        $clinicAdmin->clinic_id = 1;
        $clinicAdmin->save();
        $clinicAdmin->syncRoles(['admin']);

        // 3. Operator Klinik RSTBDI (Petugas Operasional, clinic_id 1)
        $operator = User::firstOrCreate(
            ['email' => 'operator@mail.com'],
            [
                'name' => 'Operator Klinik',
                'password' => Hash::make('password'),
                'clinic_id' => 1,
            ]
        );
        $operator->clinic_id = 1;
        $operator->save();
        $operator->syncRoles(['operator']);

        // Staf klinik lainnya disinkronkan ke role operator
        $staffUsers = [
            ['email' => 'admisi@mail.com', 'name' => 'Petugas Admisi'],
            ['email' => 'terapis@mail.com', 'name' => 'Terapis Olahraga'],
            ['email' => 'kasir@mail.com', 'name' => 'Petugas Kasir'],
            ['email' => 'perawat@mail.com', 'name' => 'Perawat Jaga'],
            ['email' => 'dokter@mail.com', 'name' => 'Dokter Umum'],
            ['email' => 'logistik@mail.com', 'name' => 'Pengelola Logistik & Peralatan'],
            ['email' => 'monitoring@mail.com', 'name' => 'Tim Monitoring'],
        ];

        foreach ($staffUsers as $staff) {
            $u = User::firstOrCreate(
                ['email' => $staff['email']],
                [
                    'name' => $staff['name'],
                    'password' => Hash::make('password'),
                    'clinic_id' => 1,
                ]
            );
            $u->clinic_id = 1;
            $u->save();
            $u->syncRoles(['operator']);
        }
    }
}
