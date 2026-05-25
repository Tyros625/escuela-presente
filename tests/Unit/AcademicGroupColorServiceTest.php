<?php

namespace Tests\Unit;

use App\Models\Tenants\AcademicGroup;
use App\Models\Tenants\Grade;
use App\Models\Tenants\Section;
use App\Services\AcademicGroupColorService;
use PHPUnit\Framework\TestCase;

class AcademicGroupColorServiceTest extends TestCase
{
    public function test_resolves_color_for_degree_three_cluster_d(): void
    {
        $grade = new Grade(['description' => '3', 'order' => 3]);
        $section = new Section(['description' => 'D']);
        $group = new AcademicGroup([
            'name' => '3D TERCERO D',
            'grade_id' => 3,
            'section_id' => 4,
        ]);
        $group->setRelation('grade', $grade);
        $group->setRelation('section', $section);

        $this->assertSame('#87CEFA', AcademicGroupColorService::resolveForGroup($group));
    }

    public function test_resolves_cluster_from_group_name_when_section_missing(): void
    {
        $grade = new Grade(['description' => '1', 'order' => 1]);
        $group = new AcademicGroup(['name' => '1°A', 'grade_id' => 1]);
        $group->setRelation('grade', $grade);
        $group->setRelation('section', null);

        $this->assertSame('#00FFFF', AcademicGroupColorService::resolveForGroup($group));
    }

    public function test_returns_null_for_unmapped_degree(): void
    {
        $grade = new Grade(['description' => '6', 'order' => 6]);
        $section = new Section(['description' => 'A']);
        $group = new AcademicGroup(['grade_id' => 6, 'section_id' => 1]);
        $group->setRelation('grade', $grade);
        $group->setRelation('section', $section);

        $this->assertNull(AcademicGroupColorService::resolveForGroup($group));
    }
}
