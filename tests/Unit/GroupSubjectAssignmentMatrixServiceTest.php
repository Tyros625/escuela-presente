<?php

namespace Tests\Unit;

use App\Services\GroupSubjectAssignmentMatrixService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class GroupSubjectAssignmentMatrixServiceTest extends TestCase
{
    public function test_cell_key_format(): void
    {
        $service = new GroupSubjectAssignmentMatrixService;

        $this->assertSame('12:34', $service->cellKey(12, 34));
    }

    public function test_grade_label_adds_degree_symbol(): void
    {
        $service = new GroupSubjectAssignmentMatrixService;
        $method = new ReflectionMethod(GroupSubjectAssignmentMatrixService::class, 'gradeLabel');
        $method->setAccessible(true);

        $this->assertSame('1°', $method->invoke($service, '1'));
        $this->assertSame('2°', $method->invoke($service, '2°'));
    }

    public function test_parse_cell_key_supports_colon_and_legacy_underscore(): void
    {
        $service = new GroupSubjectAssignmentMatrixService;

        $this->assertSame([12, 34], $service->parseCellKey('12:34'));
        $this->assertSame([12, 34], $service->parseCellKey('12_34'));
        $this->assertNull($service->parseCellKey('invalid'));
    }
}
