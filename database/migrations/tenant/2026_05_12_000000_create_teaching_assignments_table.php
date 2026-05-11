<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teaching_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('specialty_id')->constrained('specialties')->cascadeOnDelete();
            $table->foreignId('academic_group_id')->constrained('academic_groups')->cascadeOnDelete();
            $table->string('shift', 16);
            $table->string('day_of_week', 20);
            $table->string('time_slot', 40);
            $table->string('assignment_type', 16)->default('manual');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['teacher_id', 'shift', 'day_of_week', 'time_slot', 'is_active'], 'ta_teacher_slot_idx');
            $table->index(['academic_group_id', 'shift', 'day_of_week', 'time_slot', 'is_active'], 'ta_group_slot_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teaching_assignments');
    }
};
