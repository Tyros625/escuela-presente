<?php

use App\Models\Tenants\AcademicGroup;
use App\Services\AcademicGroupColorService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        AcademicGroup::query()
            ->with(['grade', 'section'])
            ->orderBy('id')
            ->chunkById(100, function ($groups) {
                foreach ($groups as $group) {
                    $color = AcademicGroupColorService::resolveForGroup($group);
                    if ($color !== null) {
                        $group->updateQuietly(['color' => $color]);
                    }
                }
            });
    }

    public function down(): void
    {
        // Colors remain; no rollback needed.
    }
};
