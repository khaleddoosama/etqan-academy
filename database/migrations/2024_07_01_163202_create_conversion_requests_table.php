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
        Schema::create('conversion_requests', function (Blueprint $table) {
            $table->id();
            // user_id
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->integer('points');
            $table->string('wallet_phone');
            // status
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversion_requests');
    }
};
