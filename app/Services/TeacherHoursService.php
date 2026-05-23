<?php

namespace App\Services;

use App\Models\Tenants\Teacher;
use App\Models\Tenants\TeachingAssignment;

class TeacherHoursService
{
    public const HOURS_PER_SLOT = 1;

    public function countAssignedSlots(int $teacherId, ?int $excludeAssignmentId = null): int
    {
        $query = TeachingAssignment::query()
            ->where('is_active', true)
            ->where('teacher_id', $teacherId);

        if ($excludeAssignmentId !== null) {
            $query->where('id', '!=', $excludeAssignmentId);
        }

        return $query->count();
    }

    public function assignedHours(int $teacherId, ?int $excludeAssignmentId = null): float
    {
        return $this->countAssignedSlots($teacherId, $excludeAssignmentId) * self::HOURS_PER_SLOT;
    }

    public function remainingHours(Teacher $teacher, ?int $excludeAssignmentId = null): ?float
    {
        if ($teacher->max_hours_per_week === null) {
            return null;
        }

        $assigned = $this->assignedHours($teacher->id, $excludeAssignmentId);

        return max(0, (float) $teacher->max_hours_per_week - $assigned);
    }

    public function validateCanTakeAssignment(Teacher $teacher, ?int $excludeAssignmentId = null): ?string
    {
        if ($teacher->max_hours_per_week === null) {
            return null;
        }

        $afterAssignment = $this->assignedHours($teacher->id, $excludeAssignmentId) + self::HOURS_PER_SLOT;

        if ($afterAssignment <= (float) $teacher->max_hours_per_week) {
            return null;
        }

        $max = (float) $teacher->max_hours_per_week;
        $current = $this->assignedHours($teacher->id, $excludeAssignmentId);

        return "El docente no tiene horas disponibles (máximo {$max}h, asignadas {$current}h).";
    }
}
