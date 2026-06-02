<?php

namespace App\Services;

use App\Models\Tenants\AcademicGroup;
use App\Models\Tenants\Grade;

class SinGrupoAcademicGroupService
{
    public const GROUP_NAME = 'SIN GRUPO';

    public static function isSinGrupoGroup(?AcademicGroup $group): bool
    {
        if ($group === null) {
            return false;
        }

        $name = strtoupper(trim((string) ($group->name ?? '')));

        return $name === self::GROUP_NAME;
    }

    /**
     * @return array{morning: int, afternoon: int}
     */
    public function ensureForSchoolCycle(int $schoolCycleId): array
    {
        $gradeId = $this->resolveGradeId($schoolCycleId);
        $ids = [];

        foreach (['morning', 'afternoon'] as $shift) {
            $group = AcademicGroup::query()->firstOrCreate(
                [
                    'school_cycle_id' => $schoolCycleId,
                    'shift' => $shift,
                    'name' => self::GROUP_NAME,
                ],
                [
                    'grade_id' => $gradeId,
                    'section_id' => null,
                    'student_limit' => 999,
                    'room_name' => null,
                    'subjects' => [],
                ]
            );

            $ids[$shift] = (int) $group->id;
        }

        return $ids;
    }

    private function resolveGradeId(int $schoolCycleId): int
    {
        $fromGroup = AcademicGroup::query()
            ->where('school_cycle_id', $schoolCycleId)
            ->where('name', '!=', self::GROUP_NAME)
            ->value('grade_id');

        if ($fromGroup !== null) {
            return (int) $fromGroup;
        }

        $gradeId = Grade::query()->orderBy('order')->orderBy('id')->value('id');

        if ($gradeId === null) {
            throw new \RuntimeException('No hay grados registrados para crear el grupo SIN GRUPO.');
        }

        return (int) $gradeId;
    }
}
