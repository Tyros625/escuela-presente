<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('academic_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_id')->constrained();
            $table->foreignId('section_id')->constrained();
            $table->unsignedBigInteger('school_cycle_id');
            $table->integer('student_limit')->default(10);
            $table->timestamps();

            $table->foreign('school_cycle_id')->references('id')->on('school_cycles')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('academic_groups');
    }
};
