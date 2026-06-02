<?php

namespace App\Services;

use App\Models\Tenants\AcademicGroup;
use App\Models\Tenants\Grade;
use App\Models\Tenants\GroupSubjectAssignment;
use App\Models\Tenants\Specialty;
use Illuminate\Support\Collection;

class GroupSubjectAssignmentMatrixService
{
    public function build(int $schoolCycleId): array
    {
        $grades = Grade::query()
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $groups = AcademicGroup::query()
            ->where('school_cycle_id', $schoolCycleId)
            ->with(['grade', 'section'])
            ->get()
            ->groupBy('grade_id');

        $specialtiesByGrade = Specialty::query()
            ->where(function ($query) use ($schoolCycleId) {
                $query->where('school_cycle_id', $schoolCycleId)
                    ->orWhereNull('school_cycle_id');
            })
            ->orderBy('description')
            ->get()
            ->groupBy('grade_id');

        $assignments = GroupSubjectAssignment::query()
            ->where('school_cycle_id', $schoolCycleId)
            ->where('is_active', true)
            ->whereNotNull('teacher_id')
            ->with(['teacher', 'specialty', 'academicGroup.section'])
            ->get();

        $assignmentMap = [];
        foreach ($assignments as $assignment) {
            $key = $this->cellKey($assignment->academic_group_id, $assignment->specialty_id);
            $assignmentMap[$key] = [
                'id' => $assignment->id,
                'teacher_id' => $assignment->teacher_id,
                'teacher_name' => $assignment->teacher?->display_name,
                'teacher_short_name' => $this->shortTeacherName($assignment->teacher),
                'assignment_type' => $assignment->assignment_type,
            ];
        }

        $gradeBlocks = [];
        $totalCells = 0;
        $filledCells = 0;

        foreach ($grades as $grade) {
            $gradeGroups = ($groups[$grade->id] ?? collect())
                ->sortBy(fn (AcademicGroup $group) => $group->section?->description ?? $group->name ?? '')
                ->values();

            if ($gradeGroups->isEmpty()) {
                continue;
            }

            $gradeSpecialties = ($specialtiesByGrade[$grade->id] ?? collect())
                ->sortBy('description')
                ->values();

            if ($gradeSpecialties->isEmpty()) {
                continue;
            }

            $cells = count($gradeGroups) * count($gradeSpecialties);
            $filled = 0;

            foreach ($gradeGroups as $group) {
                foreach ($gradeSpecialties as $specialty) {
                    if (isset($assignmentMap[$this->cellKey($group->id, $specialty->id)])) {
                        $filled++;
                    }
                }
            }

            $totalCells += $cells;
            $filledCells += $filled;

            $gradeBlocks[] = [
                'grade_id' => $grade->id,
                'grade_label' => $this->gradeLabel($grade->description),
                'groups' => $gradeGroups->map(fn (AcademicGroup $group) => [
                    'id' => $group->id,
                    'name' => $group->name,
                    'group_label' => $group->name ?? trim(
                        ($group->grade?->description ?? '').' '.($group->section?->description ?? '')
                    ),
                    'shift' => $group->shift,
                ])->all(),
                'specialties' => $gradeSpecialties->map(fn (Specialty $specialty) => [
                    'id' => $specialty->id,
                    'description' => $specialty->description,
                    'code' => $specialty->code,
                    'hours_per_week' => max(1, (int) ($specialty->hours_per_week ?? 1)),
                ])->all(),
            ];
        }

        return [
            'school_cycle_id' => $schoolCycleId,
            'grades' => $gradeBlocks,
            'assignments' => $assignmentMap,
            'stats' => [
                'total_cells' => $totalCells,
                'filled_cells' => $filledCells,
                'completion_percent' => $totalCells > 0
                    ? (int) round(($filledCells / $totalCells) * 100)
                    : 0,
            ],
        ];
    }

    public function cellKey(int $groupId, int $specialtyId): string
    {
        return "{$groupId}_{$specialtyId}";
    }

    private function gradeLabel(string $description): string
    {
        $trimmed = trim($description);

        if ($trimmed === '') {
            return 'Grado';
        }

        if (str_contains($trimmed, '°')) {
            return $trimmed;
        }

        return "{$trimmed}°";
    }

    private function shortTeacherName(?\App\Models\Tenants\Teacher $teacher): ?string
    {
        if ($teacher === null) {
            return null;
        }

        $first = trim((string) $teacher->name);

        return $first !== '' ? $first : $teacher->display_name;
    }
}
