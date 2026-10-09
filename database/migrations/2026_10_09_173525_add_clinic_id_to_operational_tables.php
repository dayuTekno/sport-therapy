<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Inisialisasi Paket Langganan Default
        $planId = DB::table('subscription_plans')->insertGetId([
            'name' => 'Professional Clinic',
            'slug' => 'pro-clinic',
            'price' => 750000,
            'billing_cycle' => 'monthly',
            'max_therapists' => 10,
            'max_patients' => 1000,
            'description' => 'Paket lengkap klinik spesialis terapi & cedera olahraga dengan kapasitas 10 terapis dan fitur kasir invoice.',
            'features_json' => json_encode([
                'Booking & WhatsApp Reminder Otomatis',
                'Evaluasi Terapi Berjenjang (Tahap A-D)',
                'Kasir & Kwitansi Resmi Cetak',
                'Rekam Medis Khusus Cedera Fisik',
                'Manajemen Stok Alat Terapi',
            ]),
            'is_active' => true,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Paket Tambahan: Starter & Enterprise
        DB::table('subscription_plans')->insert([
            [
                'name' => 'Starter Clinic',
                'slug' => 'starter-clinic',
                'price' => 350000,
                'billing_cycle' => 'monthly',
                'max_therapists' => 3,
                'max_patients' => 250,
                'description' => 'Cocok untuk fisioterapi mandiri atau klinik rintisan.',
                'features_json' => json_encode([
                    'Booking Online & Jadwal Terapi',
                    'Sesi Terapi Standar',
                    'Manajemen Pasien Terpadu',
                ]),
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Enterprise Chain',
                'slug' => 'enterprise-chain',
                'price' => 1800000,
                'billing_cycle' => 'monthly',
                'max_therapists' => 50,
                'max_patients' => 10000,
                'description' => 'Solusi waralaba klinik olahraga dan jaringan cabang rumah sakit.',
                'features_json' => json_encode([
                    'Seluruh Fitur Professional',
                    'Dukungan Multi-Cabang & Terapis Tak Terbatas',
                    'Laporan Analitik Eksekutif Lanjutan',
                    'Prioritas Customer Support 24/7',
                ]),
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 2. Inisialisasi Klinik Default (Tenant Pertama: Sport Therapy RSTBDI)
        $defaultClinicId = DB::table('clinics')->insertGetId([
            'clinic_code' => 'CLN-RSTBDI-001',
            'name' => 'Sport Therapy Clinic RSTBDI',
            'slug' => 'sport-therapy-rstbdi',
            'subdomain' => 'rstbdi',
            'phone_number' => '085183036722',
            'email' => 'klinik.rstbdi@mail.com',
            'address' => 'Jl. Pelayanan Kesehatan No. 10',
            'status' => 'active',
            'subscription_plan_id' => $planId,
            'max_therapists' => 10,
            'max_patients' => 1000,
            'is_active' => true,
            'notes' => 'Klinik utama / tenant awal platform Sport Therapy RSTBDI.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Tambahkan clinic_id ke tabel users
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'clinic_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('clinic_id')->nullable()->after('id')->constrained('clinics')->nullOnDelete();
            });
            // Update user klinik (semua kecuali ID 1 yang menjadi superadmin)
            DB::table('users')->where('id', '>', 1)->update(['clinic_id' => $defaultClinicId]);
        }

        // 4. Tambahkan clinic_id ke master_patients
        if (Schema::hasTable('master_patients') && !Schema::hasColumn('master_patients', 'clinic_id')) {
            Schema::table('master_patients', function (Blueprint $table) {
                $table->foreignId('clinic_id')->nullable()->after('id')->constrained('clinics')->cascadeOnDelete();
            });
            DB::table('master_patients')->update(['clinic_id' => $defaultClinicId]);
        }

        // 5. Tambahkan clinic_id ke therapists
        if (Schema::hasTable('therapists') && !Schema::hasColumn('therapists', 'clinic_id')) {
            Schema::table('therapists', function (Blueprint $table) {
                $table->foreignId('clinic_id')->nullable()->after('id')->constrained('clinics')->cascadeOnDelete();
            });
            DB::table('therapists')->update(['clinic_id' => $defaultClinicId]);
        }

        // 6. Tambahkan clinic_id ke therapy_types
        if (Schema::hasTable('therapy_types') && !Schema::hasColumn('therapy_types', 'clinic_id')) {
            Schema::table('therapy_types', function (Blueprint $table) {
                $table->foreignId('clinic_id')->nullable()->after('id')->constrained('clinics')->cascadeOnDelete();
            });
            DB::table('therapy_types')->update(['clinic_id' => $defaultClinicId]);
        }

        // 7. Tambahkan clinic_id ke therapy_equipments
        if (Schema::hasTable('therapy_equipments') && !Schema::hasColumn('therapy_equipments', 'clinic_id')) {
            Schema::table('therapy_equipments', function (Blueprint $table) {
                $table->foreignId('clinic_id')->nullable()->after('id')->constrained('clinics')->cascadeOnDelete();
            });
            DB::table('therapy_equipments')->update(['clinic_id' => $defaultClinicId]);
        }

        // 8. Tambahkan clinic_id ke reservations
        if (Schema::hasTable('reservations') && !Schema::hasColumn('reservations', 'clinic_id')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->foreignId('clinic_id')->nullable()->after('id')->constrained('clinics')->cascadeOnDelete();
            });
            DB::table('reservations')->update(['clinic_id' => $defaultClinicId]);
        }

        // 9. Tambahkan clinic_id ke therapy_sessions
        if (Schema::hasTable('therapy_sessions') && !Schema::hasColumn('therapy_sessions', 'clinic_id')) {
            Schema::table('therapy_sessions', function (Blueprint $table) {
                $table->foreignId('clinic_id')->nullable()->after('id')->constrained('clinics')->cascadeOnDelete();
            });
            DB::table('therapy_sessions')->update(['clinic_id' => $defaultClinicId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'therapy_sessions',
            'reservations',
            'therapy_equipments',
            'therapy_types',
            'therapists',
            'master_patients',
            'users'
        ];

        foreach ($tables as $t) {
            if (Schema::hasTable($t) && Schema::hasColumn($t, 'clinic_id')) {
                Schema::table($t, function (Blueprint $table) use ($t) {
                    $table->dropForeign([ 'clinic_id' ]);
                    $table->dropColumn('clinic_id');
                });
            }
        }
    }
};
