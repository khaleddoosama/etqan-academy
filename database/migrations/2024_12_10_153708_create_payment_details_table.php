<?php

use App\Enums\PaymentType;
use App\Enums\Status;
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
        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');

            $table->string('whatsapp_number', 20)->nullable();

            $table->string('payment_type')->default(PaymentType::CASH->value);
            $table->string('payment_method')->nullable();

            $table->string('transfer_number')->nullable();
            $table->string('transfer_image')->nullable();

            $table->decimal('amount', 10, 2)->default(0.00);

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
        Schema::dropIfExists('payment_details');
    }
};
