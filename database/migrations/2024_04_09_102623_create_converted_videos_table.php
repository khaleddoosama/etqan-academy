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
        Schema::create('converted_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecture_id')->constrained()->onDelete('cascade');
            $table->string('mp4_Format_240')->nullable();
            $table->string('mp4_Format_360')->nullable();
            $table->string('mp4_Format_480')->nullable();
            $table->string('mp4_Format_720')->nullable();
            $table->string('mp4_Format_1080')->nullable();
            $table->string('webm_Format_240')->nullable();
            $table->string('webm_Format_360')->nullable();
            $table->string('webm_Format_480')->nullable();
            $table->string('webm_Format_720')->nullable();
            $table->string('webm_Format_1080')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('converted_videos');
    }
};
