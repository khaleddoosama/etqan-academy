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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->LONGTEXT('description')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->integer('number_of_levels')->default(1);
            $table->enum('language', ['ar', 'en'])->default('ar');
            $table->string('thumbnail')->nullable();
            $table->float('price')->nullable();
            $table->float('discount_price')->nullable();
            $table->integer('status')->default(1);

            // array of programs
            $table->json('programs')->nullable();

            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('instructor_id')->references('id')->on('instructors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
