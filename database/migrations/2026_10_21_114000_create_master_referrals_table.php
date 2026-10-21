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
        // 1. Create table master_referrals
        if (!Schema::hasTable('master_referrals')) {
            Schema::create('master_referrals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('clinic_id')->nullable()->constrained('clinics')->cascadeOnDelete();
                $table->string('code')->nullable()->index();
                $table->string('name');
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('agency')->nullable(); // Instansi / Lembaga / Profesi / Asal Referal
                $table->decimal('commission_amount', 12, 2)->default(0)->nullable(); // Komisi per sesi terapi
                $table->text('notes')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Update master_patients: replace referral_therapist_id with referral_id
        if (Schema::hasTable('master_patients')) {
            Schema::table('master_patients', function (Blueprint $table) {
                if (Schema::hasColumn('master_patients', 'referral_therapist_id')) {
                    $table->dropForeign(['referral_therapist_id']);
                    $table->dropColumn('referral_therapist_id');
                }
                if (!Schema::hasColumn('master_patients', 'referral_id')) {
                    $table->foreignId('referral_id')
                        ->nullable()
                        ->after('occupation')
                        ->constrained('master_referrals')
                        ->nullOnDelete();
                }
            });
        }

        // 3. Register menu.referral permission if Spatie permission is installed
        try {
            if (class_exists(\Spatie\Permission\Models\Permission::class)) {
                $perm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'menu.referral', 'guard_name' => 'web']);
                if (class_exists(\Spatie\Permission\Models\Role::class)) {
                    $roles = \Spatie\Permission\Models\Role::whereIn('name', ['Superadmin', 'superadmin', 'Admin Klinik', 'admin-klinik'])->get();
                    foreach ($roles as $role) {
                        if (!$role->hasPermissionTo($perm)) {
                            $role->givePermissionTo($perm);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // Non-critical, ignore if permission tables differ
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('master_patients') && Schema::hasColumn('master_patients', 'referral_id')) {
            Schema::table('master_patients', function (Blueprint $table) {
                $table->dropForeign(['referral_id']);
                $table->dropColumn('referral_id');
            });
        }

        Schema::dropIfExists('master_referrals');
    }
};
