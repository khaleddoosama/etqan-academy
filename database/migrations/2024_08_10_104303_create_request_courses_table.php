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
        Schema::create('request_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('phone')->nullable();
            $table->string('message')->nullable();
            $table->boolean('status')->default(0);

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
        Schema::dropIfExists('request_courses');
    }
};
