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
        // 1. Subscription Plans (Paket Langganan SaaS)
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Starter, Pro Clinic, Enterprise Chain
            $table->string('slug')->unique();
            $table->decimal('price', 15, 2)->default(0);
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
            $table->integer('max_therapists')->default(3);
            $table->integer('max_patients')->default(250);
            $table->text('description')->nullable();
            $table->json('features_json')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(1);
            $table->timestamps();
        });

        // 2. Clinics (Tenant / Klinik Mitra Terdaftar)
        Schema::create('clinics', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_code')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('subdomain')->nullable()->unique();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('logo_path')->nullable();
            $table->enum('status', ['active', 'trial', 'suspended', 'expired'])->default('trial');
            $table->timestamp('trial_ends_at')->nullable();
            $table->foreignId('subscription_plan_id')->nullable()->constrained('subscription_plans')->nullOnDelete();
            $table->integer('max_therapists')->default(5);
            $table->integer('max_patients')->default(500);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. Clinic Subscriptions (Riwayat Billing / Langganan SaaS Klinik)
        Schema::create('clinic_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->nullable()->constrained('subscription_plans')->nullOnDelete();
            $table->string('invoice_code')->unique();
            $table->decimal('amount', 15, 2)->default(0);
            $table->enum('payment_status', ['pending', 'paid', 'expired', 'failed'])->default('paid');
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 4. Landing Page Settings (CMS Kelola Bagian Luar Website)
        Schema::create('landing_page_settings', function (Blueprint $table) {
            $table->id();
            $table->string('section_key'); // hero, about, features, pricing_header, contact, cta
            $table->string('title')->nullable();
            $table->text('subtitle')->nullable();
            $table->longText('content_json')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_settings');
        Schema::dropIfExists('clinic_subscriptions');
        Schema::dropIfExists('clinics');
        Schema::dropIfExists('subscription_plans');
    }
};
