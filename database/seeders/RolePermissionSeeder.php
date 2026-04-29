<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // reset cache permission
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Menu based permissions
        $permissions = [
            // Admin only
            'menu.master-data',
            'menu.users',
            'menu.roles',
            
            // Admisi
            'menu.registrasi',
            'menu.antrian-admisi',
            
            // Medis
            'menu.amnesa-dokter',
            'menu.amnesa-perawat',
            'menu.rekap-medis',
            
            // Keuangan & Obat
            'menu.kasir',
            'menu.apoteker',
            
            // CRUD permissions that existed before
            'user.view', 'user.create', 'user.edit', 'user.delete',
            'role.view', 'role.create', 'role.edit', 'role.delete',
            'master-eselon.view', 'master-eselon.create', 'master-eselon.edit', 'master-eselon.delete',
            'master-procedures.view', 'master-procedures.create', 'master-procedures.edit', 'master-procedures.delete',
            'master-polyclinic.view', 'master-polyclinic.create', 'master-polyclinic.edit', 'master-polyclinic.delete',
            'master-medicine.view', 'master-medicine.create', 'master-medicine.edit', 'master-medicine.delete',
            'master-icd.view', 'master-icd.create', 'master-icd.edit', 'master-icd.delete',
            'master-doctor.view', 'master-doctor.create', 'master-doctor.edit', 'master-doctor.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // 1. Admin
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo(Permission::all());

        // 2. Admisi
        $admisi = Role::firstOrCreate(['name' => 'admisi', 'guard_name' => 'web']);
        $admisi->givePermissionTo([
            'menu.registrasi',
            'menu.antrian-admisi'
        ]);

        // 3. Dokter
        $dokter = Role::firstOrCreate(['name' => 'dokter', 'guard_name' => 'web']);
        $dokter->givePermissionTo([
            'menu.amnesa-dokter',
            'menu.rekap-medis'
        ]);

        // 4. Perawat
        $perawat = Role::firstOrCreate(['name' => 'perawat', 'guard_name' => 'web']);
        $perawat->givePermissionTo([
            'menu.amnesa-perawat',
            'menu.rekap-medis'
        ]);

        // 5. Kasir
        $kasir = Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']);
        $kasir->givePermissionTo([
            'menu.kasir'
        ]);

        // 6. Apoteker
        $apoteker = Role::firstOrCreate(['name' => 'apoteker', 'guard_name' => 'web']);
        $apoteker->givePermissionTo([
            'menu.apoteker'
        ]);

        // 7. Monitoring
        $monitoring = Role::firstOrCreate(['name' => 'monitoring', 'guard_name' => 'web']);
        $monitoring->givePermissionTo([
            'menu.rekap-medis'
        ]);
        
        // Remove old operator/user roles if they exist to clean up
        $operator = Role::where('name', 'operator')->first();
        if ($operator) $operator->delete();
        
        $userRole = Role::where('name', 'user')->first();
        if ($userRole) $userRole->delete();
    }
}
