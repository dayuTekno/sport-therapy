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
        Schema::create('master_news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')
                  ->references('id')
                  ->on('users')
                  ->cascadeOnDelete();
            $table->string("slug")->unique();
            $table->string("title");
            $table->text("tags")->nullable();
            $table->text("description");
            $table->text("thumbnail");
            $table->enum("publish_st", ["draft", "publish"])->default("draft");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_news');
    }
};
