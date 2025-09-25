<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_courses', function (Blueprint $table) {
            $table->dateTime('expires_at')->nullable()->after('status');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('user_courses', function (Blueprint $table) {
            $table->dropIndex(['expires_at']);
            $table->dropColumn('expires_at');
        });
    }
};

