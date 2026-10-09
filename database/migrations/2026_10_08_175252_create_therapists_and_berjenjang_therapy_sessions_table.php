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
        // 1. Table therapists
        Schema::create('therapists', function (Blueprint $table) {
            $table->id();
            $table->string('therapist_code')->unique();
            $table->string('full_name');
            $table->string('specialization')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Copy existing doctors to therapists if available
        if (Schema::hasTable('master_doctors')) {
            $doctors = \DB::table('master_doctors')->get();
            foreach ($doctors as $doc) {
                \DB::table('therapists')->insert([
                    'id' => $doc->id,
                    'therapist_code' => str_replace('d', 'TRP', str_replace('D', 'TRP', $doc->doctor_code ?? ('TRP-' . $doc->id))),
                    'full_name' => str_replace('Dokter ', 'Terapis ', $doc->full_name),
                    'specialization' => $doc->specialization ?? 'Sport Therapist',
                    'phone' => $doc->phone,
                    'email' => $doc->email,
                    'is_active' => $doc->is_active ?? 1,
                    'created_at' => $doc->created_at ?? now(),
                    'updated_at' => $doc->updated_at ?? now(),
                ]);
            }
        }

        // 2. Table therapy_types (Katalog Jenis Terapi Berjenjang)
        Schema::create('therapy_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('duration_minutes')->default(45);
            $table->integer('stage_order')->default(1);
            $table->decimal('price', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Update reservations foreign key and add current_stage
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['therapist_id']);
            $table->foreign('therapist_id')->references('id')->on('therapists')->nullOnDelete();
            $table->integer('current_stage')->default(1)->after('status');
        });

        // 4. Table therapy_sessions (Sesi Terapi Berjenjang: Terapi A -> Terapi B -> Terapi C)
        Schema::create('therapy_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('master_patients')->cascadeOnDelete();
            $table->foreignId('therapist_id')->nullable()->constrained('therapists')->nullOnDelete();
            $table->foreignId('therapy_type_id')->nullable()->constrained('therapy_types')->nullOnDelete();
            $table->integer('stage_number')->default(1); // 1 = Terapi A, 2 = Terapi B, dst.
            $table->string('stage_name'); // misal: "Terapi A - Penanganan Akut & Manual Therapy"
            $table->dateTime('scheduled_at')->nullable();
            $table->enum('status', ['pending', 'scheduled', 'in_progress', 'completed', 'skipped'])->default('pending');
            $table->text('evaluation_notes')->nullable(); // Evaluasi hasil sesi
            $table->string('recommended_next_stage')->nullable(); // Rekomendasi terapi lanjutan (misal: "Lanjut Terapi B")
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('therapy_sessions');
        
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['therapist_id']);
            $table->dropColumn('current_stage');
        });

        Schema::dropIfExists('therapy_types');
        Schema::dropIfExists('therapists');
    }
};
