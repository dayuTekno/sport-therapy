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
        Schema::table('master_medicines', function (Blueprint $table) {
            $table->decimal('stock', 12, 2)->default(0)->after('medicine_international_name');
            $table->string('uom')->default('Tablet')->after('stock'); // Unit of Measure (Tablet, Botol, Pcs)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_medicines', function (Blueprint $table) {
            //
        });
    }
};
