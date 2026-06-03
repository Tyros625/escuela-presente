<?php

namespace Tests\Unit;

use App\Models\Tenants\Grade;
use App\Models\Tenants\Specialty;
use App\Services\GroupSubjectAssignmentCatalog;
use PHPUnit\Framework\TestCase;

class GroupSubjectAssignmentCatalogTest extends TestCase
{
    public function test_first_grade_subject_order_has_twelve_entries(): void
    {
        $grade = new Grade(['description' => '1']);

        $this->assertSame(12, GroupSubjectAssignmentCatalog::expectedSubjectCount($grade));
    }

    public function test_second_grade_subject_order_has_eleven_entries(): void
    {
        $grade = new Grade(['description' => '2']);

        $this->assertSame(11, GroupSubjectAssignmentCatalog::expectedSubjectCount($grade));
    }

    public function test_normalize_spanish_aliases(): void
    {
        $specialty = new Specialty(['description' => 'ESPAÑOL', 'code' => null]);

        $this->assertSame('ESPANOL', GroupSubjectAssignmentCatalog::normalizeSubjectKey($specialty));
        $this->assertSame('ESPANOL', GroupSubjectAssignmentCatalog::columnLabel($specialty));
    }

    public function test_column_label_for_physical_education(): void
    {
        $specialty = new Specialty(['description' => 'EDUCACION FISICA', 'code' => null]);

        $this->assertSame('ED_FISICA', GroupSubjectAssignmentCatalog::normalizeSubjectKey($specialty));
        $this->assertSame('ED.FISICA', GroupSubjectAssignmentCatalog::columnLabel($specialty));
    }
}
