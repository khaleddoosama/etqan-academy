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
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->string('invoice_id')->nullable();     // ID بتاع الفاتورة عند فواتيرك
            $table->string('invoice_key')->nullable();    // مفتاح الفاتورة
            
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('EGP');
            $table->string('status')->default('pending'); // pending, paid, failed
            $table->string('gateway')->default('fawaterak');

            $table->string('customer_first_name');
            $table->string('customer_last_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_address')->nullable();


            $table->string('payment_method')->nullable(); // مثل (Visa, Meeza, Fawry)

            $table->json('response_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
