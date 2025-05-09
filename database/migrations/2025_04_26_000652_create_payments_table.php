<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\PaymentType;
use App\Enums\Status;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('payment_details');
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('restrict');

            $table->string('invoice_id')->nullable();
            $table->string('invoice_key')->nullable();

            $table->string('gateway')->default('fawaterak');

            $table->decimal('amount_before_coupon', 10, 2)->default(0.00);
            $table->decimal('amount_after_coupon', 10, 2)->default(0.00);
            $table->decimal('amount_confirmed', 10, 2)->default(0.00);

            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->onDelete('restrict');
            $table->string('discount')->nullable();
            $table->string('type')->nullable();

            $table->string('transfer_number')->nullable();
            $table->string('transfer_image')->nullable();

            $table->string('currency')->default('EGP');

            $table->string('payment_method_id')->nullable();
            $table->string('payment_method')->nullable(); // مثل (Visa, Meeza, Fawry)

            $table->json('payment_data')->nullable();
            $table->json('response_payload')->nullable();

            $table->string('status')->default('pending'); // pending, paid, failed, canceled, refunded, expired

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
