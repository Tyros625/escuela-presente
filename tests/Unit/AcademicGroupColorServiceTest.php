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
        $this->assertSame('3D', AcademicGroupColorService::groupKey($group));
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

    public function test_name_prefix_wins_over_mismatched_grade_for_color(): void
    {
        $grade = new Grade(['description' => '2', 'order' => 2]);
        $section = new Section(['description' => '1 B']);
        $group = new AcademicGroup(['name' => '1B PRIMERO B']);
        $group->setRelation('grade', $grade);
        $group->setRelation('section', $section);

        $this->assertSame('1B', AcademicGroupColorService::groupKey($group));
        $this->assertSame('#FFE4C4', AcademicGroupColorService::resolveForGroup($group));
    }

    public function test_section_description_parses_full_degree_cluster(): void
    {
        $grade = new Grade(['description' => 'TERCERO', 'order' => 3]);
        $section = new Section(['description' => '1 C']);
        $group = new AcademicGroup(['name' => 'PRIMERO C']);
        $group->setRelation('grade', $grade);
        $group->setRelation('section', $section);

        $this->assertSame('1C', AcademicGroupColorService::groupKey($group));
        $this->assertSame('#8A2BE2', AcademicGroupColorService::resolveForGroup($group));
    }

    public function test_one_c_and_three_c_resolve_to_different_colors(): void
    {
        $gradeOne = new Grade(['description' => 'PRIMERO', 'order' => 1]);
        $gradeThree = new Grade(['description' => 'TERCERO', 'order' => 3]);

        $groupOneC = new AcademicGroup(['name' => '1C PRIMERO C']);
        $groupOneC->setRelation('grade', $gradeOne);
        $groupOneC->setRelation('section', new Section(['description' => '1 C']));

        $groupThreeC = new AcademicGroup(['name' => '3C TERCERO C']);
        $groupThreeC->setRelation('grade', $gradeThree);
        $groupThreeC->setRelation('section', new Section(['description' => '3 C']));

        $this->assertSame('#8A2BE2', AcademicGroupColorService::resolveForGroup($groupOneC));
        $this->assertSame('#6495ED', AcademicGroupColorService::resolveForGroup($groupThreeC));
        $this->assertNotSame(
            AcademicGroupColorService::resolveForGroup($groupOneC),
            AcademicGroupColorService::resolveForGroup($groupThreeC)
        );
    }

    public function test_consistency_error_when_name_conflicts_with_grade_section(): void
    {
        $grade = new Grade(['description' => 'TERCERO', 'order' => 3]);
        $section = new Section(['description' => '3 C']);

        $error = AcademicGroupColorService::consistencyError('1C PRIMERO C', $grade, $section);

        $this->assertNotNull($error);
    }

    public function test_parse_degree_cluster_from_section_with_space(): void
    {
        $parsed = AcademicGroupColorService::parseDegreeClusterFromText('3 C');

        $this->assertSame([3, 'C'], $parsed);
    }
}
