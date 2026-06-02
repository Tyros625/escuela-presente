<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_subject_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_group_id')->constrained('academic_groups')->cascadeOnDelete();
            $table->foreignId('specialty_id')->constrained('specialties')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->foreignId('school_cycle_id')->constrained('school_cycles')->cascadeOnDelete();
            $table->string('assignment_type', 16)->default('manual');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['academic_group_id', 'specialty_id'], 'group_subject_assignments_group_specialty_unique');
            $table->index(['school_cycle_id', 'teacher_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_subject_assignments');
    }
};
