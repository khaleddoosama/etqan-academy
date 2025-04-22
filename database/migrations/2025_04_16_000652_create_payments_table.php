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

            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('whatsapp_number', 20)->nullable();

            $table->string('payment_method')->nullable();

            $table->string('transfer_identifier')->nullable(); // number or email
            $table->string('transfer_image')->nullable();

            $table->decimal('amount_before_coupon', 10, 2)->default(0.00);
            $table->decimal('amount_after_coupon', 10, 2)->default(0.00);
            $table->decimal('amount_confirmed', 10, 2)->default(0.00);

            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->onDelete('set null');
            $table->string('discount')->nullable();
            $table->string('type')->nullable();

            $table->string('status')->default(Status::PENDING->value);

            // approved by admin
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();

            // rejected by admin
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable();

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
