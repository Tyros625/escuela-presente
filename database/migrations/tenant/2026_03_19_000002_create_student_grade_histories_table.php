<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_grade_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_grade_id')->constrained('student_grades')->onDelete('cascade');
            $table->dateTime('changed_at');
            $table->string('field_changed');
            $table->decimal('old_value', 4, 1)->nullable();
            $table->decimal('new_value', 4, 1)->nullable();
            $table->text('reason')->nullable();
            $table->foreignId('changed_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_grade_histories');
    }
};

