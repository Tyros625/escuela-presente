<?php

namespace Database\Seeders;

use App\Models\Tenants\AcademicGroup;
use App\Models\Tenants\Grade;
use App\Models\Tenants\SchoolCycle;
use App\Models\Tenants\Section;
use Illuminate\Database\Seeder;

class AcademicDataSeeder extends Seeder
{
    public function run()
    {
        $grades = [1, 2, 3, 4, 5, 6];

        foreach ($grades as $grade) {
            if (! Grade::where('description', $grade)->exists()) {
                Grade::create(['description' => $grade]);
            }
        }

        $groups = ['A', 'B', 'C', 'D', 'E', 'F'];

        foreach ($groups as $group) {
            if (! Section::where('description', $group)->exists()) {
                Section::create(['description' => $group]);
            }
        }

        $schoolCycles = ['2022-2023', '2023-2024', '2024-2025', '2025-2026'];

        foreach ($schoolCycles as $group) {
            if (! SchoolCycle::where('description', $group)->exists()) {
                SchoolCycle::create(['description' => $group]);
            }
        }

        $groups = [
            ['grade_id' => 1, 'section_id' => 1, 'school_cycle_id' => 1, 'student_limit' => 20],
            ['grade_id' => 1, 'section_id' => 2, 'school_cycle_id' => 1, 'student_limit' => 20],
            ['grade_id' => 1, 'section_id' => 3, 'school_cycle_id' => 1, 'student_limit' => 20],
            ['grade_id' => 1, 'section_id' => 4, 'school_cycle_id' => 1, 'student_limit' => 20],
            ['grade_id' => 1, 'section_id' => 5, 'school_cycle_id' => 1, 'student_limit' => 20],
            ['grade_id' => 1, 'section_id' => 6, 'school_cycle_id' => 1, 'student_limit' => 20],
            ['grade_id' => 2, 'section_id' => 1, 'school_cycle_id' => 1, 'student_limit' => 20],
            ['grade_id' => 2, 'section_id' => 2, 'school_cycle_id' => 1, 'student_limit' => 20],
            ['grade_id' => 2, 'section_id' => 3, 'school_cycle_id' => 1, 'student_limit' => 20],
            ['grade_id' => 2, 'section_id' => 4, 'school_cycle_id' => 1, 'student_limit' => 20],
            ['grade_id' => 2, 'section_id' => 5, 'school_cycle_id' => 1, 'student_limit' => 20],
            ['grade_id' => 2, 'section_id' => 6, 'school_cycle_id' => 1, 'student_limit' => 20],
            ['grade_id' => 3, 'section_id' => 1, 'school_cycle_id' => 1, 'student_limit' => 20],
            ['grade_id' => 3, 'section_id' => 2, 'school_cycle_id' => 1, 'student_limit' => 20],
            ['grade_id' => 3, 'section_id' => 3, 'school_cycle_id' => 1, 'student_limit' => 20],
            ['grade_id' => 3, 'section_id' => 4, 'school_cycle_id' => 1, 'student_limit' => 20],
            ['grade_id' => 3, 'section_id' => 5, 'school_cycle_id' => 1, 'student_limit' => 20],
            ['grade_id' => 3, 'section_id' => 6, 'school_cycle_id' => 1, 'student_limit' => 20],
        ];

        foreach ($groups as $group) {
            AcademicGroup::create([
                'grade_id' => $group['grade_id'],
                'section_id' => $group['section_id'],
                'school_cycle_id' => $group['school_cycle_id'],
                'student_limit' => $group['student_limit'],
            ]);
        }
    }
}
