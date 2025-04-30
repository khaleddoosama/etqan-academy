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
        Schema::create('package_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('from')->nullable();
            $table->string('logo')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->string('duration')->nullable();
            $table->string('device_limit')->nullable();
            $table->string('number_of_downloads')->nullable();
            $table->text('description')->nullable();
            $table->boolean ('has_ai_access')->nullable();
            $table->boolean ('has_flaticon_access')->nullable();
            $table->json('programs')->nullable();
            $table->string('status')->default('approved');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_plans');
    }
};
