<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Non-destructive: clinic_subscriptions dipakai sebagai request pembelian paket oleh klinik
     * yang di-approve manual oleh SaaS admin.
     */
    public function up(): void
    {
        // 1. payment_status dari ENUM menjadi VARCHAR agar mendukung status 'rejected' & 'cancelled'
        $driver = DB::getDriverName();
        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE clinic_subscriptions DROP CONSTRAINT IF EXISTS clinic_subscriptions_payment_status_check");
            DB::statement("ALTER TABLE clinic_subscriptions ALTER COLUMN payment_status TYPE VARCHAR(20)");
            DB::statement("ALTER TABLE clinic_subscriptions ALTER COLUMN payment_status SET DEFAULT 'pending'");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE clinic_subscriptions MODIFY payment_status VARCHAR(20) NOT NULL DEFAULT 'pending'");
        }

        // 2. Kolom pendukung request pembelian & proses approval
        Schema::table('clinic_subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('clinic_subscriptions', 'requested_by_user_id')) {
                $table->foreignId('requested_by_user_id')->nullable()->after('subscription_plan_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('clinic_subscriptions', 'payment_proof_path')) {
                $table->string('payment_proof_path')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('clinic_subscriptions', 'processed_by_user_id')) {
                $table->foreignId('processed_by_user_id')->nullable()->after('notes')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('clinic_subscriptions', 'processed_at')) {
                $table->timestamp('processed_at')->nullable()->after('processed_by_user_id');
            }
            if (!Schema::hasColumn('clinic_subscriptions', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('processed_at');
            }
        });

        // 3. Index untuk monitoring request (filter status & riwayat per klinik)
        Schema::table('clinic_subscriptions', function (Blueprint $table) {
            $table->index(['payment_status', 'created_at'], 'clinic_subscriptions_status_created_idx');
            $table->index(['clinic_id', 'created_at'], 'clinic_subscriptions_clinic_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinic_subscriptions', function (Blueprint $table) {
            $table->dropIndex('clinic_subscriptions_status_created_idx');
            $table->dropIndex('clinic_subscriptions_clinic_created_idx');

            foreach (['requested_by_user_id', 'processed_by_user_id'] as $fk) {
                if (Schema::hasColumn('clinic_subscriptions', $fk)) {
                    $table->dropForeign([$fk]);
                }
            }

            $columnsToDrop = array_values(array_filter(
                ['requested_by_user_id', 'payment_proof_path', 'processed_by_user_id', 'processed_at', 'admin_notes'],
                fn ($col) => Schema::hasColumn('clinic_subscriptions', $col)
            ));
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
