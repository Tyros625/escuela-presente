<?php

namespace App\Services;

use App\Models\Tenants\GroupSubjectAssignment;
use App\Models\Tenants\Specialty;
use App\Models\Tenants\Teacher;

class GroupSubjectAssignmentValidator
{
    public function assignedWeeklyHours(int $teacherId, ?int $excludeAssignmentId = null): float
    {
        $query = GroupSubjectAssignment::query()
            ->where('is_active', true)
            ->where('teacher_id', $teacherId)
            ->with('specialty');

        if ($excludeAssignmentId !== null) {
            $query->where('id', '!=', $excludeAssignmentId);
        }

        return $query->get()->sum(function (GroupSubjectAssignment $assignment) {
            return max(1, (int) ($assignment->specialty?->hours_per_week ?? 1));
        });
    }

    public function validateTeacherHours(
        Teacher $teacher,
        Specialty $specialty,
        ?int $excludeAssignmentId = null
    ): ?string {
        if ($teacher->max_hours_per_week === null) {
            return null;
        }

        $hoursToAdd = max(1, (int) ($specialty->hours_per_week ?? 1));
        $afterAssignment = $this->assignedWeeklyHours($teacher->id, $excludeAssignmentId) + $hoursToAdd;

        if ($afterAssignment <= (float) $teacher->max_hours_per_week) {
            return null;
        }

        $max = (float) $teacher->max_hours_per_week;
        $current = $this->assignedWeeklyHours($teacher->id, $excludeAssignmentId);

        return "El docente no tiene horas disponibles (máximo {$max}h, asignadas {$current}h).";
    }

    public function validateTeacherSubjectMatch(Teacher $teacher, Specialty $specialty): ?string
    {
        if ($teacher->subject_id === null) {
            return null;
        }

        if ((int) $teacher->subject_id === (int) $specialty->id) {
            return null;
        }

        $teacherSubject = $teacher->subject?->description ?? 'otra materia';

        return "El docente está registrado principalmente en {$teacherSubject}; confirme que puede impartir {$specialty->description}.";
    }
}
