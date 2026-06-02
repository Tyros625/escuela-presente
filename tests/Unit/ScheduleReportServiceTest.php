<?php



namespace Tests\Unit;



use App\Models\Tenants\Specialty;
use App\Models\Tenants\Teacher;
use App\Services\ScheduleReportService;

use PHPUnit\Framework\TestCase;

use ReflectionMethod;



class ScheduleReportServiceTest extends TestCase

{

    public function test_teacher_rows_sort_by_subject_registration_then_assignment_order(): void

    {

        $service = new ScheduleReportService;

        $method = new ReflectionMethod(ScheduleReportService::class, 'buildTeacherRows');

        $method->setAccessible(true);



        $mathTeacher = new Teacher(['name' => 'IVAN', 'subject_id' => 6]);

        $mathTeacher->id = 1;



        $spanishTeacherA = new Teacher(['name' => 'MARIA', 'subject_id' => 1]);

        $spanishTeacherA->id = 2;



        $spanishTeacherB = new Teacher(['name' => 'MAGALI', 'subject_id' => 1]);

        $spanishTeacherB->id = 3;



        $assignments = collect([

            $this->makeAssignment($mathTeacher, 5, 6, 'MATEMATICAS', 'lunes', '7:30-8:20'),

            $this->makeAssignment($spanishTeacherB, 20, 1, 'ESPAÑOL', 'martes', '7:30-8:20'),

            $this->makeAssignment($spanishTeacherA, 30, 1, 'ESPAÑOL', 'lunes', '8:20-9:10'),

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



    public function test_math_teacher_sorts_by_assignment_specialty_not_mismatched_teacher_subject(): void

    {

        $service = new ScheduleReportService;

        $method = new ReflectionMethod(ScheduleReportService::class, 'buildTeacherRows');

        $method->setAccessible(true);



        $teacher = new Teacher(['name' => 'ARMANDO', 'subject_id' => 1]);

        $teacher->id = 10;



        $spanish = new Teacher(['name' => 'ELIZABETH', 'subject_id' => 1]);

        $spanish->id = 11;



        $assignments = collect([

            $this->makeAssignment($teacher, 15, 6, 'MATEMATICAS', 'lunes', '7:30-8:20'),

            $this->makeAssignment($spanish, 25, 1, 'ESPAÑOL', 'lunes', '8:20-9:10'),

        ]);



        $rows = $method->invoke($service, $assignments, ['lunes'], ['7:30-8:20', '8:20-9:10']);



        $this->assertSame(['ELIZABETH', 'ARMANDO'], array_column($rows, 'teacher_name'));

        $this->assertSame(['ESPAÑOL', 'MATEMATICAS'], array_column($rows, 'subject_name'));

    }



    private function makeAssignment(

        Teacher $teacher,

        int $id,

        int $specialtyId,

        string $specialtyDescription,

        string $day,

        string $slot

    ): object {

        return (object) [

            'id' => $id,

            'teacher_id' => $teacher->id,

            'specialty_id' => $specialtyId,

            'teacher' => $teacher,

            'day_of_week' => $day,

            'time_slot' => $slot,

            'specialty' => new Specialty([
                'id' => $specialtyId,
                'description' => $specialtyDescription,
            ]),

            'academicGroup' => null,

        ];

    }

}


