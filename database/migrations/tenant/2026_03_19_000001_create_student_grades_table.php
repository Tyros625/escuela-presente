<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('academic_group_id')->constrained('academic_groups');
            $table->string('subject')->nullable();
            $table->decimal('partial_1', 4, 1)->nullable();
            $table->decimal('partial_2', 4, 1)->nullable();
            $table->decimal('partial_3', 4, 1)->nullable();
            $table->decimal('average', 4, 1)->nullable();
            $table->enum('status', ['approved', 'failed'])->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_grades');
    }
};

