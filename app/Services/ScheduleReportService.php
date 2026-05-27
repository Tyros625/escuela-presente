<?php

namespace App\Services;

use App\Models\Tenants\GeneralConfiguration;
use App\Models\Tenants\TeachingAssignment;
use Illuminate\Support\Collection;

class ScheduleReportService
{
    /**
     * @return array<string, mixed>
     */
    public function build(string $shift = 'morning'): array
    {
        $slots = $shift === 'afternoon'
            ? config('teaching_schedule.evening_slots', [])
            : config('teaching_schedule.morning_slots', []);

        $days = config('teaching_schedule.days', []);
        $dayLabels = array_map(
            static fn (string $day) => strtoupper(self::dayLabelEs($day)),
            $days
        );

        $assignments = TeachingAssignment::query()
            ->where('is_active', true)
            ->where('shift', $shift)
            ->with([
                'teacher.subject',
                'specialty',
                'academicGroup.grade',
                'academicGroup.section',
            ])
            ->get();

        $teachers = $this->buildTeacherRows($assignments, $days, $slots);

        $config = GeneralConfiguration::first();

        return [
            'shift' => $shift,
            'slots' => $slots,
            'days' => $days,
            'day_labels' => $dayLabels,
            'slots_per_day' => count($slots),
            'teachers' => $teachers,
            'school_name' => $config?->name ?? 'Escuela',
            'generated_at' => now()->format('d/m/Y H:i'),
        ];
    }

    /**
     * @param  Collection<int, TeachingAssignment>  $assignments
     * @return array<int, array<string, mixed>>
     */
    private function buildTeacherRows(Collection $assignments, array $days, array $slots): array
    {
        $byTeacher = $assignments->groupBy('teacher_id');
        $rows = [];

        foreach ($byTeacher as $teacherAssignments) {
            /** @var TeachingAssignment $first */
            $first = $teacherAssignments->first();
            $teacher = $first->teacher;

            if ($teacher === null) {
                continue;
            }

            $cells = [];
            foreach ($days as $day) {
                $cells[$day] = [];
                foreach ($slots as $slot) {
                    $cells[$day][$slot] = null;
                }
            }

            foreach ($teacherAssignments as $assignment) {
                $day = $assignment->day_of_week;
                $slot = $assignment->time_slot;

                if (! isset($cells[$day]) || ! array_key_exists($slot, $cells[$day])) {
                    continue;
                }

                $group = $assignment->academicGroup;
                $cells[$day][$slot] = [
                    'text' => TeachingScheduleAbbreviation::cellLabel($group, $assignment->specialty),
                    'background' => $group !== null
                        ? (AcademicGroupColorService::resolveForGroup($group) ?? $group->color ?? '#FFFFFF')
                        : '#FFFFFF',
                ];
            }

            $generalSubject = $teacher->subject?->description
                ?? $teacherAssignments->first()?->specialty?->description
                ?? '—';

            $rows[] = [
                'teacher_name' => $teacher->name,
                'subject_name' => $generalSubject,
                'subject_order' => (int) ($teacher->subject_id ?? PHP_INT_MAX),
                'cells' => $cells,
            ];
        }

        usort($rows, static function (array $a, array $b): int {
            $bySubject = $a['subject_order'] <=> $b['subject_order'];
            if ($bySubject !== 0) {
                return $bySubject;
            }

            return strcasecmp($a['teacher_name'], $b['teacher_name']);
        });

        foreach ($rows as $index => &$row) {
            $row['number'] = $index + 1;
            unset($row['subject_order']);
        }
        unset($row);

        return $rows;
    }

    private static function dayLabelEs(string $day): string
    {
        return match ($day) {
            'lunes' => 'Lunes',
            'martes' => 'Martes',
            'miercoles' => 'Miércoles',
            'jueves' => 'Jueves',
            'viernes' => 'Viernes',
            default => ucfirst($day),
        };
    }
}
