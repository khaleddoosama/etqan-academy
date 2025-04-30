<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_section_id')->nullable()->after('course_id');
            $table->integer('position')->default('0');


            $table->foreign('parent_section_id')
                ->references('id')
                ->on('sections')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropForeign(['parent_section_id']);
            $table->dropColumn('parent_section_id');
            $table->dropColumn('position');
        });
    }
};
