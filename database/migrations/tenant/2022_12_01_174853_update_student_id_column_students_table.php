<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('student_academics', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->foreign('student_id')->references('id')->on('students')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('student_healths', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->foreign('student_id')->references('id')->on('students')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('student_relatives', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->foreign('student_id')->references('id')->on('students')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('student_socioeconomics', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->foreign('student_id')->references('id')->on('students')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('student_academics', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->foreign('student_id')->references('id')->on('students');
        });

        Schema::table('student_healths', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->foreign('student_id')->references('id')->on('students');
        });

        Schema::table('student_relatives', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->foreign('student_id')->references('id')->on('students');
        });

        Schema::table('student_socioeconomics', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->foreign('student_id')->references('id')->on('students');
        });
    }
};
