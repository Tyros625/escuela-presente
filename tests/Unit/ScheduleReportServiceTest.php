<?php

namespace Tests\Unit;

use App\Models\Tenants\Teacher;
use App\Services\ScheduleReportService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class ScheduleReportServiceTest extends TestCase
{
    public function test_teacher_rows_sort_by_subject_registration_then_name(): void
    {
        $service = new ScheduleReportService;
        $method = new ReflectionMethod(ScheduleReportService::class, 'buildTeacherRows');
        $method->setAccessible(true);

        $mathTeacher = new Teacher(['name' => 'IVAN', 'subject_id' => 6]);
        $mathTeacher->id = 1;
        $mathTeacher->setRelation('subject', (object) ['description' => 'MATEMATICAS']);

        $spanishTeacherA = new Teacher(['name' => 'MARIA', 'subject_id' => 1]);
        $spanishTeacherA->id = 2;
        $spanishTeacherA->setRelation('subject', (object) ['description' => 'ESPAÑOL']);

        $spanishTeacherB = new Teacher(['name' => 'MAGALI', 'subject_id' => 1]);
        $spanishTeacherB->id = 3;
        $spanishTeacherB->setRelation('subject', (object) ['description' => 'ESPAÑOL']);

        $assignments = collect([
            $this->makeAssignment($mathTeacher, 'lunes', '7:30-8:20'),
            $this->makeAssignment($spanishTeacherB, 'martes', '7:30-8:20'),
            $this->makeAssignment($spanishTeacherA, 'lunes', '8:20-9:10'),
        ]);

        $rows = $method->invoke(
            $service,
            $assignments,
            ['lunes', 'martes'],
            ['7:30-8:20', '8:20-9:10']
        );

        $this->assertSame(['MARIA', 'MAGALI', 'IVAN'], array_column($rows, 'teacher_name'));
        $this->assertSame(['ESPAÑOL', 'ESPAÑOL', 'MATEMATICAS'], array_column($rows, 'subject_name'));
    }

    private function makeAssignment(Teacher $teacher, string $day, string $slot): object
    {
        return (object) [
            'teacher_id' => $teacher->id,
            'teacher' => $teacher,
            'day_of_week' => $day,
            'time_slot' => $slot,
            'specialty' => null,
            'academicGroup' => null,
        ];
    }
}
