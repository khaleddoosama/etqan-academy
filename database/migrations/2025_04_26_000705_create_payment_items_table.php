<?php

use App\Enums\PaymentType;
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
        Schema::create('payment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('restrict');
            $table->foreignId('course_installment_id')->nullable()->constrained('course_installments')->onDelete('restrict');
            $table->foreignId('package_plan_id')->nullable()->constrained('package_plans')->onDelete('restrict');
            $table->foreignId('payment_id')->constrained('payments')->onDelete('restrict');
            $table->string('payment_type')->default(PaymentType::CASH->value);

            $table->decimal('amount', 10, 2)->default(0.00);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_items');
    }
};
