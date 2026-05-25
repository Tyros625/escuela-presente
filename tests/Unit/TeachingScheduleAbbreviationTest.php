<?php

namespace Tests\Unit;

use App\Models\Tenants\AcademicGroup;
use App\Models\Tenants\Grade;
use App\Models\Tenants\Section;
use App\Models\Tenants\Specialty;
use App\Services\TeachingScheduleAbbreviation;
use PHPUnit\Framework\TestCase;

class TeachingScheduleAbbreviationTest extends TestCase
{
    public function test_builds_cell_label_with_code_and_group(): void
    {
        $grade = new Grade(['description' => '3', 'order' => 3]);
        $section = new Section(['description' => 'B']);
        $group = new AcademicGroup(['name' => '3B TERCERO B']);
        $group->setRelation('grade', $grade);
        $group->setRelation('section', $section);

        $specialty = new Specialty(['description' => 'ESPAÑOL', 'code' => 'ESP']);

        $this->assertSame('3B ESP', TeachingScheduleAbbreviation::cellLabel($group, $specialty));
    }

    public function test_subject_key_from_description_alias(): void
    {
        $specialty = new Specialty(['description' => 'SERVICIO', 'code' => null]);

        $this->assertSame('SERV', TeachingScheduleAbbreviation::subjectKey($specialty));
    }
}
