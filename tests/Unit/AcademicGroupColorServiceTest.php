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

        $group = new AcademicGroup(['name' => '3D TERCERO D']);

        $group->setRelation('grade', $grade);

        $group->setRelation('section', $section);



        $this->assertSame('#87CEFA', AcademicGroupColorService::resolveForGroup($group));

    }



    public function test_name_prefix_wins_over_mismatched_grade_for_color(): void

    {

        $grade = new Grade(['description' => '2', 'order' => 2]);

        $section = new Section(['description' => '1 B']);

        $group = new AcademicGroup(['name' => '1B PRIMERO B']);

        $group->setRelation('grade', $grade);

        $group->setRelation('section', $section);



        $this->assertSame('#FFE4C4', AcademicGroupColorService::resolveForGroup($group));

    }



    public function test_one_c_and_three_c_resolve_to_different_colors(): void

    {

        $groupOneC = new AcademicGroup(['name' => '1C PRIMERO C']);

        $groupOneC->setRelation('grade', new Grade(['order' => 1]));

        $groupOneC->setRelation('section', new Section(['description' => '1 C']));



        $groupThreeC = new AcademicGroup(['name' => '3C TERCERO C']);

        $groupThreeC->setRelation('grade', new Grade(['order' => 3]));

        $groupThreeC->setRelation('section', new Section(['description' => '3 C']));



        $this->assertSame('#8A2BE2', AcademicGroupColorService::resolveForGroup($groupOneC));

        $this->assertSame('#6495ED', AcademicGroupColorService::resolveForGroup($groupThreeC));

    }



    public function test_consistency_error_when_name_conflicts_with_explicit_section_code(): void

    {

        $error = AcademicGroupColorService::consistencyError(

            '1C PRIMERO C',

            new Grade(['order' => 3]),

            new Section(['description' => '3 C'])

        );



        $this->assertNotNull($error);

    }



    public function test_no_consistency_error_when_section_label_is_not_a_numeric_code(): void

    {

        $error = AcademicGroupColorService::consistencyError(

            '1C PRIMERO C',

            new Grade(['order' => 3]),

            new Section(['description' => 'PRIMERO C'])

        );



        $this->assertNull($error);

    }

}


