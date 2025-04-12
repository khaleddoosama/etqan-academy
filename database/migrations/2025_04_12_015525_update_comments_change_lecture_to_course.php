<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['lecture_id']);
            $table->dropColumn('lecture_id');

            $table->foreignId('course_id')->constrained()->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');

            $table->foreignId('lecture_id')->constrained()->onDelete('cascade');
        });
    }
};
