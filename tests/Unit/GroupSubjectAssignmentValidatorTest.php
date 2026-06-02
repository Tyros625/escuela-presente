<?php

namespace Tests\Unit;

use App\Models\Tenants\Specialty;
use App\Models\Tenants\Teacher;
use App\Services\GroupSubjectAssignmentMatrixService;
use App\Services\GroupSubjectAssignmentValidator;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class GroupSubjectAssignmentValidatorTest extends TestCase
{
    public function test_validate_teacher_subject_match_returns_null_when_teacher_has_no_primary_subject(): void
    {
        $validator = new GroupSubjectAssignmentValidator;
        $teacher = new Teacher(['name' => 'MARIA', 'subject_id' => null]);
        $specialty = new Specialty(['description' => 'FISICA']);

        $this->assertNull($validator->validateTeacherSubjectMatch($teacher, $specialty));
    }

    public function test_validate_teacher_subject_match_returns_null_when_subjects_match(): void
    {
        $validator = new GroupSubjectAssignmentValidator;
        $teacher = new Teacher(['name' => 'MARIA', 'subject_id' => 4]);
        $specialty = new Specialty(['description' => 'FISICA']);
        $specialty->id = 4;

        $this->assertNull($validator->validateTeacherSubjectMatch($teacher, $specialty));
    }

    public function test_validate_teacher_subject_match_warns_on_mismatch(): void
    {
        $validator = new GroupSubjectAssignmentValidator;

        $teacher = new Teacher(['name' => 'MARIA', 'subject_id' => 1]);
        $teacher->setRelation('subject', new Specialty(['description' => 'ESPAÑOL']));

        $specialty = new Specialty(['description' => 'FISICA']);
        $specialty->id = 9;

        $warning = $validator->validateTeacherSubjectMatch($teacher, $specialty);

        $this->assertNotNull($warning);
        $this->assertStringContainsString('FISICA', $warning);
    }
}

class GroupSubjectAssignmentMatrixServiceTest extends TestCase
{
    public function test_cell_key_format(): void
    {
        $service = new GroupSubjectAssignmentMatrixService;

        $this->assertSame('12_34', $service->cellKey(12, 34));
    }

    public function test_grade_label_adds_degree_symbol(): void
    {
        $service = new GroupSubjectAssignmentMatrixService;
        $method = new ReflectionMethod(GroupSubjectAssignmentMatrixService::class, 'gradeLabel');
        $method->setAccessible(true);

        $this->assertSame('1°', $method->invoke($service, '1'));
        $this->assertSame('2°', $method->invoke($service, '2°'));
    }
}
