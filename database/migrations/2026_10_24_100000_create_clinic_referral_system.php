<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Non-destructive: menambahkan kode referal milik klinik, relasi klinik perujuk,
     * dan tabel komisi referal antar klinik mitra.
     */
    public function up(): void
    {
        // 1. Kode referal milik klinik & klinik perujuk (yang mereferensikan)
        Schema::table('clinics', function (Blueprint $table) {
            if (!Schema::hasColumn('clinics', 'referral_code')) {
                $table->string('referral_code', 20)->nullable()->unique()->after('clinic_code');
            }
            if (!Schema::hasColumn('clinics', 'referred_by_clinic_id')) {
                $table->foreignId('referred_by_clinic_id')
                    ->nullable()
                    ->after('referral_code')
                    ->constrained('clinics')
                    ->nullOnDelete();
            }
        });

        // 2. Backfill kode referal untuk klinik yang sudah terdaftar sebelumnya
        $existing = DB::table('clinics')->whereNull('referral_code')->get(['id', 'name']);
        foreach ($existing as $clinic) {
            DB::table('clinics')->where('id', $clinic->id)->update([
                'referral_code' => \App\Models\Clinic::generateReferralCode($clinic->name),
            ]);
        }

        // 3. Riwayat komisi referal klinik (1 klinik terundang = 1 komisi saat pertama kali berlangganan)
        if (!Schema::hasTable('clinic_referral_commissions')) {
            Schema::create('clinic_referral_commissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('referrer_clinic_id')->constrained('clinics')->cascadeOnDelete();
                $table->foreignId('referred_clinic_id')->unique()->constrained('clinics')->cascadeOnDelete();
                $table->foreignId('subscription_plan_id')->nullable()->constrained('subscription_plans')->nullOnDelete();
                $table->decimal('base_amount', 15, 2)->default(0); // Harga paket langganan klinik terundang
                $table->string('commission_type', 10)->default('rupiah'); // rupiah | percent (snapshot setting)
                $table->decimal('commission_value', 15, 2)->default(0); // Nilai setting saat komisi dicatat
                $table->decimal('commission_amount', 15, 2)->default(0); // Nominal komisi final
                $table->string('status', 20)->default('pending'); // pending | paid | cancelled
                $table->timestamp('paid_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_referral_commissions');

        Schema::table('clinics', function (Blueprint $table) {
            if (Schema::hasColumn('clinics', 'referred_by_clinic_id')) {
                $table->dropForeign(['referred_by_clinic_id']);
                $table->dropColumn('referred_by_clinic_id');
            }
            if (Schema::hasColumn('clinics', 'referral_code')) {
                $table->dropUnique(['referral_code']);
                $table->dropColumn('referral_code');
            }
        });
    }
};
