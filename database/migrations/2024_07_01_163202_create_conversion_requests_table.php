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
            // full name
            $table->string('name');
            // code
            $table->string('code');
            // email
            $table->string('email');
            // phone
            $table->string('phone');
            // whatsapp
            $table->string('whatsapp');
            // points
            $table->decimal('points', 8, 2)->default(0);
            // phone wallet
            $table->string('phone_wallet');
            // password
            $table->string('password');
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
