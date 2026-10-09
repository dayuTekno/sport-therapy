<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Modifikasi billing_cycle menjadi VARCHAR(50) agar mendukung '6_months', 'yearly', dsb.
        $driver = DB::getDriverName();
        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE subscription_plans DROP CONSTRAINT IF EXISTS subscription_plans_billing_cycle_check");
            DB::statement("ALTER TABLE subscription_plans ALTER COLUMN billing_cycle TYPE VARCHAR(50)");
            DB::statement("ALTER TABLE subscription_plans ALTER COLUMN billing_cycle SET NOT NULL");
            DB::statement("ALTER TABLE subscription_plans ALTER COLUMN billing_cycle SET DEFAULT 'monthly'");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE subscription_plans MODIFY billing_cycle VARCHAR(50) NOT NULL DEFAULT 'monthly'");
        }

        // 2. Tambah kolom pendukung rincian biaya & komitmen sesuai gambar
        Schema::table('subscription_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('subscription_plans', 'subtitle')) {
                $table->string('subtitle')->nullable()->after('name');
            }
            if (!Schema::hasColumn('subscription_plans', 'monthly_rate')) {
                $table->decimal('monthly_rate', 15, 2)->default(0)->after('price');
            }
            if (!Schema::hasColumn('subscription_plans', 'commitment_label')) {
                $table->string('commitment_label')->nullable()->after('monthly_rate');
            }
            if (!Schema::hasColumn('subscription_plans', 'duration_in_months')) {
                $table->integer('duration_in_months')->default(6)->after('commitment_label');
            }
        });

        // 3. Update Paket 1: PAKET BERLANGGANAN 6 BULAN
        DB::table('subscription_plans')->where('id', 1)->update([
            'name' => 'PAKET BERLANGGANAN 6 BULAN',
            'slug' => 'paket-6-bulan',
            'subtitle' => '(Paket Minimum Subscribe Awal — Paling Fleksibel)',
            'price' => 1500000.00,
            'monthly_rate' => 250000.00,
            'commitment_label' => 'Total Periode 6 Bulan Pertama: Rp 1.500.000,- (All-in)',
            'billing_cycle' => '6_months',
            'duration_in_months' => 6,
            'max_therapists' => 5,
            'max_patients' => 500,
            'description' => 'Paket minimum subscribe awal paling fleksibel untuk klinik fisioterapi & terapi cedera olahraga rintisan maupun berkembang.',
            'features_json' => json_encode([
                'Booking Online & Reservasi Pasien Mandiri',
                'Evaluasi & Terapi Berjenjang (Fase Akut, Subakut, Pemulihan, Fungsional)',
                'Kasir & Kwitansi Resmi Cetak (Tanpa Potongan Transaksi)',
                'Rekam Medis Khusus Cedera Fisik & Olahraga',
                'Manajemen Stok & Logistik Alat Terapi',
                'Kapasitas hingga 5 Terapis Terdaftar',
                'Gratis Update Sistem & Panduan Implementasi',
            ]),
            'is_active' => true,
            'sort_order' => 1,
            'updated_at' => now(),
        ]);

        // 4. Update Paket 2: PAKET BERLANGGANAN 1 TAHUN
        $plan2Exists = DB::table('subscription_plans')->where('id', 2)->exists();
        $plan2Data = [
            'name' => 'PAKET BERLANGGANAN 1 TAHUN',
            'slug' => 'paket-1-tahun',
            'subtitle' => '(Hemat Ekstra — Tarif Bulanan Lebih Ringan)',
            'price' => 2400000.00,
            'monthly_rate' => 200000.00,
            'commitment_label' => 'Total Periode 1 Tahun Penuh: Rp 2.400.000,- / tahun',
            'billing_cycle' => 'yearly',
            'duration_in_months' => 12,
            'max_therapists' => 15,
            'max_patients' => 2500,
            'description' => 'Paket hemat ekstra dengan komitmen 1 tahun penuh, tarif bulanan jauh lebih hemat dan kuota kapasitas lebih leluasa.',
            'features_json' => json_encode([
                'Seluruh Fitur Lengkap Paket 6 Bulan',
                'Tarif Paling Hemat (Hanya Rp 200.000,- / bulan)',
                'Total Periode 1 Tahun Penuh: Rp 2.400.000,-',
                'Kapasitas hingga 15 Terapis Terdaftar',
                'Kapasitas hingga 2.500 Pasien per Bulan',
                'Prioritas Layanan Bantuan Teknis & Pelatihan',
                'Garansi Backup Data Berkala & SLA 99.9%',
            ]),
            'is_active' => true,
            'sort_order' => 2,
            'updated_at' => now(),
        ];

        if ($plan2Exists) {
            DB::table('subscription_plans')->where('id', 2)->update($plan2Data);
        } else {
            $plan2Data['id'] = 2;
            $plan2Data['created_at'] = now();
            DB::table('subscription_plans')->insert($plan2Data);
        }

        // 5. Hapus paket ke-3 (Enterprise Chain) jika belum ada klinik yang terikat, agar presisi hanya 2 paket sesuai gambar
        $clinicWithPlan3 = DB::table('clinics')->where('subscription_plan_id', 3)->exists();
        if (!$clinicWithPlan3) {
            DB::table('subscription_plans')->where('id', 3)->delete();
        } else {
            DB::table('subscription_plans')->where('id', 3)->update(['is_active' => false]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            if (Schema::hasColumn('subscription_plans', 'subtitle')) {
                $table->dropColumn('subtitle');
            }
            if (Schema::hasColumn('subscription_plans', 'monthly_rate')) {
                $table->dropColumn('monthly_rate');
            }
            if (Schema::hasColumn('subscription_plans', 'commitment_label')) {
                $table->dropColumn('commitment_label');
            }
            if (Schema::hasColumn('subscription_plans', 'duration_in_months')) {
                $table->dropColumn('duration_in_months');
            }
        });
    }
};
