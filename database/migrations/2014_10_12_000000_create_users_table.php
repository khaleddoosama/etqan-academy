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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');

            $table->string('username')->unique()->nullable();
            $table->string('code')->unique();
            $table->string('slug')->unique()->nullable();

            $table->text('bio')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            $table->string('picture')->nullable();
            $table->string('phone')->nullable();
            $table->string('country')->nullable();
            $table->string('address')->nullable();
            $table->string('job_title')->nullable();
            $table->string('gender')->nullable();
            $table->integer('age')->nullable();
            $table->decimal('points', 8, 2)->default(0);

            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('last_login')->nullable();

            $table->enum('role', ['student', 'instructor', 'admin'])->default('student');
            $table->integer('status')->default(1)->comment('1: approved, 2: rejected, 3: removed');
            $table->timestamp('approved_at')->default(now());
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('removed_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
