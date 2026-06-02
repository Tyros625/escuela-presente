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

            /** @var TeachingAssignment $firstAssignment */
            $firstAssignment = $teacherAssignments->sortBy('id')->first();

            $generalSubject = $firstAssignment->specialty?->description
                ?? $teacher->subject?->description
                ?? '—';

            $subjectOrder = (int) ($teacherAssignments
                ->min(fn ($a) => $a->specialty_id) ?? $teacher->subject_id ?? PHP_INT_MAX);

            $rows[] = [
                'teacher_name' => self::teacherReportLabel($teacher),
                'subject_name' => $generalSubject,
                'subject_order' => $subjectOrder,
                'teacher_order' => (int) ($teacherAssignments->min('id') ?? PHP_INT_MAX),
                'latest_assignment_id' => (int) $teacherAssignments->max('id'),
                'cells' => $cells,
            ];
        }

        usort($rows, static function (array $a, array $b): int {
            $bySubject = $a['subject_order'] <=> $b['subject_order'];
            if ($bySubject !== 0) {
                return $bySubject;
            }

            $byLatest = $b['latest_assignment_id'] <=> $a['latest_assignment_id'];
            if ($byLatest !== 0) {
                return $byLatest;
            }

            return $a['teacher_order'] <=> $b['teacher_order'];
        });

        foreach ($rows as $index => &$row) {
            $row['number'] = $index + 1;
            unset($row['subject_order'], $row['teacher_order'], $row['latest_assignment_id']);
        }
        unset($row);

        return $rows;
    }

    private static function teacherReportLabel(\App\Models\Tenants\Teacher $teacher): string
    {
        $father = trim((string) ($teacher->last_name_father ?? $teacher->last_name ?? ''));
        $mother = trim((string) ($teacher->last_name_mother ?? ''));
        $first = trim((string) $teacher->name);

        if ($father !== '' || $mother !== '') {
            $last = trim($father.' '.$mother);

            return $first !== '' ? "{$last}, {$first}" : $last;
        }

        return $first !== '' ? $first : trim((string) $teacher->display_name);
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
