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
        // Reset cache permission
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Menu & Feature based permissions
        $permissions = [
            // Admin only (Settings & Master Data)
            'menu.master-data',
            'menu.users',
            'menu.roles',

            // Pasien & Reservasi
            'menu.pasien',
            'menu.registrasi',       // Buat Reservasi
            'menu.antrian-admisi',   // Reservasi Terapi
            'menu.riwayat-terapi',   // Riwayat Terapi (Selesai & Batal)

            // Terapi & Medis
            'menu.sesi-terapi',      // Sesi Terapi Pasien
            'menu.amnesa-dokter',    // Sesi Terapi Pasien (legacy alias)
            'menu.jadwal-terapis',   // Jadwal Terapis
            'menu.terapis',          // Master Terapis
            'menu.jenjang-terapi',   // Jenjang Terapi
            'menu.rekap-medis',      // Laporan Rekap Rekam Medis

            // Keuangan & Stok Peralatan
            'menu.kasir',            // Kasir & Pembayaran
            'menu.apoteker',         // Stok Peralatan Terapi
            'menu.peralatan-terapi', // Master Peralatan Terapi

            // CRUD permissions
            'user.view', 'user.create', 'user.edit', 'user.delete',
            'role.view', 'role.create', 'role.edit', 'role.delete',
            'master-eselon.view', 'master-eselon.create', 'master-eselon.edit', 'master-eselon.delete',
            'master-medicine.view', 'master-medicine.create', 'master-medicine.edit', 'master-medicine.delete',
            'master-doctor.view', 'master-doctor.create', 'master-doctor.edit', 'master-doctor.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // =========================================================================
        // 1. SAAS ADMINISTRATOR (Akses Platform SaaS Tertinggi)
        // =========================================================================
        $saasAdmin = Role::firstOrCreate(['name' => 'saas_admin', 'guard_name' => 'web']);
        $saasAdmin->syncPermissions(Permission::all());

        // =========================================================================
        // 2. ADMIN KLINIK (Akses Penuh Seluruh Modul Klinik & Pengelolaan User Staf)
        // =========================================================================
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminPermissions = [
            'menu.master-data',
            'menu.users',
            'menu.roles',
            'menu.pasien',
            'menu.registrasi',
            'menu.antrian-admisi',
            'menu.riwayat-terapi',
            'menu.sesi-terapi',
            'menu.amnesa-dokter',
            'menu.jadwal-terapis',
            'menu.terapis',
            'menu.jenjang-terapi',
            'menu.rekap-medis',
            'menu.kasir',
            'menu.apoteker',
            'menu.peralatan-terapi',
            'user.view', 'user.create', 'user.edit', 'user.delete',
            'role.view', 'role.create', 'role.edit',
        ];
        $admin->syncPermissions($adminPermissions);

        // =========================================================================
        // 3. OPERATOR KLINIK (Akses Seluruh Operasional Layanan Tanpa Menu Settings)
        // =========================================================================
        $operator = Role::firstOrCreate(['name' => 'operator', 'guard_name' => 'web']);
        $operatorPermissions = [
            'menu.master-data',
            'menu.pasien',
            'menu.registrasi',
            'menu.antrian-admisi',
            'menu.riwayat-terapi',
            'menu.sesi-terapi',
            'menu.amnesa-dokter',
            'menu.jadwal-terapis',
            'menu.terapis',
            'menu.jenjang-terapi',
            'menu.rekap-medis',
            'menu.kasir',
            'menu.apoteker',
            'menu.peralatan-terapi',
        ];
        $operator->syncPermissions($operatorPermissions);

        // Bersihkan role usang di luar admin, operator, saas_admin
        $obsoleteRoles = ['admisi', 'dokter', 'perawat', 'kasir', 'monitoring', 'terapis', 'clinic_admin', 'logistik', 'apoteker', 'user'];
        foreach ($obsoleteRoles as $oldRole) {
            $r = Role::where('name', $oldRole)->first();
            if ($r) {
                $r->delete();
            }
        }

        // Refresh cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
