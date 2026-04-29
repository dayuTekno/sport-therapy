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
        Schema::create('admission_queues', function (Blueprint $table) {
            $table->id();
            $table->integer('queue_number');
            $table->string('queue_code');
            $table->enum('status', ['waiting', 'called', 'completed', 'skipped'])->default('waiting');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_queues');
    }
};
