<?php

use App\Models\Tenants\SchoolCycle;
use App\Services\SinGrupoAcademicGroupService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $service = app(SinGrupoAcademicGroupService::class);

        SchoolCycle::query()->pluck('id')->each(function (int $schoolCycleId) use ($service) {
            $service->ensureForSchoolCycle($schoolCycleId);
        });
    }

    public function down(): void
    {
        // Intentionally left empty: SIN GRUPO groups may already be referenced.
    }
};
