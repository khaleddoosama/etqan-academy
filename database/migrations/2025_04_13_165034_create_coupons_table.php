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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('discount', 8, 2);
            $table->enum('type', ['percentage', 'fixed']);
            $table->timestamp('start_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_count')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Step 2: Drop the table
        Schema::dropIfExists('coupons');

        // Step 3: Enable foreign key checks again
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
