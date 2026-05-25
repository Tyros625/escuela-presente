<?php

use App\Models\Tenants\AcademicGroup;
use App\Services\AcademicGroupColorService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_groups', function (Blueprint $table) {
            $table->string('color', 7)->nullable()->after('name');
        });

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
        Schema::table('academic_groups', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
