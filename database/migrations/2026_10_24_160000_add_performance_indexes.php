<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Index performa. PostgreSQL TIDAK membuat index otomatis untuk foreign key, padahal hampir
     * setiap query difilter clinic_id (ClinicScope) & di-join via patient_id / reservation_id, dsb.
     * Non-destructive: hanya menambah index yang belum ada; tidak mengubah data maupun hasil query.
     */
    protected array $indexes = [
        'users' => [['clinic_id']],
        'clinics' => [['status'], ['referred_by_clinic_id']],
        'master_patients' => [['clinic_id', 'full_name'], ['referral_id']],
        'master_referrals' => [['clinic_id']],
        'therapists' => [['clinic_id']],
        'therapy_types' => [['clinic_id', 'stage_order']],
        'therapy_equipments' => [['clinic_id']],
        'reservations' => [
            ['clinic_id', 'status'],
            ['clinic_id', 'payment_status'],
            ['clinic_id', 'created_at'],
            ['clinic_id', 'paid_at'],
            ['patient_id'],
            ['therapist_id'],
        ],
        'therapy_sessions' => [
            ['clinic_id', 'scheduled_at'],
            ['clinic_id', 'status'],
            ['clinic_id', 'paid_at'],
            ['reservation_id'],
            ['patient_id', 'status'],
            ['therapist_id'],
            ['therapy_type_id'],
        ],
        'clinic_referral_commissions' => [['referrer_clinic_id', 'status'], ['status']],
        'antrians' => [['created_at'], ['patient_id']],
        'medical_records' => [['queue_id']],
        'medical_record_diagnoses' => [['medical_record_id']],
        'medical_record_procedures' => [['medical_record_id']],
        'medical_record_treatments' => [['medical_record_id']],
        'medical_record_medicines' => [['medical_record_id']],
    ];

    public function up(): void
    {
        foreach ($this->indexes as $table => $indexes) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            foreach ($indexes as $columns) {
                if (!Schema::hasColumns($table, $columns) || Schema::hasIndex($table, $columns)) {
                    continue;
                }

                Schema::table($table, function (Blueprint $blueprint) use ($table, $columns) {
                    $blueprint->index($columns, $this->indexName($table, $columns));
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->indexes as $table => $indexes) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            foreach ($indexes as $columns) {
                $name = $this->indexName($table, $columns);
                if (Schema::hasIndex($table, $name)) {
                    Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropIndex($name));
                }
            }
        }
    }

    protected function indexName(string $table, array $columns): string
    {
        return substr('perf_' . $table . '_' . implode('_', $columns) . '_idx', 0, 63);
    }
};
