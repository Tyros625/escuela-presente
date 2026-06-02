<?php

namespace Tests\Unit;

use App\Models\Tenants\AcademicGroup;
use App\Models\Tenants\Specialty;
use App\Services\SinGrupoAcademicGroupService;
use App\Services\TeachingScheduleRules;
use PHPUnit\Framework\TestCase;

class TeachingScheduleRulesTest extends TestCase
{
    public function test_detects_servicio_by_description(): void
    {
        $specialty = new Specialty(['description' => 'SERVICIO', 'code' => null]);

        $this->assertTrue(TeachingScheduleRules::isServicioSpecialty($specialty));
    }

    public function test_detects_servicio_by_code(): void
    {
        $specialty = new Specialty(['description' => 'Otro', 'code' => 'SERV']);

        $this->assertTrue(TeachingScheduleRules::isServicioSpecialty($specialty));
    }

    public function test_regular_subject_is_not_servicio(): void
    {
        $specialty = new Specialty(['description' => 'ESPAÑOL', 'code' => 'ESP']);

        $this->assertFalse(TeachingScheduleRules::isServicioSpecialty($specialty));
    }

    public function test_detects_sin_grupo_group(): void
    {
        $group = new AcademicGroup(['name' => 'SIN GRUPO']);

        $this->assertTrue(SinGrupoAcademicGroupService::isSinGrupoGroup($group));
    }
}
