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
        Schema::create('lectures', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique()->nullable();
            $table->string('video')->nullable();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('disk')->default('public');
            $table->string('position')->default('0');

            $table->foreignId('section_id')->constrained()->onDelete('cascade');

            $table->integer('hours')->nullable();
            $table->integer('minutes')->nullable();
            $table->integer('seconds')->nullable();

            $table->string('quality')->nullable();

            $table->boolean('processed')->default(false);
            $table->boolean('longitudinal')->default(false);

            // attachments
            $table->json('attachments')->nullable();

            $table->integer('status')->default(0);

            //unique for title and section
            $table->unique(['title', 'section_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lectures');
    }
};
