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
        Schema::table('therapy_sessions', function (Blueprint $table) {
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid')->after('status');
            $table->decimal('total_price', 15, 2)->default(0)->after('payment_status');
            $table->decimal('discount', 15, 2)->default(0)->after('total_price');
            $table->decimal('paid_amount', 15, 2)->default(0)->after('discount');
            $table->string('payment_method')->nullable()->after('paid_amount');
            $table->string('invoice_code')->nullable()->after('payment_method');
            $table->timestamp('paid_at')->nullable()->after('invoice_code');
            $table->foreignId('cashier_user_id')->nullable()->after('paid_at')->constrained('users')->nullOnDelete();
            $table->text('cashier_notes')->nullable()->after('cashier_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('therapy_sessions', function (Blueprint $table) {
            $table->dropForeign(['cashier_user_id']);
            $table->dropColumn([
                'payment_status',
                'total_price',
                'discount',
                'paid_amount',
                'payment_method',
                'invoice_code',
                'paid_at',
                'cashier_user_id',
                'cashier_notes',
            ]);
        });
    }
};
